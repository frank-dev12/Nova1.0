(() => {
    const content = document.querySelector('[data-admin-content]');
    if (!content || window.novaAdminNav) return;
    window.novaAdminNav = true;

    const cache = new Map();
    let request = 0;
    let controller = null;
    const activeClass = 'flex items-center gap-3 rounded-full border border-[#c4a46a]/45 bg-white/10 px-3 py-2.5 text-sm font-medium text-white';
    const idleClass = 'flex items-center gap-3 rounded-full border border-[#c4a46a]/25 px-3 py-2.5 text-sm text-white/75 hover:border-[#c4a46a]/50 hover:bg-white/5 hover:text-white';

    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';

    function cacheKey(url) {
        return url.pathname + url.search;
    }

    function panelUrl(value) {
        let url;
        try {
            url = value instanceof URL ? value : new URL(value, window.location.href);
        } catch (error) {
            return null;
        }
        if (url.origin !== window.location.origin) return null;
        const base = document.body?.dataset.base || '';
        const root = `${base}/admin/`.replace(/\/{2,}/g, '/');
        if (!url.pathname.startsWith(root)) return null;
        if (url.pathname.endsWith('/login.php') || url.pathname.endsWith('/logout.php')) return null;
        return url;
    }

    function stripFlashes(html) {
        const holder = document.createElement('template');
        holder.innerHTML = html;
        holder.content.querySelectorAll('[data-flash]').forEach((node) => node.remove());
        return holder.innerHTML;
    }

    function readDocument(html) {
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const next = doc.querySelector('[data-admin-content]');
        if (!next || !doc.body?.dataset.adminSection) return null;
        return {
            html: next.innerHTML,
            title: doc.title,
            section: doc.body.dataset.adminSection || '',
            label: doc.body.dataset.adminLabel || '',
            badges: readBadges(doc),
        };
    }

    function readBadges(doc) {
        const visible = (node) => (node && !node.classList.contains('hidden') ? node.textContent.trim() : '');
        return {
            orders: visible(doc.querySelector('[data-order-badge]')),
            messages: visible(doc.querySelector('[data-message-badge]')),
        };
    }

    function applyBadges(badges) {
        if (!badges) return;
        const order = document.querySelector('[data-order-badge]');
        if (order) {
            order.textContent = badges.orders || '0';
            order.classList.toggle('hidden', badges.orders === '');
        }
        const message = document.querySelector('[data-message-badge]');
        if (message) {
            message.textContent = badges.messages || '0';
            message.classList.toggle('hidden', badges.messages === '');
        }
    }

    function closeMenu() {
        document.querySelector('[data-admin-menu]')?.classList.remove('is-open');
        document.querySelector('[data-admin-backdrop]')?.classList.remove('is-open');
        const toggle = document.querySelector('[data-admin-menu-toggle]');
        toggle?.setAttribute('aria-expanded', 'false');
        document.querySelector('[data-admin-icon-open]')?.classList.remove('hidden');
        document.querySelector('[data-admin-icon-close]')?.classList.add('hidden');
        document.documentElement.classList.remove('admin-menu-open');
    }

    function setActive(section) {
        document.querySelectorAll('[data-admin-link]').forEach((link) => {
            const on = link.dataset.adminLink === section;
            link.className = on ? activeClass : idleClass;
            if (on) link.setAttribute('aria-current', 'page');
            else link.removeAttribute('aria-current');
            const chevron = link.querySelector('[data-admin-chevron]');
            if (chevron) chevron.className = on ? 'inline-flex' : 'hidden';
        });
    }

    function paint(entry, url, options) {
        content.innerHTML = entry.html;
        content.removeAttribute('aria-busy');
        document.title = entry.title;
        document.body.dataset.adminSection = entry.section;
        document.body.dataset.adminLabel = entry.label;
        const bar = document.querySelector('[data-admin-bar-title]');
        if (bar) bar.textContent = entry.label;
        setActive(entry.section);
        applyBadges(entry.badges);
        closeMenu();
        const next = url.pathname + url.search + url.hash;
        if (options.push) history.pushState({ novaPanel: 1 }, '', next);
        else if (options.replace) history.replaceState({ novaPanel: 1 }, '', next);
        if (!options.quiet) window.scrollTo(0, 0);
    }

    function showSkeleton() {
        content.setAttribute('aria-busy', 'true');
        content.innerHTML = '<div data-admin-pending role="status" aria-live="polite" aria-label="Cargando sección" class="grid gap-4"><div class="nova-skeleton-block h-28 rounded-[28px]"></div><div class="grid grid-cols-1 gap-3 sm:grid-cols-3"><div class="nova-skeleton-block h-24 rounded-[24px]"></div><div class="nova-skeleton-block h-24 rounded-[24px]"></div><div class="nova-skeleton-block h-24 rounded-[24px]"></div></div><div class="nova-skeleton-block h-48 rounded-[24px]"></div></div>';
    }

    function showError(url, message) {
        content.removeAttribute('aria-busy');
        document.body.dataset.adminNav = 'error';
        content.replaceChildren();
        const section = document.createElement('section');
        section.className = 'rounded-[28px] bg-white p-6';
        const title = document.createElement('h1');
        title.className = 'text-xl font-black';
        title.textContent = 'No se pudo cargar';
        const copy = document.createElement('p');
        copy.className = 'mt-2 text-sm text-slate-500';
        copy.textContent = message || 'No se pudo abrir esta sección.';
        const retry = document.createElement('button');
        retry.type = 'button';
        retry.dataset.adminRetry = '';
        retry.dataset.url = url.href;
        retry.className = 'mt-4 inline-flex rounded-full bg-ink px-4 py-2.5 text-sm font-semibold text-white';
        retry.textContent = 'Reintentar';
        section.append(title, copy, retry);
        content.append(section);
    }

    function store(url, entry) {
        cache.set(cacheKey(url), {
            html: stripFlashes(entry.html),
            title: entry.title,
            section: entry.section,
            label: entry.label,
        });
    }

    async function load(input, options = {}) {
        const url = panelUrl(input);
        if (!url) {
            window.location.assign(String(input));
            return;
        }
        const key = cacheKey(url);
        const here = cacheKey(new URL(window.location.href));
        if (options.push && !options.fresh && key === here) {
            closeMenu();
            return;
        }
        const cached = options.fresh ? null : cache.get(key);
        if (cached) {
            controller?.abort();
            controller = null;
            document.body.dataset.adminNav = 'hit';
            paint(cached, url, options);
            return;
        }
        document.body.dataset.adminNav = 'miss';
        if (!options.quiet) showSkeleton();
        controller?.abort();
        const own = new AbortController();
        controller = own;
        const token = ++request;
        try {
            const response = await fetch(url.href, {
                headers: { Accept: 'text/html' },
                credentials: 'same-origin',
                cache: 'no-store',
                signal: own.signal,
            });
            if (token !== request) return;
            const finalUrl = new URL(response.url, window.location.href);
            if (!panelUrl(finalUrl)) {
                window.location.assign(finalUrl.href);
                return;
            }
            if (!response.ok) {
                showError(url, response.status === 419 ? 'La sesión expiró. Recarga el panel e inténtalo otra vez.' : '');
                return;
            }
            const page = readDocument(await response.text());
            if (!page) {
                window.location.assign(finalUrl.href);
                return;
            }
            store(finalUrl, page);
            paint(page, finalUrl, options.push ? { push: true } : options);
        } catch (error) {
            if (error.name === 'AbortError' || token !== request) return;
            showError(url);
        }
    }

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0) return;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        const retry = event.target.closest('[data-admin-retry]');
        if (retry) {
            event.preventDefault();
            load(retry.dataset.url || window.location.href, { fresh: true });
            return;
        }
        const link = event.target.closest('a[href]');
        if (!link || link.target === '_blank' || link.hasAttribute('download')) return;
        if (!panelUrl(link.href)) return;
        event.preventDefault();
        load(link.href, { push: true });
    }, true);

    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !content.contains(form) || event.defaultPrevented) return;
        event.preventDefault();
        const method = (form.getAttribute('method') || 'get').toLowerCase();
        if (method === 'get') {
            const url = new URL(form.action, window.location.href);
            new FormData(form).forEach((value, key) => {
                if (typeof value === 'string') url.searchParams.set(key, value);
            });
            load(panelUrl(url) ? url : form.action, { push: true, fresh: true });
            return;
        }
        controller?.abort();
        const own = new AbortController();
        controller = own;
        const token = ++request;
        showSkeleton();
        document.body.dataset.adminNav = 'submit';
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                cache: 'no-store',
                headers: { Accept: 'text/html' },
                signal: own.signal,
            });
            if (token !== request) return;
            const finalUrl = new URL(response.url, window.location.href);
            if (!panelUrl(finalUrl)) {
                window.location.assign(finalUrl.href);
                return;
            }
            if (!response.ok) {
                showError(finalUrl, response.status === 419 ? 'La sesión expiró. Recarga el panel e inténtalo otra vez.' : '');
                return;
            }
            const page = readDocument(await response.text());
            if (!page) {
                window.location.assign(finalUrl.href);
                return;
            }
            cache.clear();
            store(finalUrl, page);
            const same = cacheKey(finalUrl) === cacheKey(new URL(window.location.href));
            paint(page, finalUrl, { push: !same, replace: same });
        } catch (error) {
            if (error.name === 'AbortError' || token !== request) return;
            showError(new URL(form.action, window.location.href));
        }
    });

    window.addEventListener('popstate', () => {
        if (!panelUrl(window.location.href)) return;
        load(window.location.href, { fresh: false });
    });

    async function refreshBadges() {
        try {
            const response = await fetch(window.location.href, {
                headers: { Accept: 'text/html' },
                credentials: 'same-origin',
                cache: 'no-store',
            });
            if (!response.ok) return;
            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (!doc.body?.dataset.adminSection) return;
            applyBadges(readBadges(doc));
        } catch (error) {}
    }

    document.addEventListener('nova:orders-changed', () => {
        [...cache.keys()].forEach((key) => {
            if (key.includes('/admin/index.php') || key.includes('/admin/pedidos.php')) cache.delete(key);
        });
        const here = window.location.pathname;
        const onLists = here.endsWith('/admin/index.php') || here.endsWith('/admin/pedidos.php');
        const field = document.activeElement;
        const editing = field && content.contains(field) && field.matches('input, textarea, select');
        if (onLists && !editing) {
            load(window.location.href, { fresh: true, quiet: true });
            return;
        }
        refreshBadges();
    });

    store(new URL(window.location.href), {
        html: content.innerHTML,
        title: document.title,
        section: document.body.dataset.adminSection || '',
        label: document.body.dataset.adminLabel || '',
    });
})();
