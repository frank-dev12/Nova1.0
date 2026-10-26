<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$q = trim((string) ($_GET['q'] ?? ''));
$items = [];
if (mb_strlen($q) >= 2) {
    foreach (array_slice(products_query($q), 0, 6) as $product) {
        $items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => money($product['price']),
            'image' => asset($product['image']),
            'url' => url('producto.php?id=' . urlencode($product['id'])),
        ];
    }
}

echo json_encode(['items' => $items], JSON_UNESCAPED_UNICODE);
