<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require dirname(__DIR__) . '/includes/admin_layout.php';

admin_header('Promociones', 'promociones');
$products = array_values(array_filter(
    products_all(false),
    static fn (array $product): bool => trim((string) $product['badge']) !== ''
));
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <h1 class="text-2xl font-black tracking-tight">Promociones</h1>
    <p class="mt-1 text-sm text-slate-500">Prendas con etiqueta visible, como Nuevo. Se edita en cada producto.</p>
    <?php if ($products === []): ?>
        <p class="mt-8 text-sm text-slate-500">Ninguna prenda tiene etiqueta por ahora.</p>
    <?php else: ?>
        <div class="mt-5 grid gap-3">
            <?php foreach ($products as $product): ?>
                <a href="<?= e(url('admin/producto.php?id=' . urlencode($product['id']))) ?>" class="flex items-center justify-between gap-3 rounded-3xl border border-slate-100 p-3 transition hover:bg-slate-50">
                    <span class="flex min-w-0 items-center gap-3">
                        <img src="<?= e(asset($product['image'])) ?>" alt="" class="h-12 w-12 rounded-xl bg-[#f3f1ee] object-cover">
                        <span class="min-w-0">
                            <span class="block truncate font-semibold"><?= e($product['name']) ?></span>
                            <span class="text-sm text-slate-500"><?= e(money($product['price'])) ?></span>
                        </span>
                    </span>
                    <span class="shrink-0 rounded-full bg-ink px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-white"><?= e($product['badge']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php admin_footer(); ?>
