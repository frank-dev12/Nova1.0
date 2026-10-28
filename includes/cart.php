<?php

declare(strict_types=1);

function cart_bucket(): array
{
    if (!isset($_SESSION['nova_cart']) || !is_array($_SESSION['nova_cart'])) {
        $_SESSION['nova_cart'] = [];
    }

    return $_SESSION['nova_cart'];
}

function cart_key(string $id, string $color, string $size): string
{
    return substr(hash('sha256', $id . '|' . $color . '|' . $size), 0, 16);
}

function cart_count(): int
{
    $count = 0;
    foreach (cart_bucket() as $line) {
        $count += (int) ($line['qty'] ?? 0);
    }

    return $count;
}

function cart_lines(): array
{
    $lines = [];
    foreach (cart_bucket() as $key => $line) {
        $product = product_find((string) ($line['id'] ?? ''));
        $qty = max(0, (int) ($line['qty'] ?? 0));
        $available = $product !== null && $qty > 0;
        $lines[] = [
            'key' => (string) $key,
            'id' => (string) ($line['id'] ?? ''),
            'name' => $product['name'] ?? 'Producto no disponible',
            'color' => (string) ($line['color'] ?? ''),
            'size' => (string) ($line['size'] ?? ''),
            'qty' => $qty,
            'price' => $available ? $product['price'] : 0,
            'subtotal' => $available ? $product['price'] * $qty : 0,
            'image' => $product['image'] ?? '',
            'available' => $available,
        ];
    }

    return $lines;
}

function cart_total(): int
{
    $total = 0;
    foreach (cart_lines() as $line) {
        if ($line['available']) {
            $total += $line['subtotal'];
        }
    }

    return $total;
}

function cart_result(bool $ok, string $message): array
{
    return ['ok' => $ok, 'message' => $message, 'count' => cart_count()];
}

function cart_normalize_variant(array $product, string $color, string $size): array
{
    $colorNames = array_column($product['colors'], 'name');
    if (!in_array($color, $colorNames, true)) {
        $color = $colorNames[0] ?? '';
    }
    if (!in_array($size, $product['sizes'], true)) {
        $size = $product['sizes'][0] ?? '';
    }

    return [$color, $size];
}

function cart_held(string $id, ?string $exceptKey = null): int
{
    $held = 0;
    foreach (cart_bucket() as $key => $line) {
        if ($exceptKey !== null && (string) $key === $exceptKey) {
            continue;
        }
        if ((string) ($line['id'] ?? '') === $id) {
            $held += (int) ($line['qty'] ?? 0);
        }
    }

    return $held;
}

function cart_add(string $id, string $color, string $size, int $qty = 1): array
{
    $product = product_find($id);
    if ($product === null) {
        return cart_result(false, 'No encontramos ese producto.');
    }

    $qty = max(1, min(8, $qty));
    [$color, $size] = cart_normalize_variant($product, $color, $size);
    $key = cart_key($id, $color, $size);
    $cart = cart_bucket();
    $current = (int) ($cart[$key]['qty'] ?? 0);
    $room = max(0, (int) $product['stock'] - cart_held($id, $key));
    if ($room < 1) {
        return cart_result(false, 'No queda stock de esa prenda.');
    }
    $cart[$key] = [
        'id' => $id,
        'color' => $color,
        'size' => $size,
        'qty' => min(8, $current + $qty, $room),
    ];
    $_SESSION['nova_cart'] = $cart;

    return cart_result(true, $product['name'] . ' se agregó al carrito.');
}

function cart_update(string $key, int $qty): array
{
    $cart = cart_bucket();
    if (!isset($cart[$key])) {
        return cart_result(false, 'Ese producto ya no está en el carrito.');
    }
    if ($qty <= 0) {
        unset($cart[$key]);
        $_SESSION['nova_cart'] = $cart;
        return cart_result(true, 'Se quitó del carrito.');
    }

    $id = (string) ($cart[$key]['id'] ?? '');
    $product = product_find($id);
    $room = $product === null ? 0 : max(0, (int) $product['stock'] - cart_held($id, $key));
    if ($room < 1) {
        return cart_result(false, 'No queda stock de esa prenda.');
    }

    $cart[$key]['qty'] = min(8, $qty, $room);
    $_SESSION['nova_cart'] = $cart;

    return cart_result(true, 'Actualizamos la cantidad.');
}

function cart_remove(string $key): array
{
    $cart = cart_bucket();
    unset($cart[$key]);
    $_SESSION['nova_cart'] = $cart;

    return cart_result(true, 'Se quitó del carrito.');
}

function cart_clear(): array
{
    $_SESSION['nova_cart'] = [];

    return cart_result(true, 'El carrito quedó vacío.');
}

function cart_handle(array $post): array
{
    $action = (string) ($post['action'] ?? 'add');

    return match ($action) {
        'add' => cart_add(
            (string) ($post['id'] ?? ''),
            (string) ($post['color'] ?? ''),
            (string) ($post['size'] ?? ''),
            (int) ($post['qty'] ?? 1)
        ),
        'update' => cart_update((string) ($post['key'] ?? ''), (int) ($post['qty'] ?? 1)),
        'remove' => cart_remove((string) ($post['key'] ?? '')),
        'clear' => cart_clear(),
        default => cart_result(false, 'No pudimos procesar esa acción.'),
    };
}

function cart_whatsapp_text(string $name = '', string $phone = ''): string
{
    $parts = ['Hola NØVA, quiero hacer este pedido:'];
    if ($name !== '') {
        $parts[] = 'Nombre: ' . $name;
    }
    if ($phone !== '') {
        $parts[] = 'Teléfono: ' . $phone;
    }
    foreach (cart_lines() as $line) {
        if (!$line['available']) {
            continue;
        }
        $parts[] = sprintf(
            '- %s (%s / %s) x%d — %s',
            $line['name'],
            $line['color'],
            $line['size'],
            $line['qty'],
            money($line['subtotal'])
        );
    }
    $parts[] = 'Total: ' . money(cart_total());

    return implode("\n", $parts);
}
