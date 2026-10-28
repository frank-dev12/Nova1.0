<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require dirname(__DIR__) . '/includes/admin_layout.php';

admin_header('Colores', 'colores');
$colors = [];
foreach (products_all(false) as $product) {
    foreach ($product['colors'] as $color) {
        $key = $color['name'] . '|' . $color['hex'];
        if (!isset($colors[$key])) {
            $colors[$key] = ['name' => $color['name'], 'hex' => $color['hex'], 'count' => 0];
        }
        $colors[$key]['count']++;
    }
}
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <h1 class="text-2xl font-black tracking-tight">Colores</h1>
    <p class="mt-1 text-sm text-slate-500">Colores definidos en las prendas, con su muestra.</p>
    <?php if ($colors === []): ?>
        <p class="mt-8 text-sm text-slate-500">Todavía no hay colores.</p>
    <?php else: ?>
        <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($colors as $color): ?>
                <article class="flex items-center gap-3 rounded-3xl border border-slate-100 p-3">
                    <span class="h-12 w-12 shrink-0 rounded-2xl border border-black/10" style="background: <?= e(e_hex($color['hex'])) ?>"></span>
                    <div>
                        <p class="font-semibold"><?= e($color['name']) ?></p>
                        <p class="text-xs text-slate-400"><?= e($color['hex']) ?> · <?= e((string) $color['count']) ?> <?= $color['count'] === 1 ? 'prenda' : 'prendas' ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php admin_footer(); ?>
