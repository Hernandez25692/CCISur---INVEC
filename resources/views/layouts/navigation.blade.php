    <script>
        // Aplicar el estado guardado antes de pintar la página, para que el sidebar no "parpadee"
        if (localStorage.getItem('invec-sidebar') === 'collapsed') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>

    @php
        // Menú agrupado por secciones: [ruta, patrón para marcar activo, ícono, texto]
        $menu = [
            'General' => [
                ['dashboard', 'dashboard', 'fa-tachometer-alt', 'Dashboard'],
            ],
            'Inventario' => [
                ['mobiliario.index', 'mobiliario.*', 'fa-couch', 'Mobiliario'],
                ['dispositivos.index', 'dispositivos.*', 'fa-laptop', 'Dispositivos'],
            ],
            'Gestión' => [
                ['empleados.index', 'empleados.*', 'fa-users', 'Empleados'],
                ['asignaciones.index', 'asignaciones.*', 'fa-handshake', 'Asignaciones'],
                ['devoluciones.index', 'devoluciones.*', 'fa-undo', 'Devoluciones'],
            ],
            'Reportes' => [
                ['reportes.asignados', 'reportes.asignados', 'fa-clipboard-list', 'Bienes Asignados'],
                ['reportes.disponibles', 'reportes.disponibles', 'fa-clipboard-check', 'Bienes Disponibles'],
            ],
        ];
    @endphp

    <!-- Barra superior (solo móvil/tablet) -->
    <header class="inv-topbar">
        <button type="button" class="inv-burger" id="sidebarToggle" aria-label="Abrir menú" aria-controls="sidebar"
            aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
        <span class="inv-topbar-title">INVEC <span>· CCISur</span></span>
    </header>

    <!-- Fondo oscuro (móvil) -->
    <div class="inv-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="inv-sidebar" id="sidebar">
        <!-- Logo y marca -->
        <div class="inv-brand">
            <div class="inv-brand-top">
                <div class="inv-brand-sub"><strong>INVEC</strong> · Sistema de Inventario</div>
                <button type="button" class="inv-panel-btn" data-sidebar-collapse aria-label="Esconder barra lateral"
                    title="Esconder barra lateral">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3" />
                        <line x1="9" y1="3" x2="9" y2="21" />
                    </svg>
                </button>
            </div>
            <a href="{{ route('dashboard') }}" class="inv-brand-logo">
                <img src="{{ asset('Logo/logo_devo.png') }}" alt="CCISur - Cámara de Comercio e Industrias del Sur">
            </a>
        </div>

        <!-- Enlaces principales -->
        <nav class="inv-nav">
            @foreach ($menu as $seccion => $links)
                <div class="inv-nav-title">{{ $seccion }}</div>
                @foreach ($links as [$ruta, $patron, $icono, $texto])
                    <a href="{{ route($ruta) }}" class="inv-link {{ request()->routeIs($patron) ? 'is-active' : '' }}">
                        <i class="fas {{ $icono }}"></i>
                        {{ $texto }}
                    </a>
                @endforeach
            @endforeach
        </nav>

        <!-- Área de usuario -->
        <div class="inv-user">
            <div class="inv-user-profile">
                <div class="inv-user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="inv-user-info">
                    <span class="inv-user-name" title="{{ Auth::user()->email }}">{{ Auth::user()->name }}</span>
                    <span class="inv-user-role">Administrador</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inv-logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <!-- Botón para abrir sidebar cuando está escondido (escritorio) -->
    <button type="button" class="inv-open-btn" data-sidebar-collapse aria-label="Mostrar barra lateral"
        title="Mostrar barra lateral">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="3" />
            <line x1="9" y1="3" x2="9" y2="21" />
        </svg>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.getElementById('sidebarToggle');

            function abrirSidebar() {
                sidebar.classList.add('is-open');
                overlay.classList.add('is-visible');
                toggle.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }

            function cerrarSidebar() {
                sidebar.classList.remove('is-open');
                overlay.classList.remove('is-visible');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            toggle.addEventListener('click', abrirSidebar);
            overlay.addEventListener('click', cerrarSidebar);

            // Esconder/mostrar sidebar en escritorio y recordar la preferencia
            document.querySelectorAll('[data-sidebar-collapse]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const escondido = document.documentElement.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('invec-sidebar', escondido ? 'collapsed' : 'expanded');
                });
            });

            // Cerrar con la tecla Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') cerrarSidebar();
            });

            // Cerrar menú al hacer clic en un enlace
            sidebar.querySelectorAll('.inv-link').forEach(function(link) {
                link.addEventListener('click', cerrarSidebar);
            });

            // Si se agranda la pantalla a escritorio con el menú abierto, liberar el scroll
            window.matchMedia('(min-width: 1024px)').addEventListener('change', function(e) {
                if (e.matches) cerrarSidebar();
            });
        });
    </script>
