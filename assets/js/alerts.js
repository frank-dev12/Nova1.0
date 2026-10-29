(() => {
    const gate = document.querySelector('[data-alert-gate]');
    const allowButton = document.querySelector('[data-alert-allow]');
    const denyButton = document.querySelector('[data-alert-deny]');
    const status = document.querySelector('[data-alert-status]');
    let chime = null;
    let watching = false;

    function choice() {
        const match = document.cookie.match(/(?:^|; )nova_alerts=(granted|denied)/);
        return match ? match[1] : '';
    }

    function writeChoice(value) {
        document.cookie = `nova_alerts=${value}; Path=/; Max-Age=31536000; SameSite=Lax`;
    }

    function permission() {
        return ('Notification' in window) ? Notification.permission : 'unsupported';
    }

    function alertsEnabled() {
        return choice() === 'granted' && permission() === 'granted';
    }

    function hideGate() {
        document.documentElement.classList.remove('nova-alerts-ask');
        document.documentElement.classList.add('nova-alerts-set');
    }

    function showGate() {
        document.documentElement.classList.remove('nova-alerts-set');
        document.documentElement.classList.add('nova-alerts-ask');
        allowButton?.focus();
    }

    function setStatus(message) {
        if (!status) return;
        status.textContent = message;
        status.classList.remove('hidden');
    }

    function syncBanners() {
        const enabled = alertsEnabled();
        const admin = Boolean(document.body?.dataset.adminAlerts);
        const settled = document.documentElement.classList.contains('nova-alerts-set');
        document.querySelectorAll('[data-alert-on]').forEach((node) => {
            node.classList.toggle('hidden', !(enabled && admin));
        });
        document.querySelectorAll('[data-alert-off]').forEach((node) => {
            node.classList.toggle('hidden', !(admin && settled && !enabled));
        });
    }

    function chimeUrl() {
        const sampleRate = 22050;
        const length = Math.floor(sampleRate * 0.55);
        const buffer = new ArrayBuffer(44 + length * 2);
        const view = new DataView(buffer);
        const write = (offset, text) => {
            for (let index = 0; index < text.length; index += 1) view.setUint8(offset + index, text.charCodeAt(index));
        };
        write(0, 'RIFF');
        view.setUint32(4, 36 + length * 2, true);
        write(8, 'WAVE');
        write(12, 'fmt ');
        view.setUint32(16, 16, true);
        view.setUint16(20, 1, true);
        view.setUint16(22, 1, true);
        view.setUint32(24, sampleRate, true);
        view.setUint32(28, sampleRate * 2, true);
        view.setUint16(32, 2, true);
        view.setUint16(34, 16, true);
        write(36, 'data');
        view.setUint32(40, length * 2, true);
        for (let index = 0; index < length; index += 1) {
            const time = index / sampleRate;
            const envelope = Math.min(1, time / 0.015) * Math.exp(-3.4 * time);
            const sample = Math.sin(2 * Math.PI * 880 * time) * 0.55 + Math.sin(2 * Math.PI * 1320 * time) * 0.35;
            view.setInt16(44 + index * 2, Math.max(-1, Math.min(1, sample * envelope)) * 0x7fff, true);
        }
        return URL.createObjectURL(new Blob([buffer], { type: 'audio/wav' }));
    }

    function ensureChime() {
        if (!chime) {
            chime = new Audio(chimeUrl());
            chime.preload = 'auto';
        }
        return chime;
    }

    function playTone() {
        const audio = ensureChime();
        audio.volume = 1;
        audio.currentTime = 0;
        audio.play().catch(() => {});
    }

    function unlockTone() {
        const audio = ensureChime();
        audio.volume = 1;
        audio.currentTime = 0;
        return audio.play().then(() => {
            audio.pause();
            audio.currentTime = 0;
        }).catch(() => {});
    }

    async function notify(title, body, id) {
        const target = document.body.dataset.ordersUrl || '';
        try {
            const registration = await navigator.serviceWorker?.getRegistration();
            if (registration) {
                await registration.showNotification(title, {
                    body,
                    tag: `nova-order-${id}`,
                    renotify: true,
                    silent: false,
                    data: { url: target },
                });
                return;
            }
        } catch (error) {}
        try {
            const note = new Notification(title, { body });
            note.onclick = () => {
                window.focus();
                if (target) window.location.href = target;
            };
        } catch (error) {}
    }

    function updateBadge(count) {
        document.querySelectorAll('[data-order-badge]').forEach((node) => {
            node.textContent = String(count);
            node.classList.toggle('hidden', count < 1);
        });
        document.querySelectorAll('[data-order-count]').forEach((node) => {
            node.textContent = String(count);
        });
    }

    function applyStats(data) {
        updateBadge(Number(data.unread || 0));
        if (data.today_count != null) {
            document.querySelectorAll('[data-orders-today]').forEach((node) => {
                node.textContent = String(data.today_count);
            });
        }
        if (data.today_total) {
            document.querySelectorAll('[data-orders-revenue]').forEach((node) => {
                node.textContent = data.today_total;
            });
        }
    }

    function showLive(order) {
        const live = document.querySelector('[data-alert-live]');
        if (!live) return;
        const title = live.querySelector('[data-alert-live-title]');
        const body = live.querySelector('[data-alert-live-body]');
        if (title) title.textContent = 'Nuevo pedido recibido';
        const code = order.code || '';
        const total = order.total_label || '';
        const line = [code, total].filter(Boolean).join(' · ') || order.summary || 'Un cliente terminó un pedido.';
        if (body) body.textContent = line;
        live.classList.remove('hidden');
    }

    async function registerWorker() {
        if (!('serviceWorker' in navigator) || !document.body.dataset.sw) return;
        try {
            await navigator.serviceWorker.register(document.body.dataset.sw);
        } catch (error) {}
    }

    async function markCurrentHeard() {
        const endpoint = document.body.dataset.adminAlerts;
        if (!endpoint) return;
        try {
            const response = await fetch(endpoint, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                cache: 'no-store',
            });
            if (!response.ok) return;
            const data = await response.json();
            const seen = new Set((sessionStorage.getItem('nova.heard') || '').split(',').filter(Boolean));
            (Array.isArray(data.orders) ? data.orders : []).forEach((order) => {
                if (order.id) seen.add(order.id);
            });
            sessionStorage.setItem('nova.heard', [...seen].join(','));
            applyStats(data);
        } catch (error) {}
    }

    function startAdminWatch() {
        const endpoint = document.body.dataset.adminAlerts;
        if (!endpoint || watching || !alertsEnabled()) return;
        watching = true;
        const seen = new Set((sessionStorage.getItem('nova.heard') || '').split(',').filter(Boolean));
        let ticking = false;
        const tick = async () => {
            if (ticking || !alertsEnabled()) return;
            ticking = true;
            try {
                const response = await fetch(endpoint, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                    cache: 'no-store',
                });
                if (!response.ok) return;
                const data = await response.json();
                applyStats(data);
                const fresh = (Array.isArray(data.orders) ? data.orders : []).filter((order) => order.id && !seen.has(order.id));
                if (fresh.length === 0) return;
                fresh.forEach((order) => seen.add(order.id));
                sessionStorage.setItem('nova.heard', [...seen].join(','));
                playTone();
                const newest = fresh[0];
                const detail = [newest.code, newest.total_label].filter(Boolean).join(' · ') || newest.summary || 'Un cliente terminó un pedido.';
                const title = fresh.length > 1 ? `${fresh.length} pedidos nuevos` : 'Nuevo pedido recibido';
                showLive(newest);
                notify(title, detail, newest.id);
                document.dispatchEvent(new CustomEvent('nova:orders-changed'));
            } catch (error) {
            } finally {
                ticking = false;
            }
        };
        tick();
        window.setInterval(tick, 7000);
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'visible') tick();
        });
    }

    allowButton?.addEventListener('click', async () => {
        allowButton.setAttribute('disabled', 'disabled');
        playTone();
        if (!('Notification' in window)) {
            writeChoice('denied');
            setStatus('Este navegador no puede mostrar notificaciones. Las alertas no van a sonar.');
            denyButton.textContent = 'Entendido';
            return;
        }
        let result = 'denied';
        try {
            result = await Notification.requestPermission();
        } catch (error) {
            result = 'denied';
        }
        if (result === 'granted') {
            writeChoice('granted');
            hideGate();
            syncBanners();
            await registerWorker();
            await markCurrentHeard();
            startAdminWatch();
            return;
        }
        writeChoice('denied');
        setStatus('No se otorgó el permiso. El sistema de alertas no funciona en este dispositivo.');
        denyButton.textContent = 'Entendido';
        allowButton.classList.add('hidden');
    });

    denyButton?.addEventListener('click', () => {
        if (choice() !== 'granted') writeChoice('denied');
        hideGate();
        syncBanners();
    });

    document.querySelectorAll('[data-alert-retry]').forEach((button) => {
        button.addEventListener('click', () => {
            document.cookie = 'nova_alerts=; Path=/; Max-Age=0; SameSite=Lax';
            if (status) {
                status.textContent = '';
                status.classList.add('hidden');
            }
            allowButton?.classList.remove('hidden');
            allowButton?.removeAttribute('disabled');
            if (denyButton) denyButton.textContent = 'No permitir';
            showGate();
            syncBanners();
        });
    });

    document.querySelector('[data-alert-live-close]')?.addEventListener('click', () => {
        document.querySelector('[data-alert-live]')?.classList.add('hidden');
    });

    document.addEventListener('pointerdown', () => {
        if (alertsEnabled()) unlockTone();
    }, { once: true });

    if (alertsEnabled()) {
        hideGate();
        registerWorker().then(startAdminWatch);
    }
    syncBanners();
    if (document.documentElement.classList.contains('nova-alerts-ask')) {
        allowButton?.focus();
    }
})();
