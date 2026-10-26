<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    message_mark_read((string) ($_POST['id'] ?? ''));
    flash('success', 'Mensaje marcado como leído.');
    redirect('admin/mensajes.php');
}

require dirname(__DIR__) . '/includes/admin_layout.php';
admin_header('Mensajes', 'mensajes');
$messages = messages_all();
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <h1 class="text-2xl font-black tracking-tight">Mensajes</h1>
    <p class="mt-1 text-sm text-slate-500">Llegan desde el formulario de contacto. La hora está en Lima.</p>
    <?php if ($messages === []): ?>
        <div class="mt-8 rounded-3xl bg-slate-50 px-6 py-12 text-center">
            <span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-white text-slate-500"><?= icon('mail', 'h-5 w-5') ?></span>
            <p class="mt-3 text-sm text-slate-500">Todavía no hay mensajes.</p>
        </div>
    <?php else: ?>
        <div class="mt-5 space-y-3">
            <?php foreach ($messages as $message): ?>
                <article class="rounded-3xl border border-slate-100 p-4 <?= empty($message['read']) ? 'bg-orange-50/40' : '' ?>">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold"><?= e($message['name'] ?? '') ?></h2>
                            <p class="text-xs text-slate-500"><?= e($message['email'] ?? '') ?><?php if (($message['phone'] ?? '') !== ''): ?> · <?= e($message['phone']) ?><?php endif; ?></p>
                        </div>
                        <time class="text-xs text-slate-400"><?= e($message['created_at'] ?? '') ?> · Lima</time>
                    </div>
                    <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700"><?= e($message['message'] ?? '') ?></p>
                    <?php if (empty($message['read'])): ?>
                        <form method="post" class="mt-3">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= e($message['id'] ?? '') ?>">
                            <button type="submit" class="text-sm font-semibold text-nova">Marcar como leído</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php admin_footer(); ?>
