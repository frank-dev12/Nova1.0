<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$errors = [];
$old = ['name' => '', 'email' => '', 'phone' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $old['name'] = trim((string) ($_POST['name'] ?? ''));
    $old['email'] = trim((string) ($_POST['email'] ?? ''));
    $old['phone'] = trim((string) ($_POST['phone'] ?? ''));
    $old['message'] = trim((string) ($_POST['message'] ?? ''));
    $honeypot = trim((string) ($_POST['empresa'] ?? ''));

    if ($honeypot !== '') {
        flash('success', 'Recibimos tu mensaje. Te respondemos pronto.');
        redirect('contacto.php');
    }
    if (mb_strlen($old['name']) < 2 || mb_strlen($old['name']) > 80) {
        $errors['name'] = 'Cuéntanos cómo te llamas.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Revisa el correo.';
    }
    if (mb_strlen($old['phone']) > 20) {
        $errors['phone'] = 'Ese teléfono es demasiado largo.';
    }
    if (mb_strlen($old['message']) < 10 || mb_strlen($old['message']) > 1000) {
        $errors['message'] = 'El mensaje necesita entre 10 y 1000 caracteres.';
    }

    if ($errors === []) {
        message_create($old['name'], $old['email'], $old['phone'], $old['message']);
        flash('success', 'Recibimos tu mensaje. Te respondemos pronto.');
        redirect('contacto.php');
    }
}

$pageTitle = 'Contacto';
$active = 'contacto';
$metaDescription = 'Escríbele a NØVA o pide por WhatsApp. Atención desde Lima para todo el Perú.';
require __DIR__ . '/includes/header.php';

function field_class(array $errors, string $key): string
{
    return isset($errors[$key])
        ? 'mt-1 w-full rounded-2xl border border-rose-300 bg-white px-4 py-3 text-sm outline-none focus:border-rose-400'
        : 'mt-1 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none focus:border-slate-400';
}
?>
<section class="grid gap-4 lg:grid-cols-[0.9fr_1.1fr]">
    <div class="rounded-[28px] bg-ink p-6 text-white sm:p-8">
        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-white/50">Contacto</p>
        <h1 class="mt-3 text-4xl font-black tracking-[-0.045em]">Estamos para ayudarte.</h1>
        <p class="mt-4 text-sm leading-relaxed text-white/75">Tallas, stock, cambios o un pedido completo. Respondemos desde <?= e((string) config('city')) ?>.</p>
        <a href="<?= e(whatsapp_link('Hola NØVA, tengo una consulta.')) ?>" target="_blank" rel="noopener noreferrer" class="mt-6 inline-flex items-center gap-2 rounded-full bg-white px-4 py-3 text-sm font-semibold text-ink">
            <?= icon_whatsapp('h-4 w-4') ?>
            WhatsApp <?= e((string) config('whatsapp_display')) ?>
        </a>
        <ul class="mt-8 space-y-4 text-sm text-white/80">
            <li class="flex items-center gap-3"><?= icon('map-pin', 'h-4 w-4') ?> <?= e((string) config('city')) ?></li>
            <li class="flex items-center gap-3"><?= icon('truck', 'h-4 w-4') ?> Envíos a todo el Perú</li>
            <li class="flex items-center gap-3"><?= icon('message-circle', 'h-4 w-4') ?> Pedidos coordinados por chat</li>
        </ul>
    </div>
    <form method="post" class="relative rounded-[28px] bg-white p-6 sm:p-8" novalidate>
        <?= csrf_field() ?>
        <div class="absolute -left-[9999px]" aria-hidden="true">
            <label>Empresa <input type="text" name="empresa" tabindex="-1" autocomplete="off"></label>
        </div>
        <h2 class="text-xl font-bold">Escríbenos</h2>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <label class="block text-sm font-medium">
                Nombre
                <input name="name" value="<?= e($old['name']) ?>" required class="<?= e(field_class($errors, 'name')) ?>">
                <?php if (isset($errors['name'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['name']) ?></span><?php endif; ?>
            </label>
            <label class="block text-sm font-medium">
                Correo
                <input type="email" name="email" value="<?= e($old['email']) ?>" required class="<?= e(field_class($errors, 'email')) ?>">
                <?php if (isset($errors['email'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['email']) ?></span><?php endif; ?>
            </label>
        </div>
        <label class="mt-4 block text-sm font-medium">
            Teléfono <span class="font-normal text-slate-400">(opcional)</span>
            <input name="phone" value="<?= e($old['phone']) ?>" class="<?= e(field_class($errors, 'phone')) ?>">
            <?php if (isset($errors['phone'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['phone']) ?></span><?php endif; ?>
        </label>
        <label class="mt-4 block text-sm font-medium">
            Mensaje
            <textarea name="message" rows="5" required class="<?= e(field_class($errors, 'message')) ?>"><?= e($old['message']) ?></textarea>
            <?php if (isset($errors['message'])): ?><span class="mt-1 block text-xs text-rose-600"><?= e($errors['message']) ?></span><?php endif; ?>
        </label>
        <button type="submit" class="mt-5 inline-flex items-center gap-2 rounded-full bg-nova px-5 py-3 text-sm font-semibold text-white transition hover:brightness-95">
            <?= icon('mail', 'h-4 w-4') ?>
            Enviar mensaje
        </button>
    </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
