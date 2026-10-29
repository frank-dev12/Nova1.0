<?php

declare(strict_types=1);

function admin_nav(): void
{
    $active = $GLOBALS['adminActive'] ?? '';
    $groups = [
        'General' => [
            'resumen' => ['Resumen', url('admin/index.php'), 'layout-dashboard'],
            'pedidos' => ['Pedidos', url('admin/pedidos.php'), 'shopping-bag'],
            'mensajes' => ['Mensajes', url('admin/mensajes.php'), 'message-circle'],
        ],
        'Catálogo' => [
            'productos' => ['Productos', url('admin/productos.php'), 'package'],
            'categorias' => ['Categorías', url('admin/categorias.php'), 'grid-2x2'],
            'tallas' => ['Tallas', url('admin/tallas.php'), 'ruler'],
            'colores' => ['Colores', url('admin/colores.php'), 'palette'],
            'colecciones' => ['Colecciones', url('admin/colecciones.php'), 'layers'],
        ],
        'Gestión' => [
            'inventario' => ['Inventario', url('admin/inventario.php'), 'boxes'],
            'promociones' => ['Promociones', url('admin/promociones.php'), 'percent'],
        ],
        'Sistema' => [
            'configuracion' => ['Configuración', url('admin/configuracion.php'), 'settings'],
            'tienda' => ['Ver tienda', url('index.php'), 'eye'],
        ],
    ];

    echo '<nav class="grid">';
    foreach ($groups as $group => $items) {
        echo '<p class="px-3 pb-1 pt-5 text-[11px] font-medium uppercase tracking-[0.16em] text-white/40">' . e($group) . '</p>';
        echo '<div class="grid gap-1">';
        foreach ($items as $key => [$label, $href, $iconName]) {
            $isActive = $active === $key;
            $class = $isActive
                ? 'flex items-center gap-3 rounded-full border border-[#c4a46a]/45 bg-white/10 px-3 py-2.5 text-sm font-medium text-white'
                : 'flex items-center gap-3 rounded-full border border-[#c4a46a]/25 px-3 py-2.5 text-sm text-white/75 hover:border-[#c4a46a]/50 hover:bg-white/5 hover:text-white';
            echo '<a class="' . $class . '" href="' . e($href) . '">' . icon($iconName, 'h-4 w-4 shrink-0') . '<span>' . e($label) . '</span><span class="ml-auto flex items-center gap-2">';
            if ($key === 'mensajes' && messages_unread_count() > 0) {
                echo '<span class="rounded-full bg-emerald-400/15 px-2 py-0.5 text-[10px] font-bold text-emerald-300">' . e((string) messages_unread_count()) . '</span>';
            }
            if ($key === 'pedidos') {
                $pending = orders_unread_count();
                $hidden = $pending > 0 ? '' : ' hidden';
                echo '<span data-order-badge class="rounded-full bg-emerald-400/15 px-2 py-0.5 text-[10px] font-bold text-emerald-300' . $hidden . '">' . e((string) $pending) . '</span>';
            }
            if ($isActive) {
                echo icon('chevron-right', 'h-4 w-4 text-white/50');
            }
            echo '</span></a>';
        }
        echo '</div>';
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
        <script>window.novaSkeletonStart = performance.now();</script>
        <?php render_alert_head(); ?>
    </head>
    <body class="min-h-screen bg-[linear-gradient(180deg,#eef6fb_0%,#f7fafc_40%,#e7f2fa_100%)] font-sans text-ink antialiased" data-base="<?= e(base_path()) ?>" data-sw="<?= e(url('sw.js')) ?>" data-admin-alerts="<?= e(url('api/alerts.php')) ?>" data-orders-url="<?= e(url('admin/pedidos.php')) ?>">
        <?php render_alert_gate(); ?>
        <div class="admin-skeleton" data-admin-skeleton aria-hidden="true">
            <div class="admin-skeleton-side nova-skeleton-block"></div>
            <div class="admin-skeleton-main">
                <div class="nova-skeleton-block h-28"></div>
                <div class="mt-4 grid grid-cols-3 gap-3">
                    <div class="nova-skeleton-block h-24"></div>
                    <div class="nova-skeleton-block h-24"></div>
                    <div class="nova-skeleton-block h-24"></div>
                </div>
                <div class="nova-skeleton-block mt-4 h-48"></div>
            </div>
        </div>
        <div data-alert-live class="fixed bottom-5 right-4 z-[70] hidden w-[min(100%-2rem,22rem)] rounded-3xl bg-white p-4 text-ink shadow-[0_20px_50px_-24px_rgba(15,23,42,0.65)]">
            <div class="flex items-start gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-rose-50 text-rose-500"><?= icon('bell', 'h-5 w-5') ?></span>
                <div class="min-w-0 flex-1">
                    <p data-alert-live-title class="font-semibold">Nuevo pedido recibido</p>
                    <p data-alert-live-body class="mt-0.5 text-sm text-slate-500"></p>
                    <p class="mt-1 text-xs text-slate-400">Hace unos segundos</p>
                </div>
                <button type="button" data-alert-live-close class="grid h-8 w-8 place-items-center rounded-full text-slate-400 hover:bg-slate-100" aria-label="Cerrar aviso"><?= icon('x', 'h-4 w-4') ?></button>
            </div>
            <a href="<?= e(url('admin/pedidos.php')) ?>" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-nova">Ver pedido <?= icon('arrow-right', 'h-4 w-4') ?></a>
        </div>
        <button type="button" class="admin-backdrop" data-admin-backdrop aria-label="Cerrar menú"></button>
        <div class="lg:grid lg:min-h-screen lg:grid-cols-[340px_1fr]">
            <div class="mx-4 mt-4 flex h-14 items-center gap-2 self-start rounded-2xl border border-white/80 bg-white/70 px-3 text-ink shadow-[0_10px_28px_-14px_rgba(20,20,20,0.45),0_0_0_1px_rgba(20,20,20,0.04)] backdrop-blur-md lg:hidden">
                <a href="<?= e(url('admin/index.php')) ?>" class="px-1 text-lg font-black tracking-[-0.06em]">NØVA</a>
                <span class="min-w-0 truncate text-sm text-slate-500"><?= e($title) ?></span>
                <button type="button" class="ml-auto grid h-9 w-9 shrink-0 place-items-center rounded-full text-ink hover:bg-black/5" data-admin-menu-toggle aria-expanded="false" aria-controls="menu-panel">
                    <span class="sr-only">Abrir menú del panel</span>
                    <span data-admin-icon-open><?= icon('menu', 'h-5 w-5') ?></span>
                    <span data-admin-icon-close class="hidden"><?= icon('x', 'h-5 w-5') ?></span>
                </button>
            </div>
            <aside id="menu-panel" data-admin-menu class="admin-side flex h-full flex-col overflow-hidden bg-black/80 text-white backdrop-blur-md lg:sticky lg:top-0 lg:h-screen">
                <div class="flex shrink-0 items-start gap-3 p-4 pb-3 lg:p-5 lg:pb-4">
                    <a href="<?= e(url('admin/index.php')) ?>" class="grid h-11 shrink-0 place-items-center rounded-2xl bg-black px-3 text-sm font-black tracking-tight">NØVA.</a>
                    <div class="min-w-0 flex-1 pt-0.5">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold leading-tight">Panel administrativo</p>
                            <span class="shrink-0 rounded-full border border-emerald-400/70 px-2 py-0.5 text-[11px] font-medium text-emerald-400">Admin</span>
                        </div>
                        <p class="text-xs text-white/50">Gestión de tienda</p>
                    </div>
                    <button type="button" class="grid h-9 w-9 shrink-0 place-items-center rounded-full border border-[#c4a46a]/30 text-white hover:bg-white/10 lg:hidden" data-admin-menu-close>
                        <span class="sr-only">Cerrar menú</span>
                        <?= icon('x', 'h-5 w-5') ?>
                    </button>
                </div>
                <div class="min-h-0 flex-1 overflow-y-auto border-t border-white/10 px-4 pb-2 pt-1 lg:px-5">
                    <?php admin_nav(); ?>
                </div>
                <div class="shrink-0 border-t border-white/10 p-4 pt-3 lg:px-5">
                    <p data-alert-on class="mb-3 hidden rounded-2xl bg-white/5 px-3 py-2 text-xs leading-relaxed text-white/60">El tono está activo. Deja esta pestaña abierta para oír los pedidos.</p>
                    <div data-alert-off class="mb-3 hidden rounded-2xl bg-white/5 px-3 py-3 text-xs leading-relaxed text-white/70">
                        <p class="font-semibold text-white">Alertas apagadas</p>
                        <p class="mt-1">Sin permiso de notificaciones no suena el aviso de pedidos.</p>
                        <button type="button" data-alert-retry class="mt-2 font-semibold text-emerald-300">Activar</button>
                    </div>
                    <form method="post" action="<?= e(url('admin/logout.php')) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="flex w-full items-center gap-3 rounded-full border border-[#c4a46a]/25 px-3 py-2.5 text-left text-sm text-white/80 hover:border-[#c4a46a]/50 hover:bg-white/5"><?= icon('log-out', 'h-4 w-4') ?> Cerrar sesión</button>
                    </form>
                </div>
            </aside>
            <div class="flex min-w-0 flex-col gap-4 px-4 py-4 lg:px-8 lg:py-6">
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
        <script src="<?= e(asset('js/skeleton.js')) ?>" defer></script>
        <script src="<?= e(asset('js/alerts.js')) ?>" defer></script>
    </body>
    </html>
    <?php
}
