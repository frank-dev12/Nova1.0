<?php

declare(strict_types=1);

$href = url('producto.php?id=' . urlencode($product['id']));
$defaultColor = $product['colors'][0]['name'] ?? '';
$defaultSize = $product['sizes'][0] ?? '';
$fitClass = $product['fit'] === 'contain' ? 'object-contain p-3' : 'object-cover';
$positionClass = $product['position'] === 'top' ? 'object-top' : 'object-center';
?>
<article class="group flex flex-col rounded-[22px] bg-white p-2.5 shadow-[0_12px_30px_-22px_rgba(15,23,42,0.55)] transition duration-200 hover:-translate-y-0.5 sm:p-3">
    <div class="relative">
        <a href="<?= e($href) ?>" class="block overflow-hidden rounded-2xl bg-[#f3f1ee]">
            <img
                src="<?= e(asset($product['image'])) ?>"
                alt="<?= e($product['name']) ?>"
                class="aspect-square w-full <?= e($fitClass) ?> <?= e($positionClass) ?> transition duration-500 group-hover:scale-[1.03]"
                onerror="this.onerror=null;this.src='<?= e(asset('img/placeholder.svg')) ?>'"
            >
        </a>
        <?php if ($product['badge'] !== ''): ?>
            <span class="absolute left-2 top-2 rounded-full bg-ink px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.14em] text-white"><?= e($product['badge']) ?></span>
        <?php endif; ?>
        <button
            type="button"
            data-wishlist="<?= e($product['id']) ?>"
            class="absolute right-2 top-2 grid h-8 w-8 place-items-center rounded-full bg-white/90 text-slate-500 shadow-sm transition hover:text-ink"
            aria-pressed="false"
            aria-label="Guardar <?= e($product['name']) ?>"
        >
            <?= icon('heart', 'h-4 w-4') ?>
        </button>
    </div>
    <div class="mt-3 flex items-start justify-between gap-2 px-1 pb-1">
        <div class="min-w-0">
            <a href="<?= e($href) ?>" class="line-clamp-2 text-sm font-semibold leading-tight tracking-tight text-ink"><?= e($product['name']) ?></a>
            <p class="mt-0.5 text-sm font-bold"><?= e(money($product['price'])) ?></p>
            <div class="mt-2 flex gap-1.5">
                <?php foreach ($product['colors'] as $color): ?>
                    <span class="h-3.5 w-3.5 rounded-full border border-black/10" style="background: <?= e(e_hex($color['hex'])) ?>" title="<?= e($color['name']) ?>"></span>
                <?php endforeach; ?>
            </div>
        </div>
        <form method="post" action="<?= e(url('carrito.php')) ?>" data-cart-form>
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="<?= e($product['id']) ?>">
            <input type="hidden" name="color" value="<?= e($defaultColor) ?>">
            <input type="hidden" name="size" value="<?= e($defaultSize) ?>">
            <input type="hidden" name="qty" value="1">
            <button type="submit" class="grid h-10 w-10 place-items-center rounded-full bg-nova text-white shadow-[0_10px_18px_-10px_rgba(255,90,31,1)] transition hover:brightness-95" aria-label="Agregar <?= e($product['name']) ?> al carrito">
                <?= icon('plus', 'h-5 w-5') ?>
            </button>
        </form>
    </div>
</article>
