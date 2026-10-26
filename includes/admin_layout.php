<?php

declare(strict_types=1);

function admin_nav(): void
{
    $active = $GLOBALS['adminActive'] ?? '';
    $items = [
        'resumen' => ['Resumen', url('admin/index.php'), 'layout-dashboard'],
        'productos' => ['Productos', url('admin/productos.php'), 'package'],
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
    </head>
    <body class="min-h-screen bg-[linear-gradient(180deg,#eef6fb_0%,#f7fafc_40%,#e7f2fa_100%)] font-sans text-ink antialiased">
        <div class="mx-auto grid min-h-screen max-w-6xl gap-4 px-4 py-4 lg:grid-cols-[240px_1fr] lg:px-6">
            <aside class="h-fit rounded-[24px] bg-white p-4 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.5)]">
                <a href="<?= e(url('admin/index.php')) ?>" class="block px-2 text-xl font-black tracking-[-0.06em]">NØVA</a>
                <p class="px-2 pb-4 text-xs text-slate-400">Panel</p>
                <?php admin_nav(); ?>
                <div class="mt-4 grid gap-1 border-t border-slate-100 pt-3">
                    <a href="<?= e(url('index.php')) ?>" class="flex items-center gap-2 rounded-2xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-50"><?= icon('eye', 'h-4 w-4') ?> Ver tienda</a>
                    <form method="post" action="<?= e(url('admin/logout.php')) ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="flex w-full items-center gap-2 rounded-2xl px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50"><?= icon('log-out', 'h-4 w-4') ?> Salir</button>
                    </form>
                </div>
            </aside>
            <div class="flex flex-col gap-4">
                <?php render_flashes(); ?>
    <?php
}

function admin_footer(): void
{
    ?>
            </div>
        </div>
    </body>
    </html>
    <?php
}
