<?php

declare(strict_types=1);

$pageTitle = 'Nosotros';
$active = 'nosotros';
$metaDescription = 'NØVA nace en Lima. Ropa versátil, diseños atemporales y atención directa por WhatsApp.';
require __DIR__ . '/includes/header.php';
?>
<section class="grid overflow-hidden rounded-[28px] bg-white lg:grid-cols-2">
    <div class="flex flex-col justify-center p-6 sm:p-10 lg:p-12">
        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-slate-500">La marca</p>
        <h1 class="mt-3 text-4xl font-black tracking-[-0.045em] sm:text-5xl">Más que ropa.</h1>
        <p class="mt-4 max-w-md text-sm leading-relaxed text-slate-600 sm:text-base">NØVA arma un guardarropa corto: polos, casacas, pantalones y hoodies que se combinan entre sí. Nacimos en Lima y enviamos a todo el Perú.</p>
        <p class="mt-3 max-w-md text-sm leading-relaxed text-slate-600">Esta es la versión 1.0 del catálogo. El pedido se cierra por WhatsApp, con alguien del equipo, no con un checkout automático.</p>
    </div>
    <div class="relative min-h-[280px]">
        <img src="<?= e(asset('img/hero/piezas.jpg')) ?>" alt="Campaña NØVA con casaca negra sobre concreto" class="absolute inset-0 h-full w-full object-cover object-top">
    </div>
</section>

<section class="grid gap-4 md:grid-cols-3">
    <?php
    $values = [
        ['tag', 'Versátil', 'Pocas prendas, muchas formas de usarlas. El polo va solo o debajo de la casaca.'],
        ['package', 'Atemporal', 'Negro, arena, olivo y denim. Colores que siguen sirviendo la próxima temporada.'],
        ['headphones', 'Cercana', 'Escribes, te respondemos y coordinamos talla, stock y envío sin filas.'],
    ];
    foreach ($values as [$iconName, $title, $text]):
    ?>
        <article class="rounded-[24px] bg-white p-6">
            <span class="grid h-11 w-11 place-items-center rounded-full bg-slate-50 text-ink"><?= icon($iconName, 'h-5 w-5') ?></span>
            <h2 class="mt-4 text-lg font-bold"><?= e($title) ?></h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-600"><?= e($text) ?></p>
        </article>
    <?php endforeach; ?>
</section>

<section class="flex flex-col items-start justify-between gap-4 rounded-[28px] bg-ink px-6 py-8 text-white sm:flex-row sm:items-center sm:px-10">
    <div>
        <h2 class="text-2xl font-black tracking-tight">¿Armamos tu próximo pedido?</h2>
        <p class="mt-1 text-sm text-white/70">Mira la colección o escríbenos directo.</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <a href="<?= e(url('catalogo.php')) ?>" class="inline-flex items-center gap-2 rounded-full bg-nova px-5 py-3 text-sm font-semibold">Explorar catálogo <?= icon('arrow-right', 'h-4 w-4') ?></a>
        <a href="<?= e(url('contacto.php')) ?>" class="inline-flex items-center rounded-full border border-white/20 px-5 py-3 text-sm font-semibold">Contacto</a>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
