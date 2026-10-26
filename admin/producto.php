<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();

$id = trim((string) ($_GET['id'] ?? ''));
$current = $id !== '' ? product_find($id, false) : null;
if ($id !== '' && $current === null) {
    flash('error', 'No encontramos esa prenda.');
    redirect('admin/productos.php');
}

$errors = [];
$product = $current ?? [
    'name' => '',
    'price' => 0,
    'category' => 'polos',
    'badge' => '',
    'colors' => [],
    'sizes' => ['S', 'M', 'L', 'XL'],
    'image' => '',
    'description' => '',
    'details' => [],
    'featured' => false,
    'featured_order' => 0,
    'active' => true,
    'fit' => 'cover',
    'position' => 'center',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $parsed = product_from_request($_POST, $_FILES, $current);
    $product = array_merge($product, $parsed['product']);
    $errors = $parsed['errors'];
    if ($errors === []) {
        product_save($parsed['product'], $current['id'] ?? null);
        flash('success', $current ? 'Se actualizó la prenda.' : 'Se publicó la prenda.');
        redirect('admin/productos.php');
    }
}

require dirname(__DIR__) . '/includes/admin_layout.php';
admin_header($current ? 'Editar prenda' : 'Nueva prenda', 'productos');

$input = 'mt-1 h-11 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-slate-400';
?>
<form method="post" enctype="multipart/form-data" class="rounded-[28px] bg-white p-5 sm:p-6">
    <?= csrf_field() ?>
    <div class="flex items-center justify-between gap-3">
        <h1 class="text-2xl font-black tracking-tight"><?= $current ? 'Editar prenda' : 'Nueva prenda' ?></h1>
        <a href="<?= e(url('admin/productos.php')) ?>" class="text-sm font-medium text-slate-500">Volver</a>
    </div>
    <div class="mt-5 grid gap-4 md:grid-cols-2">
        <label class="block text-sm font-medium">
            Nombre
            <input name="name" value="<?= e($product['name']) ?>" class="<?= e($input) ?>">
            <?php if (isset($errors['name'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['name']) ?></span><?php endif; ?>
        </label>
        <label class="block text-sm font-medium">
            Precio (S/)
            <input type="number" name="price" min="0" step="1" value="<?= e((string) $product['price']) ?>" class="<?= e($input) ?>">
            <?php if (isset($errors['price'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['price']) ?></span><?php endif; ?>
        </label>
        <label class="block text-sm font-medium">
            Categoría
            <select name="category" class="<?= e($input) ?>">
                <?php foreach (categories_all() as $category): ?>
                    <option value="<?= e($category['slug']) ?>" <?= $product['category'] === $category['slug'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['category'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['category']) ?></span><?php endif; ?>
        </label>
        <label class="block text-sm font-medium">
            Etiqueta
            <input name="badge" value="<?= e($product['badge']) ?>" placeholder="Nuevo" class="<?= e($input) ?>">
            <?php if (isset($errors['badge'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['badge']) ?></span><?php endif; ?>
        </label>
        <label class="block text-sm font-medium md:col-span-2">
            Descripción
            <textarea name="description" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400"><?= e($product['description']) ?></textarea>
            <?php if (isset($errors['description'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['description']) ?></span><?php endif; ?>
        </label>
        <label class="block text-sm font-medium">
            Colores
            <textarea name="colors" rows="4" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 font-mono text-sm outline-none focus:border-slate-400"><?= e($_SERVER['REQUEST_METHOD'] === 'POST' ? (string) ($_POST['colors'] ?? '') : colors_to_text($product['colors'])) ?></textarea>
            <span class="mt-1 block text-xs text-slate-400">Una línea por color: Arena|#E4D5C1</span>
            <?php if (isset($errors['colors'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['colors']) ?></span><?php endif; ?>
        </label>
        <label class="block text-sm font-medium">
            Tallas
            <input name="sizes" value="<?= e($_SERVER['REQUEST_METHOD'] === 'POST' ? (string) ($_POST['sizes'] ?? '') : implode(', ', $product['sizes'])) ?>" class="<?= e($input) ?>">
            <span class="mt-1 block text-xs text-slate-400">Separadas por coma</span>
            <?php if (isset($errors['sizes'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['sizes']) ?></span><?php endif; ?>
        </label>
        <label class="block text-sm font-medium md:col-span-2">
            Detalles
            <textarea name="details" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-slate-400"><?= e(implode("\n", $product['details'])) ?></textarea>
            <span class="mt-1 block text-xs text-slate-400">Un detalle por línea</span>
        </label>
        <label class="block text-sm font-medium">
            Imagen
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm">
            <?php if ($product['image'] !== ''): ?>
                <img src="<?= e(asset($product['image'])) ?>" alt="" class="mt-3 h-28 w-24 rounded-2xl object-cover">
            <?php endif; ?>
            <?php if (isset($errors['image'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['image']) ?></span><?php endif; ?>
        </label>
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block text-sm font-medium">
                Recorte
                <select name="fit" class="<?= e($input) ?>">
                    <option value="cover" <?= $product['fit'] === 'cover' ? 'selected' : '' ?>>Llenar</option>
                    <option value="contain" <?= $product['fit'] === 'contain' ? 'selected' : '' ?>>Contener</option>
                </select>
            </label>
            <label class="block text-sm font-medium">
                Encuadre
                <select name="position" class="<?= e($input) ?>">
                    <option value="center" <?= $product['position'] === 'center' ? 'selected' : '' ?>>Centro</option>
                    <option value="top" <?= $product['position'] === 'top' ? 'selected' : '' ?>>Arriba</option>
                </select>
            </label>
            <label class="block text-sm font-medium">
                Orden en favoritos
                <input type="number" name="featured_order" min="0" value="<?= e((string) $product['featured_order']) ?>" class="<?= e($input) ?>">
            </label>
            <div class="flex flex-col justify-end gap-3 pb-2">
                <label class="inline-flex items-center gap-2 text-sm font-medium">
                    <input type="checkbox" name="featured" <?= $product['featured'] ? 'checked' : '' ?>> Favorito de la temporada
                </label>
                <label class="inline-flex items-center gap-2 text-sm font-medium">
                    <input type="checkbox" name="active" <?= $product['active'] ? 'checked' : '' ?>> Visible en la tienda
                </label>
            </div>
        </div>
    </div>
    <button type="submit" class="mt-6 inline-flex items-center gap-2 rounded-full bg-ink px-5 py-3 text-sm font-semibold text-white">
        <?= icon('check', 'h-4 w-4') ?>
        Guardar
    </button>
</form>
<?php admin_footer(); ?>
