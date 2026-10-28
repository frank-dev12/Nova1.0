<?php

declare(strict_types=1);

function admin_nav(): void
{
    $active = $GLOBALS['adminActive'] ?? '';
    $items = [
        'resumen' => ['Resumen', url('admin/index.php'), 'layout-dashboard'],
        'productos' => ['Productos', url('admin/productos.php'), 'package'],
        'pedidos' => ['Pedidos', url('admin/pedidos.php'), 'bell'],
        'mensajes' => ['Mensajes', url('admin/mensajes.php'), 'message-circle'],
    ];
    echo '<nav class="grid gap-1">';
    foreach ($items as $key => [$label, $href, $iconName]) {
        $class = $active === $key
            ? 'flex items-center gap-2 rounded-2xl bg-ink px-3 py-2.5 text-sm font-semibold text-white'
            : 'flex items-center gap-2 rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50';
        echo '<a class="' . $class . '" href="' . e($href) . '">' . icon($iconName, 'h-4 w-4') . '<span>' . e($label) . '</span>';
        if ($key === 'mensajes' && messages_unread_count() > 0) {
            echo '<span class="ml-auto rounded-full bg-nova px-2 py-0.5 text-[10px] font-bold text-white">' . e((string) messages_unread_count()) . '</span>';
        }
        if ($key === 'pedidos') {
            $pending = orders_unread_count();
            $hidden = $pending > 0 ? '' : ' hidden';
            echo '<span data-order-badge class="ml-auto rounded-full bg-nova px-2 py-0.5 text-[10px] font-bold text-white' . $hidden . '">' . e((string) $pending) . '</span>';
        }
        echo '</a>';
    }
    echo '</nav>';
}

function admin_header(string $title, string $active): void
{
    $GLOBALS['adminActive'] = $active;
    ?>
    <!doctype html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title><?= e($title) ?> · Panel NØVA</title>
        <link rel="icon" href="<?= e(asset('favicon.svg')) ?>" type="image/svg+xml">
        <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
        <?php render_alert_head(); ?>
    </head>
    <body class="min-h-screen bg-[linear-gradient(180deg,#eef6fb_0%,#f7fafc_40%,#e7f2fa_100%)] font-sans text-ink antialiased" data-base="<?= e(base_path()) ?>" data-sw="<?= e(url('sw.js')) ?>" data-admin-alerts="<?= e(url('api/alerts.php')) ?>" data-orders-url="<?= e(url('admin/pedidos.php')) ?>">
        <?php render_alert_gate(); ?>
        <a data-alert-live href="<?= e(url('admin/pedidos.php')) ?>" class="fixed bottom-5 left-1/2 z-[70] hidden max-w-[90vw] -translate-x-1/2 truncate rounded-full bg-ink px-4 py-2.5 text-sm text-white shadow-lg"></a>
        <button type="button" class="admin-backdrop" data-admin-backdrop aria-label="Cerrar menú"></button>
        <div class="mx-auto grid min-h-screen w-full max-w-6xl content-start gap-4 px-4 py-4 lg:grid-cols-[240px_1fr] lg:px-6">
            <div class="flex h-14 items-center gap-2 self-start rounded-2xl bg-white px-3 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.5)] lg:hidden">
                <a href="<?= e(url('admin/index.php')) ?>" class="px-1 text-lg font-black tracking-[-0.06em]">NØVA</a>
                <span class="min-w-0 truncate text-sm text-slate-500"><?= e($title) ?></span>
                <button type="button" class="ml-auto grid h-9 w-9 shrink-0 place-items-center rounded-full text-ink hover:bg-slate-50" data-admin-menu-toggle aria-expanded="false" aria-controls="menu-panel">
                    <span class="sr-only">Abrir menú del panel</span>
                    <span data-admin-icon-open><?= icon('menu', 'h-5 w-5') ?></span>
                    <span data-admin-icon-close class="hidden"><?= icon('x', 'h-5 w-5') ?></span>
                </button>
            </div>
            <aside id="menu-panel" data-admin-menu class="admin-side h-fit rounded-[24px] bg-white p-4 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.5)]">
                <div class="flex items-start justify-between">
                    <div>
                        <a href="<?= e(url('admin/index.php')) ?>" class="block px-2 text-xl font-black tracking-[-0.06em]">NØVA</a>
                        <p class="px-2 pb-4 text-xs text-slate-400">Panel</p>
                    </div>
                    <button type="button" class="grid h-9 w-9 place-items-center rounded-full text-ink hover:bg-slate-50 lg:hidden" data-admin-menu-close>
                        <span class="sr-only">Cerrar menú</span>
                        <?= icon('x', 'h-5 w-5') ?>
                    </button>
                </div>
                <?php admin_nav(); ?>
                <p data-alert-on class="mt-4 hidden rounded-2xl bg-slate-50 px-3 py-2 text-xs leading-relaxed text-slate-500">El tono está activo. Deja esta pestaña abierta para oír los pedidos.</p>
                <div data-alert-off class="mt-4 hidden rounded-2xl bg-orange-50 px-3 py-3 text-xs leading-relaxed text-slate-600">
                    <p class="font-semibold text-ink">Alertas apagadas</p>
                    <p class="mt-1">Sin permiso de notificaciones no suena el aviso de pedidos.</p>
                    <button type="button" data-alert-retry class="mt-2 font-semibold text-nova">Activar</button>
                </div>
                <div class="mt-4 grid gap-1 border-t border-slate-100 pt-3">
                    <a href="<?= e(url('index.php')) ?>" class="flex items-center gap-2 rounded-2xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-50"><?= icon('eye', 'h-4 w-4') ?> Ver tienda</a>
                    <form method="post" action="<?= e(url('admin/logout.php')) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="flex w-full items-center gap-2 rounded-2xl px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50"><?= icon('log-out', 'h-4 w-4') ?> Salir</button>
                    </form>
                </div>
            </aside>
            <div class="flex min-w-0 flex-col gap-4">
                <?php render_flashes(); ?>
    <?php
}

function admin_footer(): void
{
    ?>
            </div>
        </div>
        <script>
            const adminMenuButton = document.querySelector('[data-admin-menu-toggle]');
            const adminMenu = document.querySelector('[data-admin-menu]');
            const adminBackdrop = document.querySelector('[data-admin-backdrop]');
            const adminIconOpen = document.querySelector('[data-admin-icon-open]');
            const adminIconClose = document.querySelector('[data-admin-icon-close]');
            const setAdminMenu = (open) => {
                if (!adminMenu || !adminMenuButton) return;
                adminMenu.classList.toggle('is-open', open);
                adminBackdrop?.classList.toggle('is-open', open);
                adminMenuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
                adminIconOpen?.classList.toggle('hidden', open);
                adminIconClose?.classList.toggle('hidden', !open);
                document.documentElement.classList.toggle('admin-menu-open', open);
            };
            if (adminMenuButton && adminMenu) {
                adminMenuButton.addEventListener('click', () => {
                    setAdminMenu(!adminMenu.classList.contains('is-open'));
                });
                adminBackdrop?.addEventListener('click', () => setAdminMenu(false));
                document.querySelector('[data-admin-menu-close]')?.addEventListener('click', () => setAdminMenu(false));
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') setAdminMenu(false);
                });
            }
        </script>
        <script src="<?= e(asset('js/alerts.js')) ?>" defer></script>
    </body>
    </html>
    <?php
}
