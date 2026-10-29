<div class="nova-alert-gate" data-alert-gate>
    <div class="w-full max-w-md rounded-[28px] bg-white p-6 shadow-[0_24px_60px_-32px_rgba(15,23,42,0.7)]" role="dialog" aria-modal="true" aria-labelledby="nova-alert-title">
        <span class="grid h-12 w-12 place-items-center rounded-full bg-orange-50 text-nova"><?= icon('bell', 'h-5 w-5') ?></span>
        <h2 id="nova-alert-title" class="mt-4 text-xl font-black tracking-tight">Activa el tono de avisos</h2>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">Al entrar, NØVA pide permiso para las notificaciones de este teléfono o computadora. Guardamos tu respuesta en una cookie. Si aceptas, el aviso también aparece fuera del panel. Si no aceptas, el pedido igual entra a la lista y puede sonar el tono con esta pestaña abierta.</p>
        <p data-alert-status class="mt-3 hidden text-sm leading-relaxed text-rose-700" role="status"></p>
        <div class="mt-5 flex flex-col gap-2 sm:flex-row">
            <button type="button" data-alert-allow class="h-11 flex-1 rounded-full bg-nova text-sm font-semibold text-white">Permitir notificaciones</button>
            <button type="button" data-alert-deny class="h-11 flex-1 rounded-full bg-slate-100 text-sm font-semibold text-ink">No permitir</button>
        </div>
    </div>
</div>
