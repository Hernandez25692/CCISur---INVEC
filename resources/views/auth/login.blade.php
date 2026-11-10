<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión | INVEC - CCISUR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        :root {
            --brand-navy-950: #0b1329;
            --brand-navy-900: #0f172a;
            --brand-navy-800: #1e293b;
            --brand-navy-700: #334155;
            --brand-gold-500: #AF9731;
            --brand-gold-400: #c4a938;
            --brand-gold-600: #8c7621;
            --brand-gold-100: #fef9c3;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            background-color: var(--brand-navy-950);
            color: #1e293b;
            position: relative;
            overflow-x: hidden;
        }

        .login-layout {
            display: flex;
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
        }

        /* ── Panel Izquierdo Institucional (Escritorio) ── */
        .brand-panel {
            display: none;
            width: 48%;
            background: radial-gradient(circle at 30% 25%, #1e293b 0%, #0b1329 100%);
            padding: 3.5rem 3.5rem;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            overflow: hidden;
        }

        .ambient-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(175, 151, 49, 0.12) 0%, rgba(175, 151, 49, 0) 70%);
            top: 20%;
            left: 20%;
            pointer-events: none;
        }

        .brand-content {
            z-index: 2;
        }

        .brand-logo-capsule {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border-radius: 1rem;
            padding: 0.75rem 1.4rem;
            max-width: 260px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
            margin-bottom: 2.25rem;
        }

        .brand-logo-capsule img {
            width: 100%;
            height: auto;
            max-height: 52px;
            object-fit: contain;
            display: block;
        }

        .brand-title {
            color: #ffffff;
            font-size: 2.1rem;
            font-weight: 800;
            line-height: 1.25;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .brand-title span {
            color: var(--brand-gold-400);
        }

        .brand-desc {
            color: #94a3b8;
            font-size: 0.98rem;
            line-height: 1.6;
            margin-bottom: 2rem;
            max-width: 440px;
        }

        .features-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            z-index: 2;
        }

        .features-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #cbd5e1;
            font-size: 0.92rem;
        }

        .features-list li i {
            color: var(--brand-gold-400);
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .brand-footer {
            z-index: 2;
            color: #64748b;
            font-size: 0.78rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding-top: 1.25rem;
        }

        /* ── Panel Derecho - Formulario de Login ── */
        .form-panel {
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            background: radial-gradient(circle at 50% 15%, #1e293b 0%, #0b1329 100%);
            position: relative;
            overflow: hidden;
        }

        .form-panel .ambient-glow {
            top: 15%;
            left: 50%;
            transform: translateX(-50%);
        }

        .form-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            padding: 2.25rem 1.75rem;
            border-radius: 1.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 2;
        }

        /* Header móvil con logo CCISUR */
        .mobile-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .mobile-logo {
            max-height: 48px;
            width: auto;
            max-width: 210px;
            object-fit: contain;
            margin: 0 auto 0.75rem auto;
            display: block;
        }

        .form-card h2 {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--brand-navy-900);
            margin-bottom: 0.35rem;
            letter-spacing: -0.02em;
        }

        .form-card p.subtitle {
            color: #64748b;
            font-size: 0.92rem;
            margin-bottom: 1.75rem;
        }

        /* Alertas */
        .alert {
            padding: 0.85rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.88rem;
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            margin-bottom: 1.5rem;
            line-height: 1.45;
        }

        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .alert-danger {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert i {
            margin-top: 0.15rem;
            flex-shrink: 0;
            font-size: 1rem;
        }

        /* Campos de Entrada */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--brand-navy-900);
            margin-bottom: 0.45rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            color: #94a3b8;
            font-size: 0.95rem;
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.6rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            color: #0f172a;
            background-color: #f8fafc;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            background-color: #ffffff;
            border-color: var(--brand-gold-500);
            box-shadow: 0 0 0 3px rgba(175, 151, 49, 0.2);
        }

        .form-control:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--brand-gold-500);
        }

        .toggle-password-btn {
            position: absolute;
            right: 0.85rem;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.35rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            transition: color 0.2s ease;
        }

        .toggle-password-btn:hover {
            color: var(--brand-navy-800);
        }

        /* Botón Ingresar */
        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            width: 100%;
            min-height: 48px;
            padding: 0.85rem 1.5rem;
            margin-top: 0.75rem;
            background: linear-gradient(135deg, var(--brand-gold-500) 0%, var(--brand-gold-600) 100%);
            color: #0f172a;
            font-size: 0.98rem;
            font-weight: 700;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(175, 151, 49, 0.35);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--brand-gold-400) 0%, var(--brand-gold-500) 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(175, 151, 49, 0.45);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* ── Responsividad (Desktop) ── */
        @media (min-width: 1024px) {
            .brand-panel {
                display: flex;
            }

            .form-panel {
                width: 52%;
                padding: 3.5rem;
                background: #f8fafc;
            }

            .form-panel .ambient-glow {
                display: none;
            }

            .form-card {
                padding: 2.75rem 2.5rem;
                border: 1px solid #e2e8f0;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            }

            .mobile-header {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="login-layout">
        <!-- Panel Izquierdo - Identidad Institucional CCISUR (Desktop) -->
        <div class="brand-panel">
            <div class="ambient-glow"></div>

            <div class="brand-content">
                <div class="brand-logo-capsule">
                    <img src="{{ asset('Logo/logo_devo.png') }}" alt="CCISUR - Cámara de Comercio e Industrias del Sur">
                </div>

                <h1 class="brand-title">Control de Inventario <br><span>y Asignaciones</span></h1>
                <p class="brand-desc">
                    Plataforma institucional para la administración, trazabilidad y control de los bienes, dispositivos y mobiliario de CCISUR.
                </p>

                <ul class="features-list">
                    <li>
                        <i class="fas fa-circle-check"></i>
                        <span>Control integral de dispositivos y mobiliario</span>
                    </li>
                    <li>
                        <i class="fas fa-circle-check"></i>
                        <span>Generación oficial de actas de entrega y devolución</span>
                    </li>
                    <li>
                        <i class="fas fa-circle-check"></i>
                        <span>Trazabilidad histórica y reportes exportables</span>
                    </li>
                    <li>
                        <i class="fas fa-circle-check"></i>
                        <span>Acceso seguro protegido por roles</span>
                    </li>
                </ul>
            </div>

            <div class="brand-footer">
                Cámara de Comercio e Industrias del Sur (CCISUR) • INVEC &copy; {{ date('Y') }}
            </div>
        </div>

        <!-- Panel Derecho - Formulario de Acceso -->
        <div class="form-panel">
            <div class="ambient-glow"></div>
            <div class="form-card">
                <!-- Header Móvil con Logo CCISUR -->
                <div class="mobile-header">
                    <img src="{{ asset('Logo/logo_devo.png') }}" alt="CCISUR" class="mobile-logo">
                </div>

                <h2>Iniciar Sesión</h2>
                <p class="subtitle">Ingresa tus credenciales para acceder al sistema</p>

                <!-- Estado o Sesión -->
                @if (session('status'))
                    <div class="alert alert-success" role="status">
                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Errores de Validación -->
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert" aria-live="assertive">
                        <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                        <div>
                            @if ($errors->count() === 1)
                                <span>{{ $errors->first() }}</span>
                            @else
                                <ul style="margin: 0; padding-left: 1rem;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login', [], false) }}">
                    @csrf

                    <!-- Correo Electrónico -->
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="form-control" placeholder="tu.correo@ccisur.hn">
                        </div>
                    </div>

                    <!-- Contraseña con Ojito -->
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input id="password" type="password" name="password" required class="form-control"
                                placeholder="••••••••" style="padding-right: 2.75rem;">
                            <button type="button" class="toggle-password-btn" onclick="togglePasswordVisibility()" aria-label="Mostrar u ocultar contraseña">
                                <i id="toggle-eye-icon" class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-arrow-right-to-bracket"></i>
                        <span>Ingresar al Sistema</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggle-eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>

</html>
