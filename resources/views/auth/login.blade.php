<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión | INVEC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reset y estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4f0fe 100%);
            min-height: 100vh;
            display: flex;
            color: #333;
            line-height: 1.6;
        }

        /* Layout principal */
        .login-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* Panel izquierdo - Información */
        .info-panel {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 50%;
            padding: 2rem 3rem;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .info-panel::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .info-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .info-panel img {
            height: 80px;
            margin-bottom: 2rem;
            filter: brightness(0) invert(1);
            z-index: 1;
        }

        .info-panel h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-align: center;
            z-index: 1;
        }

        .info-panel p {
            font-size: 1.1rem;
            text-align: center;
            margin-bottom: 2.5rem;
            opacity: 0.9;
            z-index: 1;
        }

        .features-list {
            list-style: none;
            margin-top: 1.5rem;
            z-index: 1;
        }

        .features-list li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .features-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #93c5fd;
            font-weight: bold;
        }

        /* Panel derecho - Formulario */
        .form-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 2rem;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            padding: 1.5rem;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header img {
            height: 60px;
            margin-bottom: 1.5rem;
        }

        .form-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #6b7280;
            font-size: 0.95rem;
        }

        /* Formulario */
        .login-form {
            margin-top: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            color: #4b5563;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f9fafb;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            background-color: white;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input {
            margin-right: 0.5rem;
            accent-color: #1e40af;
        }

        .forgot-password {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #1e40af;
            color: white;
        }

        .btn-primary:hover {
            background-color: #1e3a8a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2);
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.95rem;
            color: #6b7280;
        }

        .register-link a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Alertas */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        /* Responsive */
        @media (min-width: 992px) {
            .info-panel {
                display: flex;
            }

            .form-panel {
                width: 50%;
            }
        }

        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-container {
            animation: fadeIn 0.6s ease-out;
        }

        /* Efecto de onda decorativo */
        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" fill="rgba(255,255,255,0.1)" /><path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" fill="rgba(255,255,255,0.2)" /><path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="rgba(255,255,255,0.3)" /></svg>');
            background-size: cover;
            z-index: 0;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- Panel izquierdo - Información -->
        <div class="info-panel">
            <img src="{{ asset('Logo/logo_menu.png') }}" alt="Logo INVEC">
            <h1>Bienvenido a INVEC</h1>
            <p>Sistema de Inventario y Asignación de Equipos</p>

            <ul class="features-list">
                <li>Control de dispositivos y mobiliario</li>
                <li>Asignaciones con actas de entrega</li>
                <li>Reportes de disponibilidad</li>
                <li>Seguimiento de mantenimientos</li>
                <li>Historial completo de equipos</li>
            </ul>

            <div class="wave"></div>
        </div>

        <!-- Panel derecho - Formulario -->
        <div class="form-panel">
            <div class="form-container">
                <div class="form-header">
                    <img src="{{ asset('Logo/logo_menu.png') }}" alt="INVEC">
                    <h2>Iniciar Sesión</h2>
                    <p>Ingresa tus credenciales para continuar</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="login-form">
                    @csrf

                    <!-- Correo -->
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            autofocus class="form-control" placeholder="tu@email.com">
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input id="password" type="password" name="password" required class="form-control"
                            placeholder="••••••••">
                    </div>

                    <!-- Recuérdame y enlace -->
                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Recuérdame
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-password">¿Olvidaste tu
                                contraseña?</a>
                        @endif
                    </div>

                    <!-- Botón -->
                    <button type="submit" class="btn btn-primary">
                        Iniciar Sesión
                    </button>
                </form>

                <div class="register-link">
                    ¿No tienes cuenta? <a href="{{ route('register') }}">Registrarse</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
