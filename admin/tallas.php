<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require dirname(__DIR__) . '/includes/admin_layout.php';

admin_header('Tallas', 'tallas');
$sizes = [];
foreach (products_all(false) as $product) {
    foreach ($product['sizes'] as $size) {
        $sizes[$size] = ($sizes[$size] ?? 0) + 1;
    }
}
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <h1 class="text-2xl font-black tracking-tight">Tallas</h1>
    <p class="mt-1 text-sm text-slate-500">Tallas que ya usan las prendas del catálogo.</p>
    <?php if ($sizes === []): ?>
        <p class="mt-8 text-sm text-slate-500">Todavía no hay tallas.</p>
    <?php else: ?>
        <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <?php foreach ($sizes as $size => $count): ?>
                <article class="rounded-3xl bg-slate-50 p-4">
                    <p class="text-2xl font-black tracking-tight"><?= e((string) $size) ?></p>
                    <p class="mt-1 text-sm text-slate-500"><?= e((string) $count) ?> <?= $count === 1 ? 'prenda' : 'prendas' ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php admin_footer(); ?>
