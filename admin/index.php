<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require dirname(__DIR__) . '/includes/admin_layout.php';

admin_header('Resumen', 'resumen');
$orders = array_slice(orders_all(), 0, 6);
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Hoy</p>
    <h1 class="mt-1 text-2xl font-black tracking-[-0.04em] sm:text-3xl">Resumen</h1>
    <p class="mt-2 text-sm leading-relaxed text-slate-500">Los pedidos quedan guardados aquí y después se abren en WhatsApp. La hora está en Lima.</p>
</section>
<section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
    <article class="min-w-0 rounded-[24px] bg-white p-4 sm:p-5">
        <p class="text-sm text-slate-500">Pedidos hoy</p>
        <p class="mt-2 text-2xl font-black sm:text-3xl" data-orders-today><?= e((string) orders_today_count()) ?></p>
    </article>
    <article class="min-w-0 rounded-[24px] bg-white p-4 sm:p-5">
        <p class="text-sm text-slate-500">Ingresos</p>
        <p class="mt-2 text-2xl font-black sm:text-3xl" data-orders-revenue><?= e(money(orders_today_total())) ?></p>
    </article>
    <article class="min-w-0 rounded-[24px] bg-white p-4 sm:p-5">
        <p class="text-sm text-slate-500">Mensajes</p>
        <p class="mt-2 text-2xl font-black sm:text-3xl"><?= e((string) messages_unread_count()) ?></p>
        <a href="<?= e(url('admin/mensajes.php')) ?>" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-nova">Ver mensajes <?= icon('arrow-right', 'h-4 w-4') ?></a>
    </article>
</section>
<section class="rounded-[24px] bg-white p-4 sm:p-5">
    <div class="flex items-center justify-between gap-3">
        <h2 class="font-bold">Últimos pedidos</h2>
        <a href="<?= e(url('admin/pedidos.php')) ?>" class="text-sm font-semibold text-nova">Ver todos</a>
    </div>
    <?php if ($orders === []): ?>
        <p class="mt-4 text-sm text-slate-500">Todavía no hay pedidos.</p>
    <?php else: ?>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[520px] text-left text-sm">
                <thead class="text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-2 py-2 font-medium">Código</th>
                        <th class="px-2 py-2 font-medium">Cliente</th>
                        <th class="px-2 py-2 font-medium">Total</th>
                        <th class="px-2 py-2 font-medium">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <?php $status = order_status($order); ?>
                        <tr class="border-t border-slate-100">
                            <td class="px-2 py-3 font-semibold"><?= e((string) ($order['code'] ?? '—')) ?></td>
                            <td class="px-2 py-3"><?= e((string) ($order['name'] ?? '')) ?></td>
                            <td class="px-2 py-3"><?= e(money((int) ($order['total'] ?? 0))) ?></td>
                            <td class="px-2 py-3">
                                <?php if ($status === 'atendido'): ?>
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">Atendido</span>
                                <?php else: ?>
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700">Pendiente</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php admin_footer(); ?>
