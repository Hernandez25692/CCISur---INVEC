<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a INVEC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Reset y estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, #f9fafb 0%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Contenedor principal */
        .welcome-container {
            position: relative;
            overflow: hidden;
            padding: 48px 24px;
            background-color: white;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 800px;
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 1024px) {
            .welcome-container {
                flex-direction: row;
                align-items: center;
                padding: 48px;
            }
        }

        /* Sección de logo y texto */
        .welcome-content {
            width: 100%;
            text-align: center;
            margin-bottom: 24px;
        }

        @media (min-width: 1024px) {
            .welcome-content {
                width: 50%;
                text-align: left;
                margin-bottom: 0;
                padding-right: 24px;
            }
        }

        .logo {
            height: 80px;
            margin: 0 auto 16px;
        }

        @media (min-width: 1024px) {
            .logo {
                margin: 0 0 16px 0;
            }
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1d4ed8;
            margin-bottom: 8px;
        }

        @media (min-width: 1024px) {
            h1 {
                font-size: 2.25rem;
            }
        }

        .welcome-text {
            color: #4b5563;
            margin-top: 8px;
            line-height: 1.5;
        }

        /* Sección del botón */
        .button-container {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        @media (min-width: 1024px) {
            .button-container {
                width: 50%;
                justify-content: flex-end;
            }
        }

        .login-button {
            background-color: #2563eb;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 9999px;
            font-size: 1.125rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .login-button:hover {
            background-color: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <div class="welcome-container">
        <!-- Logo y bienvenida -->
        <div class="welcome-content">
            <img src="{{ asset('Logo/logo_menu.png') }}" class="logo" alt="INVEC Logo">
            <h1>Sistema INVEC</h1>
            <p class="welcome-text">Inventariado y Asignación de Equipo <br> Cámara de Comercio e Industrias del Sur -
                CCISur</p>
        </div>

        <!-- Botón -->
        <div class="button-container">
            <a href="{{ route('login') }}" class="login-button">
                Iniciar Sesión
            </a>
        </div>
    </div>
</body>

</html>
