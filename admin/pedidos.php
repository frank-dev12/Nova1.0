<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    order_mark_read((string) ($_POST['id'] ?? ''));
    flash('success', 'Pedido marcado como atendido.');
    redirect('admin/pedidos.php');
}

require dirname(__DIR__) . '/includes/admin_layout.php';
admin_header('Pedidos', 'pedidos');
$orders = orders_all();
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <h1 class="text-2xl font-black tracking-tight">Pedidos</h1>
    <p class="mt-1 text-sm text-slate-500">Llegan cuando alguien termina el carrito, también con la sesión de administrador abierta. La hora está en Lima. Con esta pestaña abierta suena el tono y el pedido entra a la lista. El aviso del sistema aparece si aceptaste las notificaciones.</p>
    <?php if ($orders === []): ?>
        <div class="mt-8 rounded-3xl bg-slate-50 px-6 py-12 text-center">
            <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-white text-slate-500"><?= icon('bell', 'h-5 w-5') ?></span>
            <p class="mt-3 text-sm text-slate-500">Todavía no hay pedidos.</p>
        </div>
    <?php else: ?>
        <div class="mt-5 space-y-3">
            <?php foreach ($orders as $order): ?>
                <?php $status = order_status($order); ?>
                <article class="rounded-3xl border border-slate-100 p-4 <?= $status === 'pendiente' ? 'bg-orange-50/40' : '' ?>">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold tracking-wide text-slate-400"><?= e((string) ($order['code'] ?? 'Sin código')) ?></p>
                            <h2 class="font-semibold"><?= e($order['name'] ?? '') ?></h2>
                            <p class="text-xs text-slate-500"><?= e($order['phone'] ?? '') ?></p>
                        </div>
                        <div class="text-right">
                            <?php if ($status === 'atendido'): ?>
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">Atendido</span>
                            <?php else: ?>
                                <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700">Pendiente</span>
                            <?php endif; ?>
                            <time class="mt-2 block text-xs text-slate-400"><?= e($order['created_at'] ?? '') ?> · Lima</time>
                        </div>
                    </div>
                    <ul class="mt-3 space-y-1 text-sm text-slate-700">
                        <?php foreach ($order['lines'] ?? [] as $line): ?>
                            <?php if (!is_array($line)) continue; ?>
                            <li><?= e((string) ($line['name'] ?? '')) ?> · <?= e((string) ($line['color'] ?? '')) ?> · Talla <?= e((string) ($line['size'] ?? '')) ?> · x<?= e((string) ($line['qty'] ?? 0)) ?> · <?= e(money((int) ($line['subtotal'] ?? 0))) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="mt-3 text-sm font-bold">Total <?= e(money((int) ($order['total'] ?? 0))) ?></p>
                    <?php if ($status === 'pendiente'): ?>
                        <form method="post" class="mt-3">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= e($order['id'] ?? '') ?>">
                            <button type="submit" class="text-sm font-semibold text-nova">Marcar como atendido</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php admin_footer(); ?>
