        </main>
        <footer class="mt-6 flex flex-col gap-4 px-1 py-6 text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                <a href="<?= e(url('index.php')) ?>" data-logo class="text-lg font-black tracking-[-0.06em] text-ink">NØVA</a>
                <span class="hidden h-4 w-px bg-slate-300 sm:block"></span>
                <nav class="flex gap-4" aria-label="Pie">
                    <a href="<?= e(url('index.php')) ?>" class="hover:text-ink">Inicio</a>
                    <a href="<?= e(url('catalogo.php')) ?>" class="hover:text-ink">Catálogo</a>
                    <a href="<?= e(url('contacto.php')) ?>" class="hover:text-ink">Contacto</a>
                </nav>
            </div>
            <p class="text-xs tracking-wide text-slate-500">Diseñado por <span class="font-semibold text-slate-700">WINTER C CODE</span></p>
        </footer>
    </div>
    <div data-toast class="pointer-events-none fixed bottom-5 left-1/2 z-50 hidden -translate-x-1/2 rounded-full bg-ink px-4 py-2.5 text-sm text-white shadow-lg" role="status"></div>
    <script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
