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
    <header class="inv-topbar" id="sidebarTopbar">
        <button type="button" class="inv-burger" id="sidebarToggle" aria-label="Abrir menú" aria-controls="sidebar"
            aria-expanded="false">
            <i class="fas fa-bars"></i>
        </button>
        <span class="inv-topbar-title">INVEC <span>· CCISur</span></span>
    </header>

    <!-- Fondo oscuro (móvil) -->
    <div class="inv-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="inv-sidebar" id="sidebar" aria-label="Menú principal">
        <!-- Logo y marca -->
        <div class="inv-brand">
            <div class="inv-brand-top">
                <div class="inv-brand-sub"><strong>INVEC</strong> · Sistema de Inventario</div>
                <!-- Botón colapsar (escritorio) -->
                <button type="button" class="inv-panel-btn" id="sidebarCollapseInside" data-sidebar-collapse
                    aria-label="Esconder barra lateral" title="Esconder barra lateral">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="3" />
                        <line x1="9" y1="3" x2="9" y2="21" />
                    </svg>
                </button>
                <!-- Botón cerrar (móvil / pantallas pequeñas) -->
                <button type="button" class="inv-mobile-close" id="sidebarMobileClose" aria-label="Cerrar menú"
                    title="Cerrar menú">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <a href="{{ route('dashboard') }}" class="inv-brand-logo">
                <img src="{{ asset('Logo/logo_devo.png') }}" width="1654" height="571"
                    alt="CCISur - Cámara de Comercio e Industrias del Sur">
            </a>
        </div>

        <!-- Enlaces principales -->
        <nav class="inv-nav" id="sidebarNav">
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
    <button type="button" class="inv-open-btn" id="sidebarCollapseOutside" data-sidebar-collapse
        aria-label="Mostrar barra lateral" title="Mostrar barra lateral">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="3" />
            <line x1="9" y1="3" x2="9" y2="21" />
        </svg>
    </button>

    <script>
        // Se ejecuta después de </aside>, cuando .inv-brand, .inv-nav e .inv-user ya están en el DOM y tienen su altura real
        (function() {
            const sidebar = document.getElementById('sidebar');
            const nav = document.getElementById('sidebarNav');
            const initialSavedScroll = sessionStorage.getItem('invec-sidebar-scroll');
            const targetScroll = initialSavedScroll !== null ? parseInt(initialSavedScroll, 10) : null;
            let restaurandoScroll = targetScroll !== null && !isNaN(targetScroll);

            // 1. Sincronizar inert/aria-hidden desde el inicio según viewport y estado
            function sincronizarInertInicial() {
                if (!sidebar) return;
                const esDesktop = window.matchMedia('(min-width: 1024px)').matches;
                const estaOculto = esDesktop
                    ? document.documentElement.classList.contains('sidebar-collapsed')
                    : !sidebar.classList.contains('is-open');

                if (estaOculto) {
                    sidebar.setAttribute('inert', '');
                    sidebar.setAttribute('aria-hidden', 'true');
                } else {
                    sidebar.removeAttribute('inert');
                    sidebar.setAttribute('aria-hidden', 'false');
                }
            }

            sincronizarInertInicial();

            // 2. Restaurar scroll exacto del menú sin dejar que un scroll recortado intermedio sobrescriba sessionStorage
            function aplicarScrollGuardado() {
                if (nav && restaurandoScroll && targetScroll !== null) {
                    nav.scrollTop = targetScroll;
                }
            }

            if (nav) {
                aplicarScrollGuardado();
                requestAnimationFrame(aplicarScrollGuardado);

                // Si el usuario interactúa manualmente con el menú, dejamos de forzar el scroll guardado
                ['wheel', 'touchstart', 'pointerdown', 'keydown'].forEach(function(evt) {
                    nav.addEventListener(evt, function() {
                        restaurandoScroll = false;
                    }, { passive: true, once: true });
                });

                window.addEventListener('load', function() {
                    aplicarScrollGuardado();
                    requestAnimationFrame(function() {
                        aplicarScrollGuardado();
                        restaurandoScroll = false;
                    });
                }, { once: true });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const sidebarNav = nav;
                const overlay = document.getElementById('sidebarOverlay');
                const toggle = document.getElementById('sidebarToggle');
                const topbar = document.getElementById('sidebarTopbar');
                const mobileClose = document.getElementById('sidebarMobileClose');
                const btnInside = document.getElementById('sidebarCollapseInside');
                const btnOutside = document.getElementById('sidebarCollapseOutside');
                const mqDesktop = window.matchMedia('(min-width: 1024px)');

                if (sidebarNav) {
                    aplicarScrollGuardado();
                    requestAnimationFrame(aplicarScrollGuardado);

                    sidebarNav.addEventListener('scroll', function() {
                        if (restaurandoScroll && targetScroll !== null && sidebarNav.scrollTop < targetScroll) {
                            return;
                        }
                        sessionStorage.setItem('invec-sidebar-scroll', sidebarNav.scrollTop);
                    }, { passive: true });
                }

            function sincronizarAccesibilidad() {
                const mainContent = document.querySelector('.inv-main');
                if (mqDesktop.matches) {
                    const colapsado = document.documentElement.classList.contains('sidebar-collapsed');
                    if (colapsado) {
                        sidebar.setAttribute('inert', '');
                        sidebar.setAttribute('aria-hidden', 'true');
                    } else {
                        sidebar.removeAttribute('inert');
                        sidebar.setAttribute('aria-hidden', 'false');
                    }
                    if (topbar) topbar.removeAttribute('inert');
                    if (mainContent) mainContent.removeAttribute('inert');
                } else {
                    const abierto = sidebar.classList.contains('is-open');
                    if (abierto) {
                        sidebar.removeAttribute('inert');
                        sidebar.setAttribute('aria-hidden', 'false');
                        if (topbar) topbar.setAttribute('inert', '');
                        if (mainContent) mainContent.setAttribute('inert', '');
                    } else {
                        sidebar.setAttribute('inert', '');
                        sidebar.setAttribute('aria-hidden', 'true');
                        if (topbar) topbar.removeAttribute('inert');
                        if (mainContent) mainContent.removeAttribute('inert');
                    }
                }
            }

            // Mueve el foco y reintenta unos frames: al abrir/expandir, los elementos heredan
            // visibility y la animan con "transition: all", así que en el primer frame aún no
            // son enfocables y el navegador ignora focus().
            function enfocarConReintento(elemento, sigueVigente) {
                if (!elemento) return;
                var intentos = 0;
                function intentar() {
                    if (sigueVigente && !sigueVigente()) return;
                    elemento.focus();
                    if (document.activeElement !== elemento && intentos < 20) {
                        intentos++;
                        requestAnimationFrame(intentar);
                    }
                }
                requestAnimationFrame(intentar);
            }

            function abrirSidebar() {
                sidebar.classList.add('is-open');
                overlay.classList.add('is-visible');
                toggle.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
                sincronizarAccesibilidad();

                // Mover el foco dentro del menú móvil al abrir.
                var objetivo = (mobileClose && getComputedStyle(mobileClose).display !== 'none')
                    ? mobileClose
                    : sidebar.querySelector('.inv-link, a, button');
                enfocarConReintento(objetivo, function() {
                    return sidebar.classList.contains('is-open');
                });
            }

            function cerrarSidebar(devolverFoco) {
                const estabaAbierto = sidebar.classList.contains('is-open');
                sidebar.classList.remove('is-open');
                overlay.classList.remove('is-visible');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
                sincronizarAccesibilidad();

                if (estabaAbierto && devolverFoco && toggle && !mqDesktop.matches) {
                    toggle.focus();
                }
            }

            toggle.addEventListener('click', abrirSidebar);
            overlay.addEventListener('click', function() {
                cerrarSidebar(true);
            });
            if (mobileClose) {
                mobileClose.addEventListener('click', function() {
                    cerrarSidebar(true);
                });
            }

            // Esconder/mostrar sidebar en escritorio y recordar la preferencia
            document.querySelectorAll('[data-sidebar-collapse]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const escondido = document.documentElement.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('invec-sidebar', escondido ? 'collapsed' : 'expanded');
                    sincronizarAccesibilidad();

                    requestAnimationFrame(function() {
                        if (escondido && btnOutside) {
                            btnOutside.focus();
                        } else if (!escondido && btnInside) {
                            enfocarConReintento(btnInside);
                        }
                    });
                });
            });

            // Cerrar con Escape y atrapar foco (Focus Trap) en móvil cuando el menú está abierto
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
                    cerrarSidebar(true);
                    return;
                }

                if (e.key === 'Tab' && !mqDesktop.matches && sidebar.classList.contains('is-open')) {
                    const enfocables = Array.from(
                        sidebar.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])')
                    ).filter(function(el) {
                        return el.offsetParent !== null && getComputedStyle(el).visibility !== 'hidden';
                    });

                    if (enfocables.length === 0) return;
                    const primero = enfocables[0];
                    const ultimo = enfocables[enfocables.length - 1];

                    if (e.shiftKey && document.activeElement === primero) {
                        e.preventDefault();
                        ultimo.focus();
                    } else if (!e.shiftKey && document.activeElement === ultimo) {
                        e.preventDefault();
                        primero.focus();
                    }
                }
            });

            // Cerrar menú al hacer clic en un enlace y guardar la posición exacta del scroll
            sidebar.querySelectorAll('.inv-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (sidebarNav) {
                        sessionStorage.setItem('invec-sidebar-scroll', sidebarNav.scrollTop);
                    }
                    cerrarSidebar(false);
                });
            });

            // Si cambia entre móvil y escritorio, ajustar estado y accesibilidad
            mqDesktop.addEventListener('change', function(e) {
                if (e.matches) {
                    cerrarSidebar(false);
                } else {
                    sincronizarAccesibilidad();
                }
            });

            sincronizarAccesibilidad();
            });
        })();
    </script>
