<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$id = (string) ($_GET['id'] ?? '');
$product = product_find($id);
if ($product === null) {
    http_response_code(404);
    $pageTitle = 'Producto no encontrado';
    $active = 'catalogo';
    require __DIR__ . '/includes/header.php';
    ?>
    <section class="rounded-[28px] bg-white px-6 py-16 text-center">
        <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-slate-50 text-slate-500"><?= icon('package', 'h-5 w-5') ?></span>
        <h1 class="mt-4 text-2xl font-black">Esa prenda ya no está</h1>
        <p class="mt-2 text-sm text-slate-500">Puede que haya salido del catálogo.</p>
        <a href="<?= e(url('catalogo.php')) ?>" class="mt-5 inline-flex items-center gap-2 rounded-full bg-ink px-4 py-2.5 text-sm font-semibold text-white">Volver al catálogo</a>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$category = category_find($product['category']);
$pageTitle = $product['name'];
$active = 'catalogo';
$metaDescription = $product['description'];
$defaultSize = $product['sizes'][0] ?? '';
$waText = sprintf(
    "Hola NØVA, me interesa %s (%s). ¿Está disponible?",
    $product['name'],
    money($product['price'])
);
require __DIR__ . '/includes/header.php';
?>
<article class="grid gap-4 lg:grid-cols-[1.05fr_0.95fr]">
    <div class="overflow-hidden rounded-[28px] bg-[#f3f1ee]">
        <img
            src="<?= e(asset($product['image'])) ?>"
            alt="<?= e($product['name']) ?>"
            class="aspect-[4/5] w-full sm:aspect-square <?= $product['fit'] === 'contain' ? 'object-contain p-6' : 'object-cover' ?> <?= $product['position'] === 'top' ? 'object-top' : 'object-center' ?>"
            onerror="this.onerror=null;this.src='<?= e(asset('img/placeholder.svg')) ?>'"
        >
    </div>
    <div class="rounded-[28px] bg-white p-5 sm:p-8">
        <p class="text-xs font-medium text-slate-500">
            <a href="<?= e(url('index.php')) ?>" class="hover:text-ink">Inicio</a>
            <span class="px-1 text-slate-300">/</span>
            <a href="<?= e(url('catalogo.php')) ?>" class="hover:text-ink">Catálogo</a>
            <?php if ($category): ?>
                <span class="px-1 text-slate-300">/</span>
                <a href="<?= e(url('catalogo.php?categoria=' . urlencode($category['slug']))) ?>" class="hover:text-ink"><?= e($category['name']) ?></a>
            <?php endif; ?>
        </p>
        <?php if ($product['badge'] !== ''): ?>
            <span class="mt-4 inline-flex rounded-full bg-ink px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-white"><?= e($product['badge']) ?></span>
        <?php endif; ?>
        <h1 class="mt-3 text-3xl font-black tracking-[-0.04em] sm:text-4xl"><?= e($product['name']) ?></h1>
        <p class="mt-2 text-2xl font-bold"><?= e(money($product['price'])) ?></p>
        <p class="mt-4 max-w-md text-sm leading-relaxed text-slate-600"><?= e($product['description']) ?></p>

        <form method="post" action="<?= e(url('carrito.php')) ?>" data-cart-form class="mt-6 space-y-5">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="<?= e($product['id']) ?>">
            <fieldset>
                <legend class="text-sm font-semibold">Color</legend>
                <div class="mt-2 flex flex-wrap gap-2">
                    <?php foreach ($product['colors'] as $index => $color): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="color" value="<?= e($color['name']) ?>" class="peer sr-only" <?= $index === 0 ? 'checked' : '' ?>>
                            <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1.5 text-sm peer-checked:border-ink peer-checked:bg-slate-50">
                                <span class="h-3.5 w-3.5 rounded-full border border-black/10" style="background: <?= e(e_hex($color['hex'])) ?>"></span>
                                <?= e($color['name']) ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <fieldset>
                <legend class="text-sm font-semibold">Talla</legend>
                <div class="mt-2 flex flex-wrap gap-2">
                    <?php foreach ($product['sizes'] as $index => $size): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="size" value="<?= e($size) ?>" class="peer sr-only" <?= $index === 0 ? 'checked' : '' ?>>
                            <span class="inline-flex min-w-11 items-center justify-center rounded-full border border-slate-200 px-3 py-1.5 text-sm font-medium peer-checked:border-ink peer-checked:bg-ink peer-checked:text-white"><?= e($size) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </fieldset>
            <label class="block text-sm font-semibold">
                Cantidad
                <input type="number" name="qty" value="1" min="1" max="8" class="mt-2 h-11 w-24 rounded-full border border-slate-200 px-4 text-sm font-medium outline-none focus:border-slate-400">
            </label>
            <div class="flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="inline-flex h-12 items-center justify-center gap-2 rounded-full bg-nova px-6 text-sm font-semibold text-white transition hover:brightness-95">
                    <?= icon('plus', 'h-4 w-4') ?>
                    Agregar al carrito
                </button>
                <a href="<?= e(whatsapp_link($waText)) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex h-12 items-center justify-center gap-2 rounded-full border border-slate-200 px-6 text-sm font-semibold transition hover:bg-slate-50">
                    <?= icon_whatsapp('h-4 w-4') ?>
                    Pedir por WhatsApp
                </a>
            </div>
        </form>

        <?php if ($product['details'] !== []): ?>
            <ul class="mt-6 space-y-2 border-t border-slate-100 pt-5 text-sm text-slate-600">
                <?php foreach ($product['details'] as $detail): ?>
                    <li class="flex items-start gap-2">
                        <span class="mt-0.5 text-nova"><?= icon('check', 'h-4 w-4') ?></span>
                        <span><?= e($detail) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</article>

<?php $related = products_related($product); ?>
<?php if ($related !== []): ?>
    <section class="rounded-[28px] bg-white p-4 sm:p-6">
        <h2 class="px-1 text-xl font-black tracking-tight">También te puede ir</h2>
        <div class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">
            <?php foreach ($related as $item): ?>
                <?php render_product_card($item); ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
