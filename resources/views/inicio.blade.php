<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a INVEC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;400&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --accent: #fbbf24;
            --bg-gradient: linear-gradient(135deg, #e0e7ff 0%, #f0fdfa 100%);
            --card-bg: rgba(255,255,255,0.95);
            --shadow: 0 20px 40px -10px rgba(30, 64, 175, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background: var(--bg-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
            position: relative;
        }

        /* Animated background shapes */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.5;
            z-index: 0;
        }
        .bg-shape.one {
            width: 320px; height: 320px;
            background: #60a5fa;
            top: -80px; left: -100px;
            animation: float1 8s ease-in-out infinite alternate;
        }
        .bg-shape.two {
            width: 220px; height: 220px;
            background: #fbbf24;
            bottom: -60px; right: -80px;
            animation: float2 10s ease-in-out infinite alternate;
        }
        @keyframes float1 {
            to { transform: translateY(40px) scale(1.1);}
        }
        @keyframes float2 {
            to { transform: translateY(-30px) scale(1.05);}
        }

        /* Card container */
        .welcome-container {
            position: relative;
            z-index: 1;
            background: var(--card-bg);
            border-radius: 32px;
            box-shadow: var(--shadow);
            max-width: 900px;
            width: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 0;
        }

        @media (min-width: 1024px) {
            .welcome-container {
                flex-direction: row;
                min-height: 420px;
            }
        }

        /* Left section */
        .welcome-content {
            flex: 1 1 0;
            padding: 48px 32px 40px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(120deg, #2563eb 60%, #fbbf24 100%);
            color: #fff;
        }
        @media (max-width: 1023px) {
            .welcome-content {
                padding: 40px 24px 32px 24px;
                text-align: center;
            }
        }

        .logo {
            height: 96px;
            width: 96px;
            max-width: 100%;
            margin-bottom: 24px;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.08));
            background: #fff;
            border-radius: 16px;
            padding: 8px 16px;
            display: block;
            object-fit: contain;
            margin-left: auto;
            margin-right: auto;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: 1px;
            color: #fff;
            text-shadow: 0 2px 8px rgba(37,99,235,0.10);
        }

        .welcome-text {
            font-size: 1.15rem;
            color: #f3f4f6;
            margin-bottom: 0;
            line-height: 1.6;
        }

        /* Right section */
        .action-section {
            flex: 1 1 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
            background: #fff;
        }
        @media (max-width: 1023px) {
            .action-section {
                padding: 32px 24px;
            }
        }

        .action-section h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 16px;
        }

        .action-section p {
            color: #64748b;
            margin-bottom: 32px;
            font-size: 1.05rem;
        }

        .login-button {
            background: linear-gradient(90deg, var(--primary), var(--accent));
            color: #fff;
            font-weight: 700;
            padding: 14px 36px;
            border-radius: 9999px;
            font-size: 1.2rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 6px 18px -4px #2563eb33;
            transition: background 0.3s, transform 0.2s, box-shadow 0.2s;
            border: none;
            outline: none;
        }
        .login-button i {
            font-size: 1.2em;
        }
        .login-button:hover {
            background: linear-gradient(90deg, var(--primary-dark), var(--accent));
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 12px 24px -6px #2563eb33;
        }

        /* Responsive stacking */
        @media (max-width: 1023px) {
            .welcome-container {
                flex-direction: column;
            }
            .welcome-content, .action-section {
                border-radius: 0;
            }
        }
    </style>
</head>

<body>
    <div class="bg-shape one"></div>
    <div class="bg-shape two"></div>
    <div class="welcome-container">
        <!-- Sección izquierda: Logo y bienvenida -->
        <div class="welcome-content">
            <img src="{{ asset('Logo/logo_menu.png') }}" class="logo" alt="INVEC Logo">
            <h1>Sistema INVEC</h1>
            <p class="welcome-text">
                Inventariado y Asignación de Equipo <br>
                <span style="font-weight:600; color:#fbbf24;">Cámara de Comercio e Industrias del Sur - CCISur</span>
            </p>
        </div>
        <!-- Sección derecha: Acción -->
        <div class="action-section">
            <h2>¡Bienvenido!</h2>
            <p>Gestiona el inventario y la asignación de equipos de manera eficiente y segura.<br>
            Accede a tu cuenta para comenzar.</p>
            <a href="{{ route('login') }}" class="login-button">
                <i class="fas fa-sign-in-alt"></i>
                Iniciar Sesión
            </a>
        </div>
    </div>
</body>

</html>
