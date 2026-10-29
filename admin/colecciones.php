<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require dirname(__DIR__) . '/includes/admin_layout.php';

admin_header('Colecciones', 'colecciones');
$products = array_values(array_filter(
    products_all(false),
    static fn (array $product): bool => $product['featured']
));
usort($products, static fn (array $a, array $b): int => $a['featured_order'] <=> $b['featured_order']);
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <h1 class="text-2xl font-black tracking-tight">Colecciones</h1>
    <p class="mt-1 text-sm text-slate-500">Favoritos de la temporada. El orden es el de la portada.</p>
    <?php if ($products === []): ?>
        <p class="mt-8 text-sm text-slate-500">Todavía no hay prendas destacadas.</p>
    <?php else: ?>
        <div class="mt-5 grid gap-3 sm:grid-cols-2">
            <?php foreach ($products as $product): ?>
                <a href="<?= e(url('admin/producto.php?id=' . urlencode($product['id']))) ?>" class="flex items-center gap-4 rounded-3xl border border-slate-100 p-3 transition hover:bg-slate-50">
                    <img src="<?= e(asset($product['image'])) ?>" alt="" class="h-16 w-16 rounded-2xl bg-[#f3f1ee] object-cover">
                    <div class="min-w-0">
                        <p class="font-semibold"><?= e($product['name']) ?></p>
                        <p class="text-sm text-slate-500"><?= e(money($product['price'])) ?> · <?= (int) $product['stock'] > 0 ? e((string) $product['stock']) . ' en stock' : 'Agotado' ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php admin_footer(); ?>
