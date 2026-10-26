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

echo "smoke ok\n";
