<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$q = trim((string) ($_GET['q'] ?? ''));
$categoria = trim((string) ($_GET['categoria'] ?? ''));
$orden = (string) ($_GET['orden'] ?? 'destacados');
$allowedSorts = ['destacados', 'precio-asc', 'precio-desc', 'nombre'];
if (!in_array($orden, $allowedSorts, true)) {
    $orden = 'destacados';
}
if ($categoria !== '' && !category_find($categoria)) {
    $categoria = '';
}

$products = products_query($q, $categoria, $orden);
$category = $categoria !== '' ? category_find($categoria) : null;
$pageTitle = $category ? $category['name'] : 'Catálogo';
$active = 'catalogo';
$searchQuery = $q;
$metaDescription = 'Explora polos, casacas, pantalones y hoodies de NØVA.';

require __DIR__ . '/includes/header.php';

function catalog_href(string $q, string $categoria, string $orden): string
{
    $params = [];
    if ($q !== '') {
        $params['q'] = $q;
    }
    if ($categoria !== '') {
        $params['categoria'] = $categoria;
    }
    if ($orden !== '' && $orden !== 'destacados') {
        $params['orden'] = $orden;
    }

    $query = http_build_query($params);

    return url('catalogo.php' . ($query !== '' ? '?' . $query : ''));
}
?>
<section class="rounded-[28px] bg-white p-4 shadow-[0_12px_30px_-24px_rgba(15,23,42,0.45)] sm:p-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Catálogo</p>
            <h1 class="mt-1 text-3xl font-black tracking-[-0.04em]"><?= e($category['name'] ?? 'Toda la colección') ?></h1>
            <p class="mt-1 text-sm text-slate-500">
                <?= e((string) count($products)) ?> <?= count($products) === 1 ? 'prenda' : 'prendas' ?>
                <?php if ($q !== ''): ?>
                    para “<?= e($q) ?>”
                <?php endif; ?>
            </p>
        </div>
        <form method="get" class="flex items-center gap-2">
            <?php if ($q !== ''): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
            <?php if ($categoria !== ''): ?><input type="hidden" name="categoria" value="<?= e($categoria) ?>"><?php endif; ?>
            <label class="sr-only" for="orden">Ordenar</label>
            <span class="text-slate-400"><?= icon('sliders-horizontal', 'h-4 w-4') ?></span>
            <select id="orden" name="orden" class="h-10 rounded-full border border-slate-200 bg-white px-3 text-sm outline-none focus:border-slate-400" onchange="this.form.requestSubmit()">
                <option value="destacados" <?= $orden === 'destacados' ? 'selected' : '' ?>>Destacados</option>
                <option value="precio-asc" <?= $orden === 'precio-asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
                <option value="precio-desc" <?= $orden === 'precio-desc' ? 'selected' : '' ?>>Precio: mayor a menor</option>
                <option value="nombre" <?= $orden === 'nombre' ? 'selected' : '' ?>>Nombre</option>
            </select>
            <button type="submit" class="sr-only">Ordenar</button>
        </form>
    </div>
    <div class="mt-5 flex flex-wrap gap-2">
        <a href="<?= e(catalog_href($q, '', $orden)) ?>" class="<?= $categoria === '' ? 'shrink-0 rounded-full bg-ink px-4 py-2 text-sm font-semibold text-white' : 'shrink-0 rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200' ?>">Todos</a>
        <?php foreach (categories_all() as $item): ?>
            <a href="<?= e(catalog_href($q, $item['slug'], $orden)) ?>" class="<?= $categoria === $item['slug'] ? 'shrink-0 rounded-full bg-ink px-4 py-2 text-sm font-semibold text-white' : 'shrink-0 rounded-full bg-slate-100 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200' ?>"><?= e($item['name']) ?></a>
        <?php endforeach; ?>
    </div>
    <?php if ($products === []): ?>
        <div class="mt-8 rounded-3xl bg-slate-50 px-6 py-14 text-center">
            <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-white text-slate-500"><?= icon('search', 'h-5 w-5') ?></span>
            <h2 class="mt-4 text-lg font-bold">No hay prendas con ese filtro</h2>
            <p class="mt-1 text-sm text-slate-500">Prueba con otro nombre o mira toda la colección.</p>
            <a href="<?= e(url('catalogo.php')) ?>" class="mt-5 inline-flex items-center gap-2 rounded-full bg-ink px-4 py-2.5 text-sm font-semibold text-white">Ver todo el catálogo</a>
        </div>
    <?php else: ?>
        <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4 lg:gap-4">
            <?php foreach ($products as $product): ?>
                <?php render_product_card($product); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
