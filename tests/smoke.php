<?php

declare(strict_types=1);

$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
require dirname(__DIR__) . '/includes/bootstrap.php';

function check(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, "FAIL {$message}\n");
        exit(1);
    }
    echo "OK {$message}\n";
}

check(password_verify('nova123', (string) config('admin_password_hash')), 'admin password hash');
check(count(products_all()) >= 4, 'catalog has products');
check(product_find('polo-essential')['name'] === 'Polo Essential', 'find polo');
check((int) product_find('polo-essential')['stock'] >= 8, 'polo stock');
check(count(products_featured()) === 4, 'four featured products');
check(count(products_query('orbit')) === 1, 'search orbit');
check(count(home_categories()) === 3, 'home categories');
check(money(89) === 'S/ 89', 'money format');
check(slugify('Pantalón Neo') === 'pantalon-neo', 'slugify');
check(str_contains(whatsapp_link('Hola'), 'https://wa.me/'), 'whatsapp link');
check(icon('search') !== '' && icon('nope') === '', 'icons');
check(icon_whatsapp() !== '', 'whatsapp icon');

$result = cart_add('polo-essential', 'Arena', 'M', 2);
check($result['ok'] === true && $result['count'] === 2, 'add to cart');
$result = cart_add('polo-essential', 'Arena', 'M', 1);
check($result['count'] === 3 && cart_total() === 89 * 3, 'merge cart line');
$line = cart_lines()[0];
check(cart_update($line['key'], 1)['count'] === 1, 'update qty');
check(cart_remove($line['key'])['count'] === 0, 'remove line');
check(csrf_token() !== '' && hash_equals(csrf_token(), csrf_token()), 'csrf token');

$ordersBackup = is_file(orders_path()) ? file_get_contents(orders_path()) : '';
register_shutdown_function(static function () use ($ordersBackup): void {
    file_put_contents(orders_path(), $ordersBackup);
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate(orders_path(), true);
    }
});
try {
    $before = orders_unread_count();
    check(order_place('Ana', '987654321')['ok'] === false, 'reject empty checkout');
    cart_add('polo-essential', 'Arena', 'M', 1);
    check(order_place('A', '987654321')['ok'] === false, 'reject short name');
    check(order_place('Ana', '12')['ok'] === false, 'reject short phone');
    $_SESSION['nova_admin'] = 'admin';
    $adminOrder = order_place('Admin', '987654321');
    check($adminOrder['ok'] === true && $adminOrder['alert'] === false, 'admin checkout does not alert');
    check(orders_unread_count() === $before, 'admin checkout stores nothing');
    check(str_contains($adminOrder['whatsapp'], 'Nombre'), 'whatsapp includes name');
    unset($_SESSION['nova_admin']);
    $customerOrder = order_place('Lucía', '987 654 321');
    check($customerOrder['ok'] === true && $customerOrder['alert'] === true, 'customer checkout alerts');
    check(cart_count() === 0, 'cart clears after checkout');
    check(orders_unread_count() === $before + 1, 'one unread order');
    $stored = null;
    foreach (orders_unread() as $order) {
        if (($order['name'] ?? '') === 'Lucía') {
            $stored = $order;
        }
    }
    check(is_array($stored) && ($stored['total'] ?? 0) === 89, 'stored order');
    check(str_contains(order_summary($stored), 'Polo Essential'), 'order summary');
    order_mark_read((string) $stored['id']);
    check(orders_unread_count() === $before, 'order marked read');
} finally {
    file_put_contents(orders_path(), $ordersBackup);
    $_SESSION['nova_cart'] = [];
    unset($_SESSION['nova_admin']);
}

echo "smoke ok\n";
