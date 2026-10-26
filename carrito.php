<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $result = cart_handle($_POST);
    flash($result['ok'] ? 'success' : 'error', $result['message']);
    $next = (string) ($_POST['redirect'] ?? '');
    redirect($next === 'producto' ? 'producto.php?id=' . urlencode((string) ($_POST['id'] ?? '')) : 'carrito.php');
}

$pageTitle = 'Carrito';
$active = 'carrito';
$lines = cart_lines();
$total = cart_total();
require __DIR__ . '/includes/header.php';
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <div class="flex items-end justify-between gap-3 px-1">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Tu pedido</p>
            <h1 class="mt-1 text-3xl font-black tracking-[-0.04em]">Carrito</h1>
        </div>
        <p class="text-sm text-slate-500"><?= e((string) cart_count()) ?> <?= cart_count() === 1 ? 'prenda' : 'prendas' ?></p>
    </div>

    <?php if ($lines === []): ?>
        <div class="mt-8 rounded-3xl bg-slate-50 px-6 py-14 text-center">
            <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-white text-slate-500"><?= icon('shopping-bag', 'h-5 w-5') ?></span>
            <h2 class="mt-4 text-lg font-bold">Todavía no hay nada aquí</h2>
            <p class="mt-1 text-sm text-slate-500">Los favoritos de la temporada son un buen punto de partida.</p>
            <a href="<?= e(url('catalogo.php')) ?>" class="mt-5 inline-flex items-center gap-2 rounded-full bg-nova px-5 py-3 text-sm font-semibold text-white">
                Explorar catálogo
                <?= icon('arrow-right', 'h-4 w-4') ?>
            </a>
        </div>
    <?php else: ?>
        <div class="mt-6 grid gap-4 lg:grid-cols-[1.4fr_0.8fr]">
            <div class="space-y-3">
                <?php foreach ($lines as $line): ?>
                    <article class="flex gap-3 rounded-3xl border border-slate-100 p-3 sm:gap-4 sm:p-4">
                        <a href="<?= e(url('producto.php?id=' . urlencode($line['id']))) ?>" class="block h-24 w-20 shrink-0 overflow-hidden rounded-2xl bg-[#f3f1ee] sm:h-28 sm:w-24">
                            <?php if ($line['image'] !== ''): ?>
                                <img src="<?= e(asset($line['image'])) ?>" alt="" class="h-full w-full object-cover">
                            <?php endif; ?>
                        </a>
                        <div class="flex min-w-0 flex-1 flex-col">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="font-semibold">
                                        <?php if ($line['available']): ?>
                                            <a href="<?= e(url('producto.php?id=' . urlencode($line['id']))) ?>" class="hover:underline"><?= e($line['name']) ?></a>
                                        <?php else: ?>
                                            <?= e($line['name']) ?>
                                        <?php endif; ?>
                                    </h2>
                                    <p class="mt-0.5 text-xs text-slate-500"><?= e($line['color']) ?> · Talla <?= e($line['size']) ?></p>
                                </div>
                                <p class="shrink-0 text-sm font-bold"><?= $line['available'] ? e(money($line['subtotal'])) : '—' ?></p>
                            </div>
                            <div class="mt-auto flex items-center justify-between gap-3 pt-3">
                                <?php if ($line['available']): ?>
                                    <form method="post" action="<?= e(url('carrito.php')) ?>" data-cart-form data-reload="true" class="inline-flex items-center rounded-full border border-slate-200">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="key" value="<?= e($line['key']) ?>">
                                        <button type="button" data-qty-step="-1" class="grid h-9 w-9 place-items-center text-slate-600" aria-label="Restar"><?= icon('minus', 'h-4 w-4') ?></button>
                                        <input data-qty type="number" name="qty" min="0" max="8" value="<?= e((string) $line['qty']) ?>" class="h-9 w-10 border-0 bg-transparent text-center text-sm font-semibold outline-none">
                                        <button type="button" data-qty-step="1" class="grid h-9 w-9 place-items-center text-slate-600" aria-label="Sumar"><?= icon('plus', 'h-4 w-4') ?></button>
                                    </form>
                                <?php else: ?>
                                    <p class="text-xs text-rose-600">Ya no está disponible</p>
                                <?php endif; ?>
                                <form method="post" action="<?= e(url('carrito.php')) ?>" data-cart-form data-reload="true">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="key" value="<?= e($line['key']) ?>">
                                    <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-ink">
                                        <?= icon('trash-2', 'h-4 w-4') ?>
                                        Quitar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <aside class="h-fit rounded-3xl bg-slate-50 p-5">
                <h2 class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500">Resumen</h2>
                <div class="mt-4 flex items-center justify-between text-sm">
                    <span>Subtotal</span>
                    <span class="font-semibold"><?= e(money($total)) ?></span>
                </div>
                <p class="mt-3 text-xs leading-relaxed text-slate-500">El pedido se confirma por WhatsApp. Coordinamos talla, color y envío a todo el Perú. Aún no hay pago en línea.</p>
                <a href="<?= e(whatsapp_link(cart_whatsapp_text())) ?>" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-nova text-sm font-semibold text-white transition hover:brightness-95">
                    <?= icon_whatsapp('h-4 w-4') ?>
                    Pedir por WhatsApp
                </a>
                <a href="<?= e(url('catalogo.php')) ?>" class="mt-3 inline-flex h-11 w-full items-center justify-center text-sm font-semibold text-slate-700">Seguir comprando</a>
            </aside>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
