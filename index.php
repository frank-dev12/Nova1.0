<?php

declare(strict_types=1);

$pageTitle = 'Tu estilo, sin límites';
$active = 'inicio';
$metaDescription = 'NØVA: ropa versátil, diseños atemporales y pedidos por WhatsApp. Envíos a todo el Perú.';
require __DIR__ . '/includes/header.php';

$slides = hero_slides();
$slideCount = count($slides);
?>
<section class="relative overflow-hidden rounded-[28px] bg-white shadow-[0_24px_50px_-32px_rgba(15,23,42,0.45)]" data-carousel>
    <div class="carousel is-ready">
    <?php foreach ($slides as $index => $slide): ?>
        <article class="hero-slide grid bg-white lg:grid-cols-2 <?= $index === 0 ? 'is-active' : '' ?>" data-slide <?= $index === 0 ? '' : 'inert' ?>>
            <div class="flex flex-col justify-center px-6 pb-8 pt-8 sm:px-10 lg:py-12 lg:pl-20 lg:pr-12">
                <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-500"><?= e($slide['eyebrow']) ?></p>
                <h1 class="mt-3 max-w-xl text-[2.55rem] font-black leading-[0.92] tracking-[-0.045em] text-ink sm:text-6xl">
                    <?= implode('<br>', array_map('e', explode("\n", $slide['title']))) ?>
                </h1>
                <p class="mt-4 max-w-sm text-sm leading-relaxed text-slate-600 sm:text-[15px]"><?= e($slide['text']) ?></p>
                <div class="mt-6">
                    <a href="<?= e(url($slide['href'])) ?>" class="inline-flex items-center gap-2 rounded-full bg-nova px-5 py-3 text-sm font-semibold text-white shadow-[0_12px_24px_-12px_rgba(255,90,31,0.95)] transition hover:brightness-95">
                        <?= e($slide['cta']) ?>
                        <?= icon('arrow-right', 'h-4 w-4') ?>
                    </a>
                    <p class="mt-2 text-xs text-slate-400"><?= e($slide['note']) ?></p>
                </div>
                <div class="mt-8 flex items-center gap-3 text-xs font-semibold text-slate-700">
                    <span><?= e(sprintf('%02d', $index + 1)) ?></span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-400"><?= e(sprintf('%02d', $slideCount)) ?></span>
                    <span class="ml-1 h-[3px] w-16 overflow-hidden rounded-full bg-slate-200">
                        <span class="block h-full bg-ink" style="width: <?= e((string) (int) round((($index + 1) / $slideCount) * 100)) ?>%"></span>
                    </span>
                </div>
            </div>
            <div class="relative min-h-[280px] sm:min-h-[360px] lg:min-h-[460px]">
                <img src="<?= e(asset($slide['image'])) ?>" alt="<?= e($slide['alt']) ?>" class="absolute inset-0 h-full w-full object-cover object-[center_22%]" <?= $index === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                <div class="pointer-events-none absolute inset-y-0 left-0 hidden w-24 bg-gradient-to-r from-white to-transparent lg:block"></div>
                <p class="pointer-events-none absolute right-6 top-6 font-script text-3xl <?= e($slide['script_class']) ?> sm:text-4xl"><?= e($slide['script']) ?></p>
            </div>
        </article>
    <?php endforeach; ?>
    </div>
    <button type="button" class="absolute bottom-36 left-3 z-20 grid h-10 w-10 place-items-center rounded-full bg-white text-ink shadow-md transition hover:bg-slate-50 sm:bottom-44 lg:bottom-auto lg:top-1/2 lg:-translate-y-1/2" data-carousel-prev aria-label="Anterior">
        <?= icon('chevron-left', 'h-5 w-5') ?>
    </button>
    <button type="button" class="absolute bottom-36 right-3 z-20 grid h-10 w-10 place-items-center rounded-full bg-white text-ink shadow-md transition hover:bg-slate-50 sm:bottom-44 lg:bottom-auto lg:top-1/2 lg:-translate-y-1/2" data-carousel-next aria-label="Siguiente">
        <?= icon('chevron-right', 'h-5 w-5') ?>
    </button>
</section>

