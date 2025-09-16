<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVEC - Sistema de Inventario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Variables de diseño */
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary-color: #f8fafc;
            --text-color: #334155;
            --text-light: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Reset y estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            color: var(--text-color);
            background-color: #f9fafb;
        }

        /* Barra de navegación */
        .navbar {
            background-color: white;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .nav-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        /* Logo y marca */
        .brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: 0.75rem;
        }

        .brand-logo {
            height: 36px;
            width: auto;
            transition: var(--transition);
        }

        .brand:hover .brand-logo {
            transform: scale(1.05);
        }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
            letter-spacing: -0.5px;
        }

        /* Enlaces principales */
        .nav-links {
            display: none;
            margin-left: 2rem;
        }

        .nav-link {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9375rem;
            margin: 0 0.75rem;
            padding: 0.5rem 0;
            position: relative;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .nav-link i {
            font-size: 0.875rem;
        }

        .nav-link.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }

        /* Área de usuario */
        .user-area {
            display: none;
            align-items: center;
            gap: 1.5rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-color);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-light);
        }

        .logout-btn {
            background: none;
            border: none;
            color: #ef4444;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
        }

        .logout-btn:hover {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .logout-btn i {
            font-size: 0.875rem;
        }

        /* Botón menú móvil */
        .mobile-menu-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            color: var(--text-color);
            background: none;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .mobile-menu-btn:hover {
            background-color: #f1f5f9;
        }

        .mobile-menu-icon {
            width: 24px;
            height: 24px;
        }

        /* Menú móvil */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: white;
            box-shadow: var(--shadow-md);
            z-index: 40;
            overflow-y: auto;
            transform: translateY(-100%);
            opacity: 0;
            transition: var(--transition);
        }

        .mobile-menu.open {
            transform: translateY(0);
            opacity: 1;
        }

        .mobile-links {
            display: flex;
            flex-direction: column;
            padding: 0.5rem 0;
        }

        .mobile-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            color: var(--text-color);
            text-decoration: none;
            font-size: 0.9375rem;
            transition: var(--transition);
        }

        .mobile-link i {
            width: 20px;
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .mobile-link:hover,
        .mobile-link.active {
            background-color: #f8fafc;
            color: var(--primary-color);
        }

        .mobile-link.active {
            font-weight: 600;
        }

        .mobile-link.active i {
            color: var(--primary-color);
        }

        .mobile-user-info {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            margin: 0.5rem 0;
        }

        .mobile-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .mobile-user-name {
            font-size: 0.9375rem;
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.25rem;
        }

        .mobile-user-email {
            font-size: 0.8125rem;
            color: var(--text-light);
        }

        .mobile-logout-btn {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.875rem 1.5rem;
            text-align: left;
            color: #ef4444;
            font-size: 0.9375rem;
            font-weight: 500;
            background: none;
            border: none;
            border-top: 1px solid var(--border-color);
            cursor: pointer;
            transition: var(--transition);
        }

        .mobile-logout-btn i {
            width: 20px;
            text-align: center;
            font-size: 0.875rem;
        }

        .mobile-logout-btn:hover {
            background-color: #fef2f2;
        }

        /* Versión desktop */
        @media (min-width: 992px) {
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
        .nav-link,
        .mobile-link,
        .logout-btn,
        .mobile-logout-btn {
            transition: var(--transition);
        }
    </style>
</head>

<body>
    <nav class="navbar" style="background: linear-gradient(90deg, #2563eb 0%, #1e40af 100%);">
        <div class="nav-container">
            <div class="nav-content">
                <!-- Logo y marca -->
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="brand">
                        <img src="{{ asset('Logo/logo_menu.png') }}" alt="INVEC" class="brand-logo">
                        <span class="brand-name" style="color: #fff;">INVEC</span>
                    </a>

                    <!-- Enlaces principales -->
                    <div class="nav-links">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            style="color: #e0e7ef;">
                            <i class="fas fa-tachometer-alt" style="color: #fff;"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('mobiliario.index') }}"
                            class="nav-link {{ request()->routeIs('mobiliario.*') ? 'active' : '' }}"
                            style="color: #e0e7ef;">
                            <i class="fas fa-couch" style="color: #fff;"></i>
                            Mobiliario
                        </a>
                        <a href="{{ route('dispositivos.index') }}"
                            class="nav-link {{ request()->routeIs('dispositivos.*') ? 'active' : '' }}"
                            style="color: #e0e7ef;">
                            <i class="fas fa-laptop" style="color: #fff;"></i>
                            Dispositivos
                        </a>
                        <a href="{{ route('asignaciones.index') }}"
                            class="nav-link {{ request()->routeIs('asignaciones.*') ? 'active' : '' }}"
                            style="color: #e0e7ef;">
                            <i class="fas fa-handshake" style="color: #fff;"></i>
                            Asignaciones
                        </a>
                        <a href="{{ route('reportes.asignados') }}"
                            class="nav-link {{ request()->routeIs('reportes.asignados') ? 'active' : '' }}"
                            style="color: #e0e7ef;">
                            <i class="fas fa-clipboard-list" style="color: #fff;"></i>
                            Reporte Asignados
                        </a>
                        <a href="{{ route('reportes.disponibles') }}"
                            class="nav-link {{ request()->routeIs('reportes.disponibles') ? 'active' : '' }}"
                            style="color: #e0e7ef;">
                            <i class="fas fa-clipboard-check" style="color: #fff;"></i>
                            Reporte Disponibles
                        </a>
                        <a href="{{ route('devoluciones.index') }}"
                            class="nav-link {{ request()->routeIs('devoluciones.*') ? 'active' : '' }}"
                            style="color: #e0e7ef;">
                            <i class="fas fa-undo" style="color: #fff;"></i>
                            Devoluciones
                        </a>
                        <a href="{{ route('empleados.index') }}"
                            class="nav-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}"
                            style="color: #e0e7ef;">
                            <i class="fas fa-users" style="color: #fff;"></i>
                            Empleados
                        </a>
                    </div>
                </div>

                <!-- Área de usuario -->
                <div class="user-area">
                    <div class="user-profile">
                        <div class="user-avatar" style="background: #fff; color: #2563eb;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <span class="user-name" style="color: #fff;">{{ Auth::user()->name }}</span>
                            <span class="user-role" style="color: #e0e7ef;">Administrador</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn" style="background: #ef4444; color: #fff; border-radius: 0.5rem; padding: 0.5rem 1.25rem; font-weight: 600; box-shadow: 0 2px 8px rgba(239,68,68,0.12); border: none; display: flex; align-items: center; gap: 0.5rem; transition: background 0.2s;">
                            <i class="fas fa-sign-out-alt" style="color: #fff;"></i>
                            Salir
                        </button>
                    </form>
                </div>

                <!-- Botón menú móvil -->
                <button class="mobile-menu-btn" id="mobileMenuBtn" style="color: #fff;">
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
        <div class="mobile-menu" id="mobileMenu" style="background: linear-gradient(90deg, #2563eb 0%, #1e40af 100%);">
            <div class="mobile-user-info" style="background: rgba(30,64,175,0.15);">
                <div class="mobile-user-avatar" style="background: #fff; color: #2563eb;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="mobile-user-name" style="color: #fff;">{{ Auth::user()->name }}</div>
                <div class="mobile-user-email" style="color: #e0e7ef;">{{ Auth::user()->email }}</div>
            </div>

            <div class="mobile-links">
                <a href="{{ route('dashboard') }}"
                    class="mobile-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    style="color: #fff;">
                    <i class="fas fa-tachometer-alt" style="color: #e0e7ef;"></i>
                    Dashboard
                </a>
                <a href="{{ route('mobiliario.index') }}"
                    class="mobile-link {{ request()->routeIs('mobiliario.*') ? 'active' : '' }}"
                    style="color: #fff;">
                    <i class="fas fa-couch" style="color: #e0e7ef;"></i>
                    Mobiliario
                </a>
                <a href="{{ route('dispositivos.index') }}"
                    class="mobile-link {{ request()->routeIs('dispositivos.*') ? 'active' : '' }}"
                    style="color: #fff;">
                    <i class="fas fa-laptop" style="color: #e0e7ef;"></i>
                    Dispositivos
                </a>
                <a href="{{ route('asignaciones.index') }}"
                    class="mobile-link {{ request()->routeIs('asignaciones.*') ? 'active' : '' }}"
                    style="color: #fff;">
                    <i class="fas fa-handshake" style="color: #e0e7ef;"></i>
                    Asignaciones
                </a>
                <a href="{{ route('reportes.asignados') }}"
                    class="mobile-link {{ request()->routeIs('reportes.asignados') ? 'active' : '' }}"
                    style="color: #fff;">
                    <i class="fas fa-clipboard-list" style="color: #e0e7ef;"></i>
                    Reporte Asignados
                </a>
                <a href="{{ route('reportes.disponibles') }}"
                    class="mobile-link {{ request()->routeIs('reportes.disponibles') ? 'active' : '' }}"
                    style="color: #fff;">
                    <i class="fas fa-clipboard-check" style="color: #e0e7ef;"></i>
                    Reporte Disponibles
                </a>
                <a href="{{ route('devoluciones.index') }}"
                    class="mobile-link {{ request()->routeIs('devoluciones.*') ? 'active' : '' }}"
                    style="color: #fff;">
                    <i class="fas fa-undo" style="color: #e0e7ef;"></i>
                    Devoluciones
                </a>
                <a href="{{ route('empleados.index') }}"
                    class="mobile-link {{ request()->routeIs('empleados.*') ? 'active' : '' }}"
                    style="color: #fff;">
                    <i class="fas fa-users" style="color: #e0e7ef;"></i>
                    Empleados
                </a>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="mobile-logout-btn" style="color: #fff;">
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
                    document.body.style.overflow = 'hidden';
                } else {
                    menuIcon.style.display = 'block';
                    closeIcon.style.display = 'none';
                    document.body.style.overflow = '';
                }
            });

            // Cerrar menú al hacer clic en un enlace
            const mobileLinks = document.querySelectorAll('.mobile-link, .mobile-logout-btn');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
                    mobileMenu.classList.remove('open');
                    menuIcon.style.display = 'block';
                    closeIcon.style.display = 'none';
                    document.body.style.overflow = '';
                });
            });
        });
    </script>
</body>

</html>