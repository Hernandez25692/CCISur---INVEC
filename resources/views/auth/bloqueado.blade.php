<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Sesión Bloqueada | CCISUR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 50% 20%, #1e293b 0%, #0b1329 100%);
            color: #f8fafc;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Luces de fondo decorativas */
        .ambient-glow {
            position: absolute;
            width: 380px;
            height: 380px;
            max-width: 90vw;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(175, 151, 49, 0.16) 0%, rgba(175, 151, 49, 0) 70%);
            top: 15%;
            left: 50%;
            transform: translate(-50%, -20%);
            pointer-events: none;
            z-index: 1;
        }

        .lock-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 460px;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.25rem;
            padding: 2.25rem 1.75rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(175, 151, 49, 0.18);
            animation: fadeInCard 0.35s ease-out;
            margin: auto;
        }

        @keyframes fadeInCard {
            from {
                opacity: 0;
                transform: translateY(12px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Cápsula para destacar el logo institucional de CCISUR */
        .logo-container {
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: center;
        }

        .logo-capsule {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #ffffff;
            border-radius: 0.85rem;
            padding: 0.6rem 1.1rem;
            max-width: 220px;
            width: 100%;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .logo-capsule img {
            width: 100%;
            height: auto;
            max-height: 48px;
            object-fit: contain;
            display: block;
        }

        /* Ícono del candado */
        .icon-badge {
            width: 74px;
            height: 74px;
            margin: 0 auto 1.25rem auto;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(175, 151, 49, 0.22) 0%, rgba(175, 151, 49, 0.06) 100%);
            border: 2px solid rgba(175, 151, 49, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #AF9731;
            font-size: 1.85rem;
            position: relative;
            box-shadow: 0 0 24px rgba(175, 151, 49, 0.25);
        }

        .icon-badge::after {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 1px dashed rgba(175, 151, 49, 0.35);
            animation: rotateDashed 20s linear infinite;
        }

        @keyframes rotateDashed {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        h1 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.65rem;
            letter-spacing: -0.02em;
            line-height: 1.3;
        }

        p.description {
            color: #94a3b8;
            font-size: 0.88rem;
            line-height: 1.55;
            margin-bottom: 1.5rem;
        }

        .security-badge {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.75rem;
            padding: 0.8rem 1rem;
            margin-bottom: 1.75rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-align: left;
        }

        .security-badge i {
            color: #10b981;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .security-badge span {
            color: #cbd5e1;
            font-size: 0.8rem;
            line-height: 1.4;
        }

        .btn-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            width: 100%;
            min-height: 48px;
            padding: 0.85rem 1.5rem;
            background: linear-gradient(135deg, #AF9731 0%, #8c7621 100%);
            color: #0F172A;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(175, 151, 49, 0.35);
            cursor: pointer;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #c4a938 0%, #9e8526 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(175, 151, 49, 0.5);
        }

        .footer-note {
            margin-top: 1.35rem;
            font-size: 0.72rem;
            color: #64748b;
            line-height: 1.4;
        }

        /* Ajustes específicos para móviles */
        @media (max-width: 480px) {
            body {
                padding: 0.75rem;
            }

            .lock-card {
                padding: 1.75rem 1.15rem;
                border-radius: 1rem;
            }

            .logo-capsule {
                max-width: 190px;
                padding: 0.5rem 0.85rem;
            }

            .logo-capsule img {
                max-height: 42px;
            }

            .icon-badge {
                width: 64px;
                height: 64px;
                font-size: 1.6rem;
                margin-bottom: 1rem;
            }

            h1 {
                font-size: 1.25rem;
            }

            p.description {
                font-size: 0.82rem;
                margin-bottom: 1.25rem;
            }

            .security-badge {
                padding: 0.65rem 0.85rem;
                margin-bottom: 1.35rem;
            }

            .security-badge span {
                font-size: 0.75rem;
            }

            .btn-login {
                font-size: 0.9rem;
                padding: 0.8rem 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="ambient-glow"></div>

    <div class="lock-card">
        <!-- Logo institucional oficial de CCISUR -->
        <div class="logo-container">
            <div class="logo-capsule">
                <img src="{{ asset('Logo/logo_devo.png') }}" alt="CCISUR - Cámara de Comercio e Industrias del Sur">
            </div>
        </div>

        <!-- Ícono Candado -->
        <div class="icon-badge">
            <i class="fas fa-lock"></i>
        </div>

        <!-- Mensaje -->
        <h1>Sesión Bloqueada por Inactividad</h1>
        <p class="description">
            Por motivos de seguridad institucional, la sesión ha sido suspendida automáticamente tras detectar inactividad para proteger los datos de la empresa.
        </p>

        <!-- Detalle de seguridad -->
        <div class="security-badge">
            <i class="fas fa-shield-halved"></i>
            <span>Tus datos y registros anteriores están guardados de forma segura en el sistema.</span>
        </div>

        <!-- Botón de retorno al Login -->
        <a href="{{ route('login') }}" class="btn-login">
            <i class="fas fa-key"></i>
            <span>Iniciar Sesión Nuevamente</span>
        </a>

        <div class="footer-note">
            Cámara de Comercio e Industrias del Sur (CCISUR) • Control de Inventario
        </div>
    </div>
</body>

</html>
