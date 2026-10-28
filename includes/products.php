<?php

declare(strict_types=1);

function products_path(): string
{
    return project_root() . '/data/products.json';
}

function categories_path(): string
{
    return project_root() . '/data/categories.json';
}

function categories_all(): array
{
    static $categories = null;
    if ($categories !== null) {
        return $categories;
    }

    $decoded = json_decode((string) file_get_contents(categories_path()), true);
    $categories = is_array($decoded) ? $decoded : [];

    return $categories;
}

function category_find(string $slug): ?array
{
    foreach (categories_all() as $category) {
        if (($category['slug'] ?? '') === $slug) {
            return $category;
        }
    }

    return null;
}

function home_categories(): array
{
    return array_values(array_filter(
        categories_all(),
        static fn (array $category): bool => !empty($category['home'])
    ));
}

function products_raw(): array
{
    $decoded = json_decode((string) file_get_contents(products_path()), true);

    return is_array($decoded) ? $decoded : [];
}

function product_normalize(array $product): array
{
    $colors = [];
    foreach ($product['colors'] ?? [] as $color) {
        if (!is_array($color)) {
            continue;
        }
        $colors[] = [
            'name' => (string) ($color['name'] ?? ''),
            'hex' => e_hex((string) ($color['hex'] ?? '')),
        ];
    }

    $sizes = array_values(array_filter(array_map('strval', $product['sizes'] ?? []), static fn (string $size): bool => $size !== ''));

    return [
        'id' => (string) ($product['id'] ?? ''),
        'name' => (string) ($product['name'] ?? ''),
        'price' => (int) ($product['price'] ?? 0),
        'stock' => max(0, (int) ($product['stock'] ?? 0)),
        'category' => (string) ($product['category'] ?? ''),
        'badge' => (string) ($product['badge'] ?? ''),
        'colors' => $colors,
        'sizes' => $sizes,
        'image' => (string) ($product['image'] ?? ''),
        'description' => (string) ($product['description'] ?? ''),
        'details' => array_values(array_map('strval', $product['details'] ?? [])),
        'featured' => !empty($product['featured']),
        'featured_order' => (int) ($product['featured_order'] ?? 0),
        'active' => array_key_exists('active', $product) ? (bool) $product['active'] : true,
        'fit' => ($product['fit'] ?? 'cover') === 'contain' ? 'contain' : 'cover',
        'position' => ($product['position'] ?? 'center') === 'top' ? 'top' : 'center',
    ];
}

function products_all(bool $onlyActive = true): array
{
    $products = array_map('product_normalize', products_raw());
    if ($onlyActive) {
        $products = array_values(array_filter($products, static fn (array $product): bool => $product['active']));
    }

    return $products;
}

function product_find(string $id, bool $onlyActive = true): ?array
{
    foreach (products_all($onlyActive) as $product) {
        if ($product['id'] === $id) {
            return $product;
        }
    }

    return null;
}

function products_featured(): array
{
    $products = array_values(array_filter(
        products_all(),
        static fn (array $product): bool => $product['featured']
    ));
    usort($products, static fn (array $a, array $b): int => $a['featured_order'] <=> $b['featured_order']);

    return $products;
}

function products_related(array $product, int $limit = 4): array
{
    $related = array_values(array_filter(
        products_all(),
        static fn (array $item): bool => $item['category'] === $product['category'] && $item['id'] !== $product['id']
    ));

    return array_slice($related, 0, $limit);
}

function products_query(string $q = '', string $category = '', string $sort = 'destacados'): array
{
    $q = trim($q);
    $needle = mb_strtolower($q);
    $products = products_all();

    if ($category !== '' && category_find($category)) {
        $products = array_values(array_filter(
            $products,
            static fn (array $product): bool => $product['category'] === $category
        ));
    }

    if ($needle !== '') {
        $products = array_values(array_filter($products, static function (array $product) use ($needle): bool {
            $category = category_find($product['category']);
            $haystack = mb_strtolower(implode(' ', [
                $product['name'],
                $product['description'],
                $product['category'],
                $category['name'] ?? '',
                $product['badge'],
            ]));

            return str_contains($haystack, $needle);
        }));
    }

    $sortBy = match ($sort) {
        'precio-asc' => static fn (array $a, array $b): int => $a['price'] <=> $b['price'],
        'precio-desc' => static fn (array $a, array $b): int => $b['price'] <=> $a['price'],
        'nombre' => static fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']),
        default => static function (array $a, array $b): int {
            $featured = ((int) $b['featured']) <=> ((int) $a['featured']);
            if ($featured !== 0) {
                return $featured;
            }
            if ($a['featured'] && $b['featured']) {
                return $a['featured_order'] <=> $b['featured_order'];
            }

            return strcasecmp($a['name'], $b['name']);
        },
    };
    usort($products, $sortBy);

    return $products;
}

function products_save_all(array $products): void
{
    $json = json_encode(array_values($products), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        throw new RuntimeException('No se pudo preparar el catálogo.');
    }

    $path = products_path();
    $handle = fopen($path, 'c+');
    if ($handle === false) {
        throw new RuntimeException('No se pudo guardar el catálogo.');
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            throw new RuntimeException('No se pudo bloquear el catálogo.');
        }
        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, $json . "\n");
        fflush($handle);
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

function parse_color_lines(string $raw): array|false
{
    $colors = [];
    foreach (preg_split('/\r\n|\r|\n/', $raw) ?: [] as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        [$name, $hex] = array_pad(explode('|', $line, 2), 2, '');
        $name = trim($name);
        $hex = strtoupper(trim($hex));
        if ($name === '' || !preg_match('/^#[0-9A-F]{6}$/', $hex)) {
            return false;
        }
        $colors[] = ['name' => $name, 'hex' => $hex];
    }

    return $colors;
}

