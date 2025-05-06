<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVEC - Sistema de Inventario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset y estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        /* Barra de navegación */
        .navbar {
            background-color: white;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 50;
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 16px;
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
        }

        /* Logo y marca */
        .brand {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .brand-logo {
            height: 40px;
            width: auto;
        }

        .brand-name {
            margin-left: 8px;
            font-size: 18px;
            font-weight: 700;
            color: #1d4ed8;
        }

        /* Enlaces principales */
        .nav-links {
            display: none;
            margin-left: 24px;
        }

        .nav-link {
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            margin: 0 12px;
            padding: 8px 0;
            position: relative;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: #1d4ed8;
        }

        .nav-link.active {
            color: #1d4ed8;
            font-weight: 600;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #1d4ed8;
        }

        /* Área de usuario */
        .user-area {
            display: none;
            align-items: center;
        }

        .user-name {
            font-size: 14px;
            color: #4b5563;
            margin-right: 16px;
        }

        .logout-btn {
            background: none;
            border: none;
            color: #dc2626;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: color 0.2s;
        }

        .logout-btn:hover {
            color: #b91c1c;
            text-decoration: underline;
        }

        /* Botón menú móvil */
        .mobile-menu-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            color: #4b5563;
            background: none;
            border: none;
            cursor: pointer;
        }

        .mobile-menu-icon {
            width: 24px;
            height: 24px;
        }

        /* Menú móvil */
        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 40;
        }

        .mobile-menu.open {
            display: block;
        }

        .mobile-link {
            display: block;
            padding: 12px 16px;
            color: #4b5563;
            text-decoration: none;
            font-size: 15px;
            transition: background-color 0.2s;
        }

        .mobile-link:hover,
        .mobile-link.active {
            background-color: #f0f4ff;
            color: #1d4ed8;
        }

        .mobile-link.active {
            font-weight: 600;
        }

        .mobile-user-info {
            padding: 12px 16px;
            border-top: 1px solid #e5e7eb;
        }

        .mobile-user-name {
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 4px;
        }

        .mobile-user-email {
            font-size: 13px;
            color: #6b7280;
        }

        .mobile-logout-btn {
            display: block;
            width: 100%;
            padding: 12px 16px;
            text-align: left;
            color: #dc2626;
            font-size: 14px;
            font-weight: 500;
            background: none;
            border: none;
            border-top: 1px solid #e5e7eb;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .mobile-logout-btn:hover {
            background-color: #fef2f2;
        }

        /* Versión desktop */
        @media (min-width: 768px) {
            .nav-links {
                display: flex;
            }

            .user-area {
                display: flex;
            }

            .mobile-menu-btn {
                display: none;
            }
        }

        /* Efectos de transición */
        .mobile-menu {
            transition: all 0.3s ease;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-content">
                <!-- Logo y marca -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="brand">
                        <img src="{{ asset('Logo/logo_menu.png') }}" alt="INVEC" class="brand-logo">
                        <span class="brand-name">INVEC</span>
                    </a>

                    <!-- Enlaces principales -->
                    <div class="nav-links">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('mobiliario.index') }}"
                            class="nav-link {{ request()->routeIs('mobiliario.*') ? 'active' : '' }}">
                            Mobiliario
                        </a>
                        <a href="{{ route('dispositivos.index') }}"
                            class="nav-link {{ request()->routeIs('dispositivos.*') ? 'active' : '' }}">
                            Dispositivos
                        </a>
                        <a href="{{ route('asignaciones.index') }}"
                            class="nav-link {{ request()->routeIs('asignaciones.*') ? 'active' : '' }}">
                            Asignaciones
                        </a>
                        <a href="{{ route('reportes.asignados') }}"
                            class="nav-link {{ request()->routeIs('reportes.asignados') ? 'active' : '' }}">
                            Reporte Asignados
                        </a>
                        <a href="{{ route('reportes.disponibles') }}"
                            class="nav-link {{ request()->routeIs('reportes.disponibles') ? 'active' : '' }}">
                            Reporte Disponibles
                        </a>
                    </div>
                </div>

                <!-- Área de usuario -->
                <div class="user-area">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>

                <!-- Botón menú móvil -->
                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <svg class="mobile-menu-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path id="menuIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path id="closeIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" style="display: none;" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menú móvil -->
        <div class="mobile-menu" id="mobileMenu">
            <div class="mobile-links">
                <a href="{{ route('dashboard') }}"
                    class="mobile-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('mobiliario.index') }}"
                    class="mobile-link {{ request()->routeIs('mobiliario.*') ? 'active' : '' }}">
                    Mobiliario
                </a>
                <a href="{{ route('dispositivos.index') }}"
                    class="mobile-link {{ request()->routeIs('dispositivos.*') ? 'active' : '' }}">
                    Dispositivos
                </a>
                <a href="{{ route('asignaciones.index') }}"
                    class="mobile-link {{ request()->routeIs('asignaciones.*') ? 'active' : '' }}">
                    Asignaciones
                </a>
                <a href="{{ route('reportes.asignados') }}"
                    class="mobile-link {{ request()->routeIs('reportes.asignados') ? 'active' : '' }}">
                    Reporte Asignados
                </a>
                <a href="{{ route('reportes.disponibles') }}"
                    class="mobile-link {{ request()->routeIs('reportes.disponibles') ? 'active' : '' }}">
                    Reporte Disponibles
                </a>
            </div>

            <div class="mobile-user-info">
                <div class="mobile-user-name">{{ Auth::user()->name }}</div>
                <div class="mobile-user-email">{{ Auth::user()->email }}</div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="mobile-logout-btn">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const menuIcon = document.getElementById('menuIcon');
            const closeIcon = document.getElementById('closeIcon');

            mobileMenuBtn.addEventListener('click', function() {
                const isOpen = mobileMenu.classList.toggle('open');

                if (isOpen) {
                    menuIcon.style.display = 'none';
                    closeIcon.style.display = 'block';
                } else {
                    menuIcon.style.display = 'block';
                    closeIcon.style.display = 'none';
                }
            });

            // Cerrar menú al hacer clic en un enlace (útil para SPA)
            const mobileLinks = document.querySelectorAll('.mobile-link');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.remove('open');
                    menuIcon.style.display = 'block';
                    closeIcon.style.display = 'none';
                });
            });
        });
    </script>
</body>

</html>
