self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const target = (event.notification.data && event.notification.data.url) || './admin/pedidos.php';
    event.waitUntil((async () => {
        const windows = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
        for (const client of windows) {
            if (client.url.includes('/admin/') && 'focus' in client) {
                if (typeof client.navigate === 'function') {
                    try {
                        await client.navigate(target);
                    } catch (error) {}
                }
                return client.focus();
            }
        }
        if (self.clients.openWindow) {
            return self.clients.openWindow(target);
        }
        return undefined;
    })());
});
