(() => {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const minimum = 520;
    const started = window.novaSkeletonStart || performance.now();

    function hide(node) {
        if (!node || node.classList.contains('is-done')) return;
        node.classList.add('is-done');
    }

    function schedule(node) {
        if (!node) return;
        if (reduceMotion) {
            hide(node);
            return;
        }
        let closed = false;
        const close = () => {
            if (closed) return;
            closed = true;
            const wait = Math.max(0, minimum - (performance.now() - started));
            window.setTimeout(() => hide(node), wait);
        };
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', close, { once: true });
        else close();
        window.setTimeout(close, 2600);
    }

    function shell() {
        return document.querySelector('[data-loader], [data-admin-skeleton]');
    }

    function showShell() {
        const node = shell();
        if (!node) return;
        node.style.transition = 'none';
        node.classList.remove('is-done');
        void node.offsetWidth;
        node.style.transition = '';
    }

    function armImages(root) {
        root.querySelectorAll('img').forEach((img) => {
            if (img.closest('[data-loader], [data-admin-skeleton], .nova-img-frame')) return;
            if (!img.getAttribute('src') || img.dataset.novaImg === '1') return;
            img.dataset.novaImg = '1';
            if (img.complete && img.naturalWidth > 0) return;
            const frame = document.createElement('span');
            const positioned = getComputedStyle(img).position === 'absolute';
            const wide = img.classList.contains('w-full');
            frame.className = 'nova-img-frame' + (positioned ? ' is-fill' : wide ? ' is-wide' : '');
            img.parentNode.insertBefore(frame, img);
            frame.appendChild(img);
            const veil = document.createElement('span');
            veil.className = 'nova-img-veil';
            veil.setAttribute('aria-hidden', 'true');
            frame.appendChild(veil);
            const done = () => veil.remove();
            img.addEventListener('load', done, { once: true });
            img.addEventListener('error', done, { once: true });
        });
    }

    schedule(shell());
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => armImages(document), { once: true });
    } else {
        armImages(document);
    }

    function internalUrl(value) {
        try {
            return new URL(value, window.location.href);
        } catch (error) {
            return null;
        }
    }

    document.addEventListener('click', (event) => {
        if (event.defaultPrevented || event.button !== 0) return;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        const link = event.target.closest('a[href]');
        if (!link || link.target === '_blank' || link.hasAttribute('download')) return;
        const url = internalUrl(link.href);
        if (!url || url.origin !== window.location.origin) return;
        if (url.pathname === window.location.pathname && url.search === window.location.search) return;
        showShell();
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.target === '_blank') return;
        const url = internalUrl(form.action || window.location.href);
        if (!url || url.origin !== window.location.origin) return;
        showShell();
    });
})();
