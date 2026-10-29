<?php

declare(strict_types=1);

function config(?string $key = null): mixed
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/config.php';
    }

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? null;
}

function project_root(): string
{
    return dirname(__DIR__);
}

function base_path(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $doc = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ''), '/');
    $root = rtrim(str_replace('\\', '/', realpath(project_root()) ?: ''), '/');

    if ($doc !== '' && $root !== '' && str_starts_with($root, $doc)) {
        $base = substr($root, strlen($doc));
        return $base;
    }

    $base = '';
    return $base;
}

function url(string $path = ''): string
{
    $base = base_path();
    $path = ltrim($path, '/');
    $prefix = $base === '' ? '' : $base;

    if ($path === '') {
        return $prefix . '/';
    }

    return $prefix . '/' . $path;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function money(int|float $amount): string
{
    return 'S/ ' . number_format((float) $amount, 0, '.', ',');
}

function slugify(string $text): string
{
    $text = strtr($text, [
        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n', 'ü' => 'u',
        'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ñ' => 'n', 'Ü' => 'u',
    ]);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';

    return trim($text, '-');
}

function e_hex(string $hex): string
{
    return preg_match('/^#[0-9a-fA-F]{6}$/', $hex) ? $hex : '#cccccc';
}

function csrf_token(): string
{
    if (empty($_SESSION['nova_csrf']) || !is_string($_SESSION['nova_csrf'])) {
        $_SESSION['nova_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['nova_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $sent = $_POST['csrf'] ?? '';
    $valid = is_string($sent) && hash_equals(csrf_token(), $sent);
    if ($valid) {
        return;
    }

    http_response_code(419);
    exit('La sesión expiró. Vuelve a intentarlo.');
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['nova_flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['nova_flash'][$key] ?? null;
    unset($_SESSION['nova_flash'][$key]);

    return is_string($value) ? $value : null;
}

function whatsapp_link(string $text = ''): string
{
    $link = 'https://wa.me/' . rawurlencode((string) config('whatsapp'));
    if ($text !== '') {
        $link .= '?text=' . rawurlencode($text);
    }

    return $link;
}

function lima_now(): string
{
    return (new DateTimeImmutable('now', new DateTimeZone('America/Lima')))->format('Y-m-d H:i');
}

function render_flashes(): void
{
    $success = flash('success');
    $error = flash('error');

    if ($success) {
        echo '<div data-flash class="rounded-2xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">' . e($success) . '</div>';
    }
    if ($error) {
        echo '<div data-flash class="rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">' . e($error) . '</div>';
    }
}

function nav_items(): array
{
    return [
        'inicio' => ['Inicio', url('index.php')],
        'catalogo' => ['Catálogo', url('catalogo.php')],
        'nosotros' => ['Nosotros', url('nosotros.php')],
        'contacto' => ['Contacto', url('contacto.php')],
    ];
}

function render_product_card(array $product): void
{
    include __DIR__ . '/partials/product-card.php';
}