function colors_to_text(array $colors): string
{
    $lines = [];
    foreach ($colors as $color) {
        $lines[] = ($color['name'] ?? '') . '|' . ($color['hex'] ?? '');
    }

    return implode("\n", $lines);
}

function product_from_request(array $post, array $files, ?array $current): array
{
    $errors = [];
    $name = trim((string) ($post['name'] ?? ''));
    $price = filter_var($post['price'] ?? null, FILTER_VALIDATE_INT);
    $stock = filter_var($post['stock'] ?? null, FILTER_VALIDATE_INT);
    $category = (string) ($post['category'] ?? '');
    $description = trim((string) ($post['description'] ?? ''));
    $badge = trim((string) ($post['badge'] ?? ''));
    $colors = parse_color_lines((string) ($post['colors'] ?? ''));
    $sizes = array_values(array_filter(array_map('trim', explode(',', (string) ($post['sizes'] ?? ''))), static fn (string $size): bool => $size !== ''));
    $details = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) ($post['details'] ?? '')) ?: []), static fn (string $line): bool => $line !== ''));
    $fit = (($post['fit'] ?? 'cover') === 'contain') ? 'contain' : 'cover';
    $position = (($post['position'] ?? 'center') === 'top') ? 'top' : 'center';

    if (mb_strlen($name) < 2 || mb_strlen($name) > 80) {
        $errors['name'] = 'El nombre debe tener entre 2 y 80 caracteres.';
    }
    if ($price === false || $price < 0 || $price > 99999) {
        $errors['price'] = 'Ingresa un precio entero válido.';
    }
    if ($stock === false || $stock < 0 || $stock > 9999) {
        $errors['stock'] = 'El stock debe ser un número entre 0 y 9999.';
    }
    if (!category_find($category)) {
        $errors['category'] = 'Elige una categoría.';
    }
    if (mb_strlen($description) < 10 || mb_strlen($description) > 800) {
        $errors['description'] = 'La descripción debe tener entre 10 y 800 caracteres.';
    }
    if (mb_strlen($badge) > 24) {
        $errors['badge'] = 'La etiqueta es demasiado larga.';
    }
    if ($colors === false || $colors === []) {
        $errors['colors'] = 'Cada color va en una línea: Nombre|#112233.';
        $colors = [];
    }
    if ($sizes === []) {
        $errors['sizes'] = 'Indica al menos una talla, separada por comas.';
    }

    $image = $current['image'] ?? '';
    $upload = $files['image'] ?? null;
    if (is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $stored = store_product_image($upload);
        if (isset($stored['error'])) {
            $errors['image'] = $stored['error'];
        } else {
            $image = $stored['path'];
        }
    }
    if ($image === '') {
        $errors['image'] = 'Sube una imagen del producto.';
    }

    $id = $current['id'] ?? slugify($name);
    if ($id === '') {
        $id = 'producto';
    }

    $product = [
        'id' => $id,
        'name' => $name,
        'price' => (int) ($price === false ? 0 : $price),
        'stock' => (int) ($stock === false ? 0 : $stock),
        'category' => $category,
        'badge' => $badge,
        'colors' => $colors,
        'sizes' => $sizes,
        'image' => $image,
        'description' => $description,
        'details' => $details,
        'featured' => isset($post['featured']),
        'featured_order' => max(0, (int) ($post['featured_order'] ?? 0)),
        'active' => isset($post['active']),
        'fit' => $fit,
        'position' => $position,
    ];

    return ['product' => $product, 'errors' => $errors];
}

function store_product_image(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['error' => 'No se pudo subir la imagen.'];
    }
    if (($file['size'] ?? 0) > 2_000_000) {
        return ['error' => 'La imagen supera 2 MB.'];
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']) ?: '';
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($extensions[$mime])) {
        return ['error' => 'Usa una imagen JPG, PNG o WebP.'];
    }

    $filename = bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
    $directory = project_root() . '/assets/img/uploads';
    if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
        return ['error' => 'No se pudo crear la carpeta de imágenes.'];
    }
    if (!move_uploaded_file((string) $file['tmp_name'], $directory . '/' . $filename)) {
        return ['error' => 'No se pudo guardar la imagen.'];
    }

    return ['path' => 'img/uploads/' . $filename];
}

function product_unique_id(string $base, array $products, ?string $ignore = null): string
{
    $id = $base !== '' ? $base : 'producto';
    $taken = [];
    foreach ($products as $product) {
        if (($product['id'] ?? '') !== $ignore) {
            $taken[$product['id']] = true;
        }
    }
    if (!isset($taken[$id])) {
        return $id;
    }

    $suffix = 2;
    while (isset($taken[$id . '-' . $suffix])) {
        $suffix++;
    }

    return $id . '-' . $suffix;
}

function product_save(array $product, ?string $originalId = null): void
{
    $products = products_raw();
    if ($originalId === null) {
        $product['id'] = product_unique_id($product['id'], $products);
        $products[] = $product;
    } else {
        $replaced = false;
        foreach ($products as $index => $current) {
            if (($current['id'] ?? '') === $originalId) {
                $product['id'] = $originalId;
                $products[$index] = $product;
                $replaced = true;
                break;
            }
        }
        if (!$replaced) {
            throw new RuntimeException('No encontramos el producto para actualizar.');
        }
    }

    products_save_all($products);
}

function product_delete(string $id): void
{
    $products = array_values(array_filter(
        products_raw(),
        static fn (array $product): bool => ($product['id'] ?? '') !== $id
    ));
    products_save_all($products);
}
