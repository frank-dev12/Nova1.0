<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Método no permitido.', 'count' => cart_count()], JSON_UNESCAPED_UNICODE);
    exit;
}

$sent = $_POST['csrf'] ?? '';
if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
    http_response_code(419);
    echo json_encode(['ok' => false, 'message' => 'La sesión expiró. Recarga la página.', 'count' => cart_count()], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(cart_handle($_POST), JSON_UNESCAPED_UNICODE);
