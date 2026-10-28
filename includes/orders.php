<?php

declare(strict_types=1);

function orders_path(): string
{
    return project_root() . '/data/orders.php';
}

function orders_read_file(): array
{
    $path = orders_path();
    if (!is_file($path)) {
        return [];
    }
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate($path, true);
    }
    $loaded = require $path;

    return is_array($loaded) ? array_values($loaded) : [];
}

function &orders_bucket(): array
{
    static $items = null;
    if ($items === null) {
        $items = orders_read_file();
    }

    return $items;
}

function orders_save(): void
{
    $items = orders_bucket();
    $content = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . var_export(array_values($items), true) . ";\n";
    file_put_contents(orders_path(), $content, LOCK_EX);
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate(orders_path(), true);
    }
}

function orders_all(): array
{
    $items = orders_bucket();
    usort($items, static fn (array $a, array $b): int => strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? '')));

    return $items;
}

function orders_unread(): array
{
    return array_values(array_filter(orders_all(), static fn (array $order): bool => empty($order['read'])));
}

function orders_unread_count(): int
{
    return count(array_filter(orders_bucket(), static fn (array $order): bool => empty($order['read'])));
}

function order_summary(array $order): string
{
    $items = [];
    foreach ($order['lines'] ?? [] as $line) {
        if (!is_array($line)) {
            continue;
        }
        $items[] = trim((string) ($line['name'] ?? 'Prenda')) . ' x' . (int) ($line['qty'] ?? 0);
    }

    $text = trim((string) ($order['name'] ?? 'Cliente'))
        . ' · '
        . trim((string) ($order['phone'] ?? ''))
        . ' · '
        . implode(', ', $items)
        . ' · '
        . money((int) ($order['total'] ?? 0));

    if (mb_strlen($text) > 180) {
        return mb_substr($text, 0, 177) . '…';
    }

    return $text;
}

function order_create(string $name, string $phone, array $lines, int $total): string
{
    $items = &orders_bucket();
    $id = bin2hex(random_bytes(8));
    $items[] = [
        'id' => $id,
        'name' => $name,
        'phone' => $phone,
        'lines' => array_values($lines),
        'total' => $total,
        'read' => false,
        'created_at' => lima_now(),
    ];
    orders_save();

    return $id;
}

function order_mark_read(string $id): void
{
    $items = &orders_bucket();
    foreach ($items as $index => $order) {
        if (($order['id'] ?? '') === $id) {
            $items[$index]['read'] = true;
            orders_save();
            return;
        }
    }
}

function order_place(string $name, string $phone): array
{
    $name = trim(preg_replace('/\s+/u', ' ', $name) ?? '');
    $phone = trim($phone);
    $digits = preg_replace('/\D+/', '', $phone) ?? '';

    if (mb_strlen($name) < 2 || mb_strlen($name) > 80) {
        return ['ok' => false, 'message' => 'Escribe tu nombre para terminar el pedido.'];
    }
    if (strlen($digits) < 6 || strlen($digits) > 15) {
        return ['ok' => false, 'message' => 'Escribe un teléfono válido.'];
    }

    $lines = [];
    foreach (cart_lines() as $line) {
        if (!$line['available']) {
            continue;
        }
        $lines[] = [
            'name' => (string) $line['name'],
            'color' => (string) $line['color'],
            'size' => (string) $line['size'],
            'qty' => (int) $line['qty'],
            'subtotal' => (int) $line['subtotal'],
        ];
    }
    if ($lines === []) {
        return ['ok' => false, 'message' => 'El carrito está vacío.'];
    }

    $text = cart_whatsapp_text($name, $phone);
    $alert = !admin_logged_in();
    if ($alert) {
        order_create($name, $phone, $lines, cart_total());
        cart_clear();
    }

    return [
        'ok' => true,
        'alert' => $alert,
        'message' => $alert ? 'Pedido enviado al panel.' : 'El pedido de un administrador no avisa al panel.',
        'whatsapp' => whatsapp_link($text),
    ];
}

function render_alert_head(): void
{
    require __DIR__ . '/partials/alert-head.php';
}

function render_alert_gate(): void
{
    require __DIR__ . '/partials/alert-gate.php';
}
