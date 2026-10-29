<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/bootstrap.php';
require_admin();
require dirname(__DIR__) . '/includes/admin_layout.php';

admin_header('Configuración', 'configuracion');
$rows = [
    ['Tienda', (string) config('name')],
    ['WhatsApp', (string) config('whatsapp_display')],
    ['Ciudad', (string) config('city')],
    ['Usuario del panel', (string) config('admin_user')],
];
?>
<section class="rounded-[28px] bg-white p-4 sm:p-6">
    <h1 class="text-2xl font-black tracking-tight">Configuración</h1>
    <p class="mt-1 text-sm text-slate-500">Datos de la tienda. Para cambiarlos edita <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">includes/config.php</code>.</p>
    <dl class="mt-5 divide-y divide-slate-100">
        <?php foreach ($rows as [$label, $value]): ?>
            <div class="flex flex-wrap items-center justify-between gap-2 py-3">
                <dt class="text-sm text-slate-500"><?= e($label) ?></dt>
                <dd class="text-sm font-semibold"><?= e($value) ?></dd>
            </div>
        <?php endforeach; ?>
    </dl>
</section>
<?php admin_footer(); ?>
