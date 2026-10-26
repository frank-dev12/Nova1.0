<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';

if (admin_logged_in()) {
    redirect('admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $user = trim((string) ($_POST['user'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    if (attempt_login($user, $password)) {
        redirect('admin/index.php');
    }
    $error = 'Usuario o contraseña incorrectos.';
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Entrar · Panel NØVA</title>
    <link rel="icon" href="<?= e(asset('favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="grid min-h-screen place-items-center bg-[radial-gradient(900px_400px_at_100%_0%,#d7ecf8,transparent_55%),linear-gradient(180deg,#eef6fb,#f7fafc)] px-4 font-sans text-ink">
    <form method="post" class="w-full max-w-sm rounded-[28px] bg-white p-6 shadow-[0_20px_50px_-32px_rgba(15,23,42,0.6)] sm:p-8">
        <?= csrf_field() ?>
        <a href="<?= e(url('index.php')) ?>" class="text-2xl font-black tracking-[-0.06em]">NØVA</a>
        <h1 class="mt-6 text-xl font-bold">Panel</h1>
        <p class="mt-1 text-sm text-slate-500">Entra para editar el catálogo y leer mensajes.</p>
        <?php if ($error !== ''): ?>
            <p class="mt-4 rounded-2xl bg-rose-50 px-3 py-2 text-sm text-rose-700" role="alert"><?= e($error) ?></p>
        <?php endif; ?>
        <label class="mt-5 block text-sm font-medium">
            Usuario
            <input name="user" autocomplete="username" required class="mt-1 h-11 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-slate-400">
        </label>
        <label class="mt-3 block text-sm font-medium">
            Contraseña
            <input type="password" name="password" autocomplete="current-password" required class="mt-1 h-11 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none focus:border-slate-400">
        </label>
        <button type="submit" class="mt-5 h-11 w-full rounded-full bg-nova text-sm font-semibold text-white">Entrar</button>
    </form>
</body>
</html>
