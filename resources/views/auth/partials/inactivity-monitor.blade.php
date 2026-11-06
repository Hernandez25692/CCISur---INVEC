@if (auth()->check())
<script>
    (function() {
        // Tiempo de inactividad sincronizado con la configuración de sesión
        const timeoutMinutes = {{ (int) config('session.lifetime', 45) }};
        const timeoutMs = timeoutMinutes * 60 * 1000;
        let lastActivity = Date.now();
        let isLocking = false;

        function recordActivity() {
            if (!isLocking) {
                lastActivity = Date.now();
            }
        }

        // Detectar cualquier interacción real del usuario
        const events = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll'];
        events.forEach(function(evt) {
            window.addEventListener(evt, recordActivity, { passive: true });
        });

        // Comprobador de inactividad cada segundo
        setInterval(function() {
            if (isLocking) return;
            const elapsed = Date.now() - lastActivity;

            if (elapsed >= timeoutMs) {
                isLocking = true;

                // 1. Cortina visual de bloqueo inmediato 
                const curtain = document.createElement('div');
                curtain.id = 'inactivity-lock-curtain';
                curtain.style.cssText = 'position:fixed;inset:0;background:rgba(11,19,41,0.92);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);z-index:999999;display:flex;align-items:center;justify-content:center;color:#ffffff;font-family:sans-serif;opacity:0;transition:opacity 0.25s ease;';
                curtain.innerHTML = '<div style="text-align:center;padding:2rem;"><i class="fas fa-lock fa-3x" style="color:#AF9731;margin-bottom:1rem;display:inline-block;"></i><h2 style="font-size:1.35rem;font-weight:700;margin-bottom:0.5rem;color:#ffffff;">Bloqueando sesión por inactividad...</h2><p style="color:#94a3b8;font-size:0.875rem;">Protegiendo los datos del inventario de CCISUR...</p></div>';
                document.body.appendChild(curtain);

                requestAnimationFrame(function() {
                    curtain.style.opacity = '1';
                });

                // 2. Destruir la sesión en el servidor y navegar a la pantalla de bloqueo
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                fetch('{{ route('logout') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }).finally(function() {
                    window.location.href = '{{ route('session.locked') }}';
                });
            }
        }, 1000);
    })();
</script>
@endif
