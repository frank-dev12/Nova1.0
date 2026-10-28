(() => {
    const base = document.body?.dataset.base || '';
    const loader = document.querySelector('[data-loader]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (loader) {
        const skip = reduceMotion || sessionStorage.getItem('nova.entered') === '1';
        if (skip) {
            loader.remove();
            sessionStorage.setItem('nova.entered', '1');
        } else {
            const started = performance.now();
            let closed = false;
            const closeLoader = () => {
                if (closed) return;
                closed = true;
                loader.classList.add('is-done');
                sessionStorage.setItem('nova.entered', '1');
                window.setTimeout(() => loader.remove(), 460);
            };
            const arm = () => {
                const wait = Math.max(0, 900 - (performance.now() - started));
                window.setTimeout(closeLoader, wait);
            };
            if (document.readyState === 'complete') arm();
            else window.addEventListener('load', arm);
            window.setTimeout(closeLoader, 2600);
        }
    }

    document.querySelectorAll('[data-logo]').forEach((logo) => {
        let clicks = 0;
        let timer = 0;
        logo.addEventListener('click', (event) => {
            event.preventDefault();
            clicks += 1;
            logo.classList.remove('is-tapped');
            void logo.offsetWidth;
            logo.classList.add('is-tapped');
            window.clearTimeout(timer);
            if (clicks >= 4) {
                window.location.href = document.body.dataset.admin || `${base}/admin/login.php`;
                return;
            }
            timer = window.setTimeout(() => {
                if (clicks === 1) window.location.href = logo.getAttribute('href');
                clicks = 0;
            }, 550);
        });
    });
    const toast = document.querySelector('[data-toast]');

    function showToast(message) {
        if (!toast) return;
        toast.textContent = message;
        toast.classList.remove('hidden');
        window.clearTimeout(showToast.timer);
        showToast.timer = window.setTimeout(() => toast.classList.add('hidden'), 2800);
    }

    function setCartCount(count) {
        document.querySelectorAll('[data-cart-count]').forEach((node) => {
            node.textContent = String(count);
        });
    }

    const menuButton = document.querySelector('[data-menu-toggle]');
    const menu = document.querySelector('[data-mobile-menu]');
    const iconOpen = document.querySelector('[data-menu-icon-open]');
    const iconClose = document.querySelector('[data-menu-icon-close]');
    if (menuButton && menu) {
        menuButton.addEventListener('click', () => {
            const open = menu.classList.toggle('hidden') === false;
            menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
            iconOpen?.classList.toggle('hidden', open);
            iconClose?.classList.toggle('hidden', !open);
        });
    }

    const carousel = document.querySelector('[data-carousel]');
    if (carousel) {
        const slides = [...carousel.querySelectorAll('[data-slide]')];
        let current = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        let timer = 0;

        function show(index) {
            if (slides.length === 0) return;
            current = (index + slides.length) % slides.length;
            slides.forEach((slide, slideIndex) => {
                const active = slideIndex === current;
                slide.classList.toggle('is-active', active);
                if (active) slide.removeAttribute('inert');
                else slide.setAttribute('inert', '');
            });
        }

        function stop() {
            window.clearInterval(timer);
        }

        function start() {
            if (reduce || slides.length < 2) return;
            stop();
            timer = window.setInterval(() => show(current + 1), 7000);
        }

        carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
            show(current - 1);
            start();
        });
        carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
            show(current + 1);
            start();
        });
        carousel.addEventListener('mouseenter', stop);
        carousel.addEventListener('mouseleave', start);
        carousel.addEventListener('focusin', stop);
        carousel.addEventListener('focusout', start);
        carousel.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowRight') show(current + 1);
            if (event.key === 'ArrowLeft') show(current - 1);
        });
        start();
    }

    const wishlistKey = 'nova.wishlist';
    function readWishlist() {
        try {
            const parsed = JSON.parse(localStorage.getItem(wishlistKey) || '[]');
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    }
    function paintHearts() {
        const saved = new Set(readWishlist());
        document.querySelectorAll('[data-wishlist]').forEach((button) => {
            const on = saved.has(button.dataset.wishlist);
            button.classList.toggle('is-saved', on);
            button.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
    }
    paintHearts();
    document.addEventListener('click', (event) => {
        const button = event.target.closest('[data-wishlist]');
        if (!button) return;
        const id = button.dataset.wishlist;
        const saved = readWishlist().filter((item) => item !== id);
        if (!button.classList.contains('is-saved')) saved.push(id);
        localStorage.setItem(wishlistKey, JSON.stringify(saved));
        paintHearts();
    });

    document.addEventListener('click', (event) => {
        const step = event.target.closest('[data-qty-step]');
        if (!step) return;
        const form = step.closest('form');
        const input = form?.querySelector('[data-qty]');
        if (!form || !input) return;
        const next = Math.min(8, Math.max(0, Number(input.value || 0) + Number(step.dataset.qtyStep || 0)));
        input.value = String(next);
        form.requestSubmit();
    });

    document.addEventListener('submit', async (event) => {
        const form = event.target.closest?.('[data-cart-form]') || null;
        if (!form || form.hasAttribute('data-reload')) return;
        event.preventDefault();
        const button = form.querySelector('[type="submit"]');
        button?.setAttribute('disabled', 'disabled');
        try {
            const response = await fetch(`${base}/api/cart.php`, {
                method: 'POST',
                body: new FormData(form),
                headers: { Accept: 'application/json' },
            });
            const data = await response.json();
            if (typeof data.count === 'number') setCartCount(data.count);
            showToast(data.message || (data.ok ? 'Listo.' : 'No se pudo actualizar el carrito.'));
        } catch {
            showToast('No se pudo conectar. Inténtalo otra vez.');
        } finally {
            button?.removeAttribute('disabled');
        }
    });

    let searchTimer = 0;
    function closeSuggests(except) {
        document.querySelectorAll('[data-suggest]').forEach((box) => {
            if (box !== except) box.classList.add('hidden');
        });
    }
    async function runSearch(input) {
        const box = input.parentElement?.querySelector('[data-suggest]');
        if (!box) return;
        const query = input.value.trim();
        if (query.length < 2) {
            box.classList.add('hidden');
            box.replaceChildren();
            return;
        }
        const response = await fetch(`${base}/api/search.php?q=${encodeURIComponent(query)}`, {
            headers: { Accept: 'application/json' },
        });
        const data = await response.json();
        box.replaceChildren();
        const items = Array.isArray(data.items) ? data.items : [];
        if (items.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'px-3 py-3 text-sm text-slate-500';
            empty.textContent = 'Sin resultados';
            box.append(empty);
        } else {
            items.forEach((item) => {
                const link = document.createElement('a');
                link.href = item.url;
                link.className = 'flex items-center gap-3 rounded-xl px-2 py-2 hover:bg-slate-50';
                const image = document.createElement('img');
                image.src = item.image;
                image.alt = '';
                image.className = 'h-12 w-12 rounded-lg object-cover bg-[#f3f1ee]';
                const name = document.createElement('span');
                name.className = 'min-w-0 flex-1 truncate text-sm font-medium';
                name.textContent = item.name;
                const price = document.createElement('span');
                price.className = 'text-sm font-semibold';
                price.textContent = item.price;
                link.append(image, name, price);
                box.append(link);
            });
        }
        box.classList.remove('hidden');
    }
    document.querySelectorAll('[data-search]').forEach((input) => {
        input.addEventListener('input', () => {
            window.clearTimeout(searchTimer);
            searchTimer = window.setTimeout(() => {
                runSearch(input).catch(() => {});
            }, 180);
        });
        input.addEventListener('focus', () => {
            closeSuggests(input.parentElement?.querySelector('[data-suggest]'));
            if (input.value.trim().length >= 2) runSearch(input).catch(() => {});
        });
    });
    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-search]') && !event.target.closest('[data-suggest]')) {
            closeSuggests(null);
        }
    });
})();
