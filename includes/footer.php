        </main>
        </div>
        <?php
        $socials = [
            ['TikTok', trim((string) config('tiktok')), icon_tiktok('h-5 w-5')],
            ['Facebook', trim((string) config('facebook')), icon_facebook('h-5 w-5')],
            ['WhatsApp', whatsapp_link('Hola NØVA, quiero hacer un pedido.'), icon_whatsapp('h-5 w-5')],
        ];
        ?>
        <footer class="bg-black text-white">
            <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-8 sm:px-6">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:gap-6">
                        <a href="<?= e(url('index.php')) ?>" class="text-lg font-black tracking-[-0.06em] text-white">NØVA</a>
                        <nav class="flex flex-wrap gap-x-4 gap-y-2 text-sm text-white/70" aria-label="Pie">
                            <a href="<?= e(url('index.php')) ?>" class="transition hover:text-white">Inicio</a>
                            <a href="<?= e(url('catalogo.php')) ?>" class="transition hover:text-white">Catálogo</a>
                            <a href="<?= e(url('contacto.php')) ?>" class="transition hover:text-white">Contacto</a>
                        </nav>
                    </div>
                    <nav class="flex flex-wrap items-center gap-3" aria-label="Redes">
                        <?php foreach ($socials as [$label, $href, $glyph]): ?>
                            <?php $socialClass = 'inline-flex h-11 items-center gap-2 rounded-full border border-white/25 px-3.5 text-sm font-medium text-white'; ?>
                            <?php if ($href === ''): ?>
                                <span class="<?= $socialClass ?>" role="img" aria-label="<?= e($label) ?>"><?= $glyph ?><span><?= e($label) ?></span></span>
                            <?php else: ?>
                                <a href="<?= e($href) ?>" target="_blank" rel="noopener noreferrer" class="<?= $socialClass ?> transition hover:border-white/70 hover:bg-white/10" aria-label="<?= e($label) ?>"><?= $glyph ?><span><?= e($label) ?></span></a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </nav>
                </div>
                <p class="border-t border-white/10 pt-4 text-center text-xs tracking-wide text-white/70">
                    © <?= e(date('Y')) ?> <a href="https://winterccode.com/" target="_blank" rel="noopener noreferrer" class="font-semibold text-white underline-offset-4 hover:underline">WINTER C CODE</a>. Todos los derechos de autor reservados.
                </p>
            </div>
        </footer>
    </div>
    <div data-toast class="pointer-events-none fixed bottom-5 left-1/2 z-50 hidden -translate-x-1/2 rounded-full bg-ink px-4 py-2.5 text-sm text-white shadow-lg" role="status"></div>
    <script src="<?= e(asset('js/skeleton.js')) ?>" defer></script>
    <script src="<?= e(asset('js/app.js')) ?>" defer></script>
    <script src="<?= e(asset('js/alerts.js')) ?>" defer></script>
</body>
</html>
