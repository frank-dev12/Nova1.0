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
    <div class="mt-5 grid grid-cols-1 gap-3 min-[770px]:grid-cols-2">
        <?php foreach ($products as $product): ?>
            <?php
            $category = category_find($product['category']);
            $stock = (int) $product['stock'];
            ?>
            <article class="flex h-full flex-col rounded-3xl border border-slate-100 p-3">
                <div class="flex gap-3">
                    <img src="<?= e(asset($product['image'])) ?>" alt="" class="h-24 w-20 shrink-0 rounded-2xl bg-[#f3f1ee] object-cover">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold"><?= e($product['name']) ?></p>
                        <?php if ($product['featured']): ?><p class="text-xs text-slate-400">Favorito</p><?php endif; ?>
                        <dl class="mt-2 grid grid-cols-2 gap-x-3 gap-y-1 text-sm">
                            <div>
                                <dt class="text-[11px] uppercase tracking-wide text-slate-400">Categoría</dt>
                                <dd><?= e($category['name'] ?? $product['category']) ?></dd>
                            </div>
                            <div>
                                <dt class="text-[11px] uppercase tracking-wide text-slate-400">Precio</dt>
                                <dd class="font-medium"><?= e(money($product['price'])) ?></dd>
                            </div>
                            <div>
                                <dt class="text-[11px] uppercase tracking-wide text-slate-400">Stock</dt>
                                <dd class="font-medium <?= $stock > 0 ? '' : 'text-rose-600' ?>"><?= $stock > 0 ? e((string) $stock) : 'Agotado' ?></dd>
                            </div>
                            <div>
                                <dt class="text-[11px] uppercase tracking-wide text-slate-400">Estado</dt>
                                <dd><?= $product['active'] ? 'Visible' : 'Oculto' ?></dd>
                            </div>
                        </dl>
                    </div>
                </div>
                <div class="mt-auto flex items-center justify-end gap-3 border-t border-slate-100 pt-3">
                    <a href="<?= e(url('admin/producto.php?id=' . urlencode($product['id']))) ?>" class="inline-flex items-center gap-1 text-sm font-medium text-ink"><?= icon('pencil', 'h-4 w-4') ?> Editar</a>
                    <form method="post" onsubmit="return confirm('¿Eliminar esta prenda?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= e($product['id']) ?>">
                        <button type="submit" class="inline-flex items-center gap-1 text-sm text-rose-600"><?= icon('trash-2', 'h-4 w-4') ?> Eliminar</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php admin_footer(); ?>
