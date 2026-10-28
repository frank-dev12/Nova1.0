<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require dirname(__DIR__) . '/includes/admin_layout.php';

admin_header('Inventario', 'inventario');
$products = products_all(false);
$out = 0;
$low = 0;
foreach ($products as $product) {
    $stock = (int) $product['stock'];
    if ($stock < 1) {
        $out++;
    } elseif ($stock <= 3) {
        $low++;
    }
}
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <h1 class="text-2xl font-black tracking-tight">Inventario</h1>
    <p class="mt-1 text-sm text-slate-500">Stock por prenda. Bajo es 3 unidades o menos.</p>
    <div class="mt-4 grid grid-cols-3 gap-3">
        <article class="rounded-3xl bg-slate-50 p-4">
            <p class="text-xs text-slate-500">Prendas</p>
            <p class="mt-1 text-2xl font-black"><?= e((string) count($products)) ?></p>
        </article>
        <article class="rounded-3xl bg-amber-50 p-4">
            <p class="text-xs text-amber-700">Bajo</p>
            <p class="mt-1 text-2xl font-black text-amber-800"><?= e((string) $low) ?></p>
        </article>
        <article class="rounded-3xl bg-rose-50 p-4">
            <p class="text-xs text-rose-600">Agotado</p>
            <p class="mt-1 text-2xl font-black text-rose-700"><?= e((string) $out) ?></p>
        </article>
    </div>
    <div class="mt-5 divide-y divide-slate-100">
        <?php foreach ($products as $product): ?>
            <?php $stock = (int) $product['stock']; ?>
            <div class="flex items-center gap-3 py-3">
                <div class="min-w-0 flex-1">
                    <p class="truncate font-semibold"><?= e($product['name']) ?></p>
                    <p class="text-sm text-slate-500"><?= $stock > 0 ? e((string) $stock) . ' en stock' : 'Sin unidades' ?></p>
                </div>
                <?php if ($stock < 1): ?>
                    <span class="shrink-0 rounded-full bg-rose-50 px-2 py-0.5 text-xs font-semibold text-rose-600">Agotado</span>
                <?php elseif ($stock <= 3): ?>
                    <span class="shrink-0 rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700">Bajo</span>
                <?php else: ?>
                    <span class="shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">Disponible</span>
                <?php endif; ?>
                <a href="<?= e(url('admin/producto.php?id=' . urlencode($product['id']))) ?>" class="inline-flex shrink-0 items-center gap-1 text-sm font-medium"><?= icon('pencil', 'h-4 w-4') ?> Ajustar</a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php admin_footer(); ?>
