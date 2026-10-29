<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$pageTitle = $pageTitle ?? 'Inicio';
$active = $active ?? '';
$metaDescription = $metaDescription ?? 'NØVA es ropa versátil para cada momento. Catálogo con envíos a todo el Perú y pedidos por WhatsApp.';
$searchQuery = $searchQuery ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="theme-color" content="#e7f3fb">
    <title><?= e($pageTitle) ?> · NØVA</title>
    <link rel="icon" href="<?= e(asset('favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
    <script>window.novaSkeletonStart = performance.now();</script>
    <?php render_alert_head(); ?>
</head>
<body class="min-h-screen bg-[radial-gradient(1100px_420px_at_8%_-10%,#ffffff,transparent_60%),radial-gradient(900px_500px_at_100%_0%,#d7ecf8,transparent_55%),linear-gradient(180deg,#eef6fb_0%,#f5f8fb_42%,#e7f2fa_100%)] font-sans text-ink antialiased" data-base="<?= e(base_path()) ?>" data-admin="<?= e(url('admin/login.php')) ?>" data-sw="<?= e(url('sw.js')) ?>">
    <?php render_alert_gate(); ?>
    <div class="nova-loader" data-loader role="status" aria-live="polite" aria-label="Cargando NØVA">
        <div class="nova-skeleton-frame">
            <div class="nova-skeleton-block h-14 w-full rounded-[22px]"></div>
            <div class="nova-skeleton-block mt-4 h-56 w-full rounded-[28px]"></div>
            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="nova-skeleton-block h-44 rounded-[22px]"></div>
                <div class="nova-skeleton-block h-44 rounded-[22px]"></div>
                <div class="nova-skeleton-block hidden h-44 rounded-[22px] sm:block"></div>
                <div class="nova-skeleton-block hidden h-44 rounded-[22px] sm:block"></div>
            </div>
        </div>
    </div>
    <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-white focus:px-4 focus:py-2">Saltar al contenido</a>
    <div class="mx-auto flex min-h-screen w-full max-w-6xl flex-col px-4 py-4 sm:px-6">
        <header class="sticky top-3 z-40 rounded-[22px] bg-white/95 px-3 py-2.5 shadow-[0_16px_40px_-28px_rgba(15,23,42,0.7)] backdrop-blur sm:px-4">
            <div class="flex items-center gap-2 sm:gap-3">
                <a href="<?= e(url('index.php')) ?>" data-logo class="shrink-0 text-[1.35rem] font-black tracking-[-0.06em] text-ink">NØVA</a>
                <nav class="ml-2 hidden items-center gap-1 lg:flex" aria-label="Principal">
                    <?php foreach (nav_items() as $key => [$label, $href]): ?>
                        <a href="<?= e($href) ?>" class="<?= $active === $key ? 'rounded-full bg-slate-100 px-3 py-1.5 text-sm font-semibold text-ink' : 'rounded-full px-3 py-1.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-ink' ?>" <?= $active === $key ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
                    <?php endforeach; ?>
                </nav>
                <form action="<?= e(url('catalogo.php')) ?>" method="get" class="relative ml-auto hidden md:block" role="search">
                    <label class="sr-only" for="buscar-escritorio">Buscar productos</label>
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?= icon('search', 'h-4 w-4') ?></span>
                    <input id="buscar-escritorio" data-search type="search" name="q" value="<?= e($searchQuery) ?>" placeholder="Buscar productos..." autocomplete="off" class="h-10 w-52 rounded-full border border-slate-200 bg-white pl-9 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-slate-400 lg:w-64">
                    <div data-suggest class="absolute left-0 right-0 top-[calc(100%+8px)] z-50 hidden overflow-hidden rounded-2xl border border-slate-100 bg-white p-1 shadow-xl"></div>
                </form>
                <a href="<?= e(url('carrito.php')) ?>" class="relative ml-auto grid h-10 w-10 place-items-center rounded-full text-ink transition hover:bg-slate-50 md:ml-1" aria-label="Carrito">
                    <?= icon('shopping-bag', 'h-5 w-5') ?>
                    <span data-cart-count class="absolute -right-0.5 -top-0.5 grid h-4 min-w-4 place-items-center rounded-full bg-nova px-1 text-[10px] font-bold leading-none text-white" aria-live="polite"><?= e((string) cart_count()) ?></span>
                </a>
                <button type="button" class="grid h-10 w-10 place-items-center rounded-full text-ink hover:bg-slate-50 lg:hidden" data-menu-toggle aria-expanded="false" aria-controls="menu-movil">
                    <span class="sr-only">Abrir menú</span>
                    <span data-menu-icon-open><?= icon('menu', 'h-5 w-5') ?></span>
                    <span data-menu-icon-close class="hidden"><?= icon('x', 'h-5 w-5') ?></span>
                </button>
            </div>
            <div id="menu-movil" data-mobile-menu class="hidden border-t border-slate-100 pt-3 lg:hidden">
                <form action="<?= e(url('catalogo.php')) ?>" method="get" class="relative mb-3" role="search">
                    <label class="sr-only" for="buscar-movil">Buscar productos</label>
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"><?= icon('search', 'h-4 w-4') ?></span>
                    <input id="buscar-movil" data-search type="search" name="q" value="<?= e($searchQuery) ?>" placeholder="Buscar productos..." autocomplete="off" class="h-11 w-full rounded-full border border-slate-200 pl-9 pr-4 text-sm outline-none focus:border-slate-400">
                    <div data-suggest class="absolute left-0 right-0 top-[calc(100%+8px)] z-50 hidden overflow-hidden rounded-2xl border border-slate-100 bg-white p-1 shadow-xl"></div>
                </form>
                <nav class="grid gap-1 pb-2" aria-label="Móvil">
                    <?php foreach (nav_items() as $key => [$label, $href]): ?>
                        <a href="<?= e($href) ?>" class="<?= $active === $key ? 'rounded-2xl bg-slate-100 px-3 py-3 text-sm font-semibold text-ink' : 'rounded-2xl px-3 py-3 text-sm font-medium text-slate-700 hover:bg-slate-50' ?>"><?= e($label) ?></a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </header>
        <main id="contenido" class="mt-4 flex flex-1 flex-col gap-4">
            <?php render_flashes(); ?>
