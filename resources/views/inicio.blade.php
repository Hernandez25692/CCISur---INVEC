<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a INVEC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --azul-base: #2563eb;
            --azul-hover: #1d4ed8;
            --gris-texto: #374151;
            --borde-radio: 24px;
        }
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', 'Inter', 'Roboto', Arial, sans-serif;
            background: linear-gradient(135deg, #e0f2fe 0%, #2563eb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: var(--borde-radio);
            box-shadow: 0 8px 32px rgba(30, 64, 175, 0.10), 0 1.5px 6px rgba(0,0,0,0.04);
            max-width: 820px;
            width: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        @media (min-width: 900px) {
            .card {
                flex-direction: row;
            }
        }
        .card-left, .card-right {
            padding: 40px 28px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .card-left {
            flex: 1 1 0;
            align-items: center;
            text-align: center;
            background: none;
        }
        @media (min-width: 900px) {
            .card-left {
                align-items: flex-start;
                text-align: left;
                padding-right: 0;
            }
        }
        .logo {
            width: 90px;
            height: auto;
            margin-bottom: 18px;
        }
        h1 {
            font-size: 2.1rem;
            font-weight: 700;
            color: var(--azul-base);
            margin-bottom: 10px;
            letter-spacing: -1px;
        }
        .desc {
            color: var(--gris-texto);
            font-size: 1.08rem;
            line-height: 1.5;
            margin-bottom: 0;
        }
        .card-right {
            flex: 0 0 260px;
            align-items: center;
            justify-content: center;
            background: none;
            border-top: 1px solid #f3f4f6;
        }
        @media (min-width: 900px) {
            .card-right {
                border-top: none;
                border-left: 1px solid #f3f4f6;
                padding-left: 36px;
                align-items: flex-end;
                justify-content: center;
            }
        }
        .login-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.7em;
            background: var(--azul-base);
            color: #fff;
            font-weight: 600;
            font-size: 1.13rem;
            border: none;
            border-radius: 9999px;
            padding: 14px 32px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(37,99,235,0.08);
            cursor: pointer;
            outline: none;
            transition: background 0.18s, transform 0.18s, box-shadow 0.18s;
        }
        .login-btn:focus-visible {
            outline: 2px solid var(--azul-hover);
            outline-offset: 2px;
        }
        .login-btn:hover, .login-btn:focus-visible {
            background: var(--azul-hover);
            transform: scale(1.045);
            box-shadow: 0 8px 24px rgba(37,99,235,0.13);
        }
        .login-btn .fa-right-to-bracket {
            font-size: 1.2em;
        }
        @media (max-width: 600px) {
            .card-left, .card-right {
                padding: 28px 12px;
            }
            .logo {
                width: 70px;
            }
            h1 {
                font-size: 1.4rem;
            }
            .login-btn {
                font-size: 1rem;
                padding: 12px 20px;
            }
        }
    </style>
</head>
<body>
    <main class="card" role="main" aria-label="Bienvenida a INVEC">
        <section class="card-left">
            <img src="{{ asset('Logo/logo_menu.png') }}" class="logo" alt="Logo Cámara de Comercio e Industrias del Sur" style="box-shadow: 0 4px 18px rgba(37,99,235,0.13); border-radius: 18px;">
            <h1 style="margin-top: 6px; font-size: 2.4rem; color: #1e293b;">Sistema <span style="color: #2563eb;">INVEC</span></h1>
            <p class="desc" style="margin-top: 12px; font-size: 1.15rem; color: #475569;">
                Inventariado y Asignación de Equipo<br>
                <span style="font-weight: 500; color: #2563eb;">Cámara de Comercio e Industrias del Sur</span>
            </p>
            <div style="margin-top: 32px; display: flex; gap: 18px;">
                <div style="background: #f1f5f9; border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-shield-halved" style="color: #2563eb; font-size: 1.3em;"></i>
                    <span style="color: #334155; font-size: 1.04em;">Seguro y confiable</span>
                </div>
                <div style="background: #f1f5f9; border-radius: 12px; padding: 14px 18px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-laptop" style="color: #2563eb; font-size: 1.3em;"></i>
                    <span style="color: #334155; font-size: 1.04em;">Gestión eficiente</span>
                </div>
            </div>
        </section>
        <section class="card-right" style="background: linear-gradient(135deg, #e0e7ff 0%, #2563eb0d 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
            <div style="width: 100%; display: flex; flex-direction: column; align-items: center;">
            <a href="{{ route('login') }}" class="login-btn" aria-label="Iniciar sesión" style="margin-bottom: 18px; width: 100%; text-align: center; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 18px rgba(37,99,235,0.13);">
                <span style="background: #fff; border-radius: 50%; padding: 8px; margin-right: 12px; display: flex; align-items: center; box-shadow: 0 2px 8px rgba(37,99,235,0.10);">
                <i class="fa-solid fa-right-to-bracket" aria-hidden="true" style="color: #2563eb; font-size: 1.35em;"></i>
                </span>
                <span style="font-size: 1.18em; font-weight: 600;">Ingresar al sistema</span>
            </a>
            <div style="margin-top: 10px; color: #64748b; font-size: 0.98em; text-align: center;">
                Acceso exclusivo para personal autorizado
            </div>
            </div>
        </section>
            </div>
            
        </section>
    </main>
    
</body>
</html>

