<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVEC - Sistema de Inventario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --active-color: #1d4ed8;
            --text-color: #374151;
            --light-bg: #f9fafb;
            --border-color: #e5e7eb;
            --danger-color: #ef4444;
            --danger-hover: #dc2626;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
                Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        /* Barra de navegación principal */
        .navbar {
            background-color: white;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 50;
        }

        .nav-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 4.5rem;
        }

        /* Logo y marca */
        .brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .nav-content>.flex.items-center {
            gap: 1rem;
            min-width: 1px;
            flex-shrink: 0;
        }

        .brand:hover {
            transform: translateY(-1px);
        }

        .brand-logo {
            height: 2.5rem;
            width: auto;
        }

        .brand img {
            display: block;
            max-height: 40px;
        }

        .brand-name {
            margin-left: 0.75rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--active-color);
            letter-spacing: -0.5px;
        }

        /* Enlaces principales - Desktop */
        .nav-links {
            display: none;
            margin-left: 2rem;
            height: 100%;
        }

        .nav-link {
            position: relative;
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9375rem;
            margin: 0 0.75rem;
            padding: 1.25rem 0;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .nav-link:hover {
            color: var(--primary-hover);
        }

        .nav-link i {
            margin-right: 0.5rem;
            font-size: 0.9rem;
            width: 1.25rem;
            text-align: center;
        }

        .nav-link.active {
            color: var(--active-color);
            font-weight: 600;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--active-color);
            border-radius: 3px 3px 0 0;
        }

        /* Área de usuario - Desktop */
        .user-area {
            display: none;
            align-items: center;
        }

        .user-avatar {
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            margin-right: 0.75rem;
        }

        .user-name {
            font-size: 0.875rem;
            color: var(--text-color);
            margin-right: 1rem;
        }

        .logout-btn {
            background: none;
            border: none;
            color: var(--danger-color);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: color 0.2s;
            padding: 0.5rem;
            border-radius: 0.25rem;
        }

        .logout-btn:hover {
            color: var(--danger-hover);
            background-color: #fef2f2;
        }

        /* Botón menú móvil */
        .mobile-menu-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            color: var(--text-color);
            background: none;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .mobile-menu-btn:hover {
            background-color: var(--light-bg);
        }

        .mobile-menu-icon {
            width: 1.5rem;
            height: 1.5rem;
        }

        /* Menú móvil */
        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 40;
            border-top: 1px solid var(--border-color);
            max-height: calc(100vh - 4.5rem);
            overflow-y: auto;
        }

        .mobile-menu.open {
            display: block;
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mobile-link {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: var(--text-color);
            text-decoration: none;
            font-size: 0.9375rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .mobile-link i {
            margin-right: 1rem;
            width: 1.25rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .mobile-link:hover,
        .mobile-link.active {
            background-color: #f0f4ff;
            color: var(--primary-hover);
            border-left-color: var(--primary-color);
        }

        .mobile-link.active {
            font-weight: 600;
            color: var(--active-color);
        }

        .mobile-user-info {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            background-color: var(--light-bg);
        }

        .mobile-user-name {
            font-size: 0.9375rem;
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .mobile-user-email {
            font-size: 0.8125rem;
            color: #6b7280;
        }

        .mobile-logout-btn {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 1rem 1.5rem;
            color: var(--danger-color);
            font-size: 0.9375rem;
            font-weight: 500;
            background: none;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .mobile-logout-btn i {
            margin-right: 1rem;
            width: 1.25rem;
            text-align: center;
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

            .nav-container {
                max-width: 1280px;
            }
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
                        <img src="{{ asset('menu/logo1.png') }}" alt="Logo INVEC" class="h-10 w-auto block">
                    </a>


                    <!-- Enlaces principales -->
                    <div class="nav-links">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('mobiliario.index') }}"
                            class="nav-link {{ request()->routeIs('mobiliario.*') ? 'active' : '' }}">
                            <i class="fas fa-couch"></i>
                            Mobiliario
                        </a>
                        <a href="{{ route('dispositivos.index') }}"
                            class="nav-link {{ request()->routeIs('dispositivos.*') ? 'active' : '' }}">
                            <i class="fas fa-laptop"></i>
                            Dispositivos
                        </a>
                        <a href="{{ route('asignaciones.index') }}"
                            class="nav-link {{ request()->routeIs('asignaciones.*') ? 'active' : '' }}">
                            <i class="fas fa-handshake"></i>
                            Asignaciones
                        </a>
                        <a href="{{ route('reportes.asignados') }}"
                            class="nav-link {{ request()->routeIs('reportes.asignados') ? 'active' : '' }}">
                            <i class="fas fa-file-alt"></i>
                            R/Asignados
                        </a>
                        <a href="{{ route('reportes.disponibles') }}"
                            class="nav-link {{ request()->routeIs('reportes.disponibles') ? 'active' : '' }}">
                            <i class="fas fa-file-export"></i>
                            R/Disponibles
                        </a>
                        <a href="{{ route('devoluciones.index') }}"
                            class="nav-link {{ request()->routeIs('devoluciones.*') ? 'active' : '' }}">
                            <i class="fas fa-undo"></i>
                            Devoluciones
                        </a>
                        <a href="{{ route('empleados.index') }}"
                            class="nav-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            Empleados
                        </a>
                    </div>
                </div>

                <!-- Área de usuario -->
                <div class="user-area">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
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
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
                <a href="{{ route('mobiliario.index') }}"
                    class="mobile-link {{ request()->routeIs('mobiliario.*') ? 'active' : '' }}">
                    <i class="fas fa-couch"></i>
                    Mobiliario
                </a>
                <a href="{{ route('dispositivos.index') }}"
                    class="mobile-link {{ request()->routeIs('dispositivos.*') ? 'active' : '' }}">
                    <i class="fas fa-laptop"></i>
                    Dispositivos
                </a>
                <a href="{{ route('asignaciones.index') }}"
                    class="mobile-link {{ request()->routeIs('asignaciones.*') ? 'active' : '' }}">
                    <i class="fas fa-handshake"></i>
                    Asignaciones
                </a>
                <a href="{{ route('reportes.asignados') }}"
                    class="mobile-link {{ request()->routeIs('reportes.asignados') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    Reporte Asignados
                </a>
                <a href="{{ route('reportes.disponibles') }}"
                    class="mobile-link {{ request()->routeIs('reportes.disponibles') ? 'active' : '' }}">
                    <i class="fas fa-file-export"></i>
                    Reporte Disponibles
                </a>
                <a href="{{ route('devoluciones.index') }}"
                    class="mobile-link {{ request()->routeIs('devoluciones.*') ? 'active' : '' }}">
                    <i class="fas fa-undo"></i>
                    Devoluciones
                </a>
                <a href="{{ route('empleados.index') }}"
                    class="mobile-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    Empleados
                </a>
            </div>

            <div class="mobile-user-info">
                <div class="mobile-user-name">{{ Auth::user()->name }}</div>
                <div class="mobile-user-email">{{ Auth::user()->email }}</div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="mobile-logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
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

            // Cerrar menú al hacer clic en un enlace
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
