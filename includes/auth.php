<?php

declare(strict_types=1);

function admin_logged_in(): bool
{
    return isset($_SESSION['nova_admin']) && is_string($_SESSION['nova_admin']) && $_SESSION['nova_admin'] !== '';
}

function attempt_login(string $user, string $password): bool
{
    $hash = (string) config('admin_password_hash');
    $validPassword = password_verify($password, $hash);
    $validUser = hash_equals((string) config('admin_user'), $user);

    if (!$validUser || !$validPassword) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['nova_admin'] = $user;

    return true;
}

function logout_admin(): void
{
    unset($_SESSION['nova_admin']);
    session_regenerate_id(true);
}

function require_admin(): void
{
    if (admin_logged_in()) {
        return;
    }

    redirect('admin/login.php');
}
