<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require dirname(__DIR__) . '/includes/admin_layout.php';

admin_header('Categorías', 'categorias');
$products = products_all(false);
$counts = [];
foreach ($products as $product) {
    $slug = (string) $product['category'];
    $counts[$slug] = ($counts[$slug] ?? 0) + 1;
}
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <h1 class="text-2xl font-black tracking-tight">Categorías</h1>
    <p class="mt-1 text-sm text-slate-500">Las que aparecen en la tienda. Las de inicio se muestran en la portada.</p>
    <div class="mt-5 grid gap-3 sm:grid-cols-2">
        <?php foreach (categories_all() as $category): ?>
            <article class="flex gap-4 rounded-3xl border border-slate-100 p-3">
                <img src="<?= e(asset($category['image'])) ?>" alt="" class="h-20 w-20 shrink-0 rounded-2xl object-cover">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="font-semibold"><?= e($category['name']) ?></h2>
                        <?php if (!empty($category['home'])): ?>
                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-700">Inicio</span>
                        <?php endif; ?>
                    </div>
                    <p class="mt-1 text-sm text-slate-500"><?= e($category['tagline']) ?></p>
                    <p class="mt-2 text-xs font-medium text-slate-400"><?= e((string) ($counts[$category['slug']] ?? 0)) ?> prendas</p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php admin_footer(); ?>
