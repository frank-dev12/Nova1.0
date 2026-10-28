<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!admin_logged_in()) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'Sin permiso.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$orders = [];
foreach (array_slice(orders_unread(), 0, 20) as $order) {
    $orders[] = [
        'id' => (string) ($order['id'] ?? ''),
        'summary' => order_summary($order),
        'created_at' => (string) ($order['created_at'] ?? ''),
    ];
}

echo json_encode([
    'ok' => true,
    'unread' => orders_unread_count(),
    'orders' => $orders,
], JSON_UNESCAPED_UNICODE);
