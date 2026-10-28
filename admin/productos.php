<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (string) ($_POST['id'] ?? '');
    if ($id !== '' && product_find($id, false)) {
        product_delete($id);
        flash('success', 'Se eliminó el producto.');
    }
    redirect('admin/productos.php');
}

require dirname(__DIR__) . '/includes/admin_layout.php';
admin_header('Productos', 'productos');
$products = products_all(false);
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black tracking-tight">Productos</h1>
            <p class="text-sm text-slate-500"><?= e((string) count($products)) ?> en el catálogo</p>
        </div>
        <a href="<?= e(url('admin/producto.php')) ?>" class="inline-flex items-center gap-2 rounded-full bg-nova px-4 py-2.5 text-sm font-semibold text-white">
            <?= icon('plus', 'h-4 w-4') ?>
            Nueva prenda
        </a>
    </div>
    <div class="mt-5 overflow-x-auto">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="text-xs uppercase tracking-wide text-slate-400">
                <tr>
                    <th class="px-2 py-2 font-medium">Prenda</th>
                    <th class="px-2 py-2 font-medium">Categoría</th>
                    <th class="px-2 py-2 font-medium">Precio</th>
                    <th class="px-2 py-2 font-medium">Stock</th>
                    <th class="px-2 py-2 font-medium">Estado</th>
                    <th class="px-2 py-2 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <?php $category = category_find($product['category']); ?>
                    <tr class="border-t border-slate-100">
                        <td class="px-2 py-3">
                            <div class="flex items-center gap-3">
                                <img src="<?= e(asset($product['image'])) ?>" alt="" class="h-12 w-12 rounded-xl bg-[#f3f1ee] object-cover">
                                <div>
                                    <p class="font-semibold"><?= e($product['name']) ?></p>
                                    <?php if ($product['featured']): ?><p class="text-xs text-slate-400">Favorito</p><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-2 py-3"><?= e($category['name'] ?? $product['category']) ?></td>
                        <td class="px-2 py-3 font-medium"><?= e(money($product['price'])) ?></td>
                        <td class="px-2 py-3 font-medium <?= (int) $product['stock'] > 0 ? '' : 'text-rose-600' ?>"><?= (int) $product['stock'] > 0 ? e((string) $product['stock']) : 'Agotado' ?></td>
                        <td class="px-2 py-3"><?= $product['active'] ? 'Visible' : 'Oculto' ?></td>
                        <td class="px-2 py-3">
                            <div class="flex items-center justify-end gap-3">
                                <a href="<?= e(url('admin/producto.php?id=' . urlencode($product['id']))) ?>" class="inline-flex items-center gap-1 font-medium text-ink"><?= icon('pencil', 'h-4 w-4') ?> Editar</a>
                                <form method="post" onsubmit="return confirm('¿Eliminar esta prenda?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="id" value="<?= e($product['id']) ?>">
                                    <button type="submit" class="inline-flex items-center gap-1 text-rose-600"><?= icon('trash-2', 'h-4 w-4') ?> Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php admin_footer(); ?>
