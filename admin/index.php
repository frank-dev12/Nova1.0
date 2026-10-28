<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require dirname(__DIR__) . '/includes/admin_layout.php';

admin_header('Resumen', 'resumen');
$products = products_all(false);
$activeProducts = count(array_filter($products, static fn (array $product): bool => $product['active']));
?>
<section class="rounded-[28px] bg-white p-6">
    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Hoy</p>
    <h1 class="mt-1 text-3xl font-black tracking-[-0.04em]">Resumen</h1>
    <p class="mt-2 max-w-xl text-sm text-slate-500">El catálogo vive en un archivo del servidor. Desde aquí puedes publicar prendas, ver los pedidos del carrito y leer lo que llega del formulario de contacto.</p>
</section>
<section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <article class="rounded-[24px] bg-white p-5">
        <p class="text-sm text-slate-500">Prendas activas</p>
        <p class="mt-2 text-3xl font-black"><?= e((string) $activeProducts) ?></p>
    </article>
    <article class="rounded-[24px] bg-white p-5">
        <p class="text-sm text-slate-500">Categorías</p>
        <p class="mt-2 text-3xl font-black"><?= e((string) count(categories_all())) ?></p>
    </article>
    <article class="rounded-[24px] bg-white p-5">
        <p class="text-sm text-slate-500">Mensajes sin leer</p>
        <p class="mt-2 text-3xl font-black"><?= e((string) messages_unread_count()) ?></p>
    </article>
    <article class="rounded-[24px] bg-white p-5">
        <p class="text-sm text-slate-500">Pedidos sin leer</p>
        <p class="mt-2 text-3xl font-black" data-order-count><?= e((string) orders_unread_count()) ?></p>
        <a href="<?= e(url('admin/pedidos.php')) ?>" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-nova">Ver pedidos <?= icon('arrow-right', 'h-4 w-4') ?></a>
    </article>
</section>
<section class="rounded-[24px] bg-white p-5">
    <h2 class="font-bold">Siguiente paso</h2>
    <p class="mt-1 text-sm text-slate-500">Reemplaza las fotos de referencia cuando tengas la campaña definitiva. El número de WhatsApp está en <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">includes/config.php</code>.</p>
    <a href="<?= e(url('admin/productos.php')) ?>" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-nova">Ir a productos <?= icon('arrow-right', 'h-4 w-4') ?></a>
</section>
<?php admin_footer(); ?>