<section class="grid rounded-[24px] bg-white px-2 py-2 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.45)] md:grid-cols-3 md:divide-x md:divide-slate-100" aria-label="Beneficios">
    <?php
    $benefits = [
        ['truck', 'Envíos a todo el Perú', 'Recibe tu pedido donde estés.', url('catalogo.php'), false],
        ['whatsapp', 'Compra por WhatsApp', 'Haz tu pedido de forma rápida.', whatsapp_link('Hola NØVA, quiero hacer un pedido.'), true],
        ['headphones', 'Atención personalizada', 'Estamos para ayudarte.', url('contacto.php'), false],
    ];
    foreach ($benefits as [$iconName, $title, $text, $href, $external]):
    ?>
        <a href="<?= e($href) ?>" class="flex items-center gap-3 rounded-2xl px-4 py-4 transition hover:bg-slate-50" <?= $external ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-slate-50 text-slate-700">
                <?= $iconName === 'whatsapp' ? icon_whatsapp('h-5 w-5') : icon($iconName, 'h-5 w-5') ?>
            </span>
            <span>
                <span class="block text-sm font-semibold text-ink"><?= e($title) ?></span>
                <span class="mt-0.5 block text-xs text-slate-500"><?= e($text) ?></span>
            </span>
        </a>
    <?php endforeach; ?>
</section>

<section class="rounded-[28px] bg-white p-4 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.45)] sm:p-6">
    <div class="flex flex-col gap-2 px-1 md:flex-row md:items-end md:justify-between">
        <h2 class="text-2xl font-black tracking-[-0.04em] sm:text-[1.7rem]">ENCUENTRA TU ESTILO</h2>
        <p class="max-w-xs text-sm leading-relaxed text-slate-500 md:text-right">Prendas versátiles, diseños atemporales y la calidad que te acompaña siempre.</p>
    </div>
    <div class="mt-5 grid gap-4 md:grid-cols-3">
        <?php foreach (home_categories() as $category): ?>
            <a href="<?= e(url('catalogo.php?categoria=' . urlencode($category['slug']))) ?>" class="group relative flex min-h-[176px] overflow-hidden rounded-[24px] bg-[#f7f5f2]">
                <span class="relative z-10 flex w-[58%] flex-col p-5">
                    <span class="text-xl font-bold tracking-tight"><?= e($category['name']) ?></span>
                    <span class="mt-1 text-sm leading-snug text-slate-500"><?= e($category['tagline']) ?></span>
                    <span class="mt-4 inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition group-hover:border-ink group-hover:bg-ink group-hover:text-white">
                        <?= icon('arrow-right', 'h-4 w-4') ?>
                    </span>
                </span>
                <img src="<?= e(asset($category['image'])) ?>" alt="" class="absolute right-0 top-0 h-full w-[54%] object-cover object-top">
                <span class="pointer-events-none absolute inset-y-0 right-[42%] w-16 bg-gradient-to-r from-[#f7f5f2] to-transparent"></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="rounded-[28px] bg-white p-4 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.45)] sm:p-6">
    <div class="flex items-end justify-between gap-3 px-1">
        <h2 class="text-2xl font-black tracking-[-0.04em] sm:text-[1.7rem]">FAVORITOS DE LA TEMPORADA</h2>
        <a href="<?= e(url('catalogo.php')) ?>" class="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-nova">
            Ver todos
            <?= icon('arrow-right', 'h-4 w-4') ?>
        </a>
    </div>
    <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
        <?php foreach (products_featured() as $product): ?>
            <?php render_product_card($product); ?>
        <?php endforeach; ?>
    </div>
</section>

<section class="relative overflow-hidden rounded-[28px] bg-[#17191b] text-white shadow-[0_18px_40px_-28px_rgba(15,23,42,0.8)]">
    <div class="grid items-center lg:grid-cols-[1.1fr_0.9fr]">
        <div class="relative z-10 p-6 sm:p-10 lg:p-12">
            <h2 class="text-3xl font-black tracking-[-0.04em] sm:text-4xl">RENUEVA TU LOOK.</h2>
            <p class="mt-2 max-w-sm text-sm text-white/75">Encuentra tus próximos favoritos.</p>
            <a href="<?= e(url('catalogo.php')) ?>" class="mt-6 inline-flex items-center gap-2 rounded-full bg-nova px-5 py-3 text-sm font-semibold text-white transition hover:brightness-95">
                Ver colección
                <?= icon('arrow-right', 'h-4 w-4') ?>
            </a>
        </div>
        <div class="relative min-h-[220px] sm:min-h-[260px]">
            <img src="<?= e(asset('img/hero/banner.jpg')) ?>" alt="Persona con casaca negra y polo claro" class="absolute inset-0 h-full w-full object-cover object-[center_20%]">
            <div class="pointer-events-none absolute inset-y-0 left-0 hidden w-24 bg-gradient-to-r from-[#17191b] to-transparent lg:block"></div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
