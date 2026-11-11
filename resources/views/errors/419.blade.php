<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesión Expirada (Error 419) - INVEC</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 font-sans text-gray-800 antialiased">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden text-center p-8">
        <div class="mb-6 flex justify-center">
            <img src="{{ asset('Logo/logo_devo.png') }}" alt="CCISUR" class="h-14 object-contain">
        </div>

        <div class="mx-auto w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 mb-4 shadow-xs">
            <i class="fas fa-clock-rotate-left text-2xl" aria-hidden="true"></i>
        </div>

        <span class="inline-block px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold mb-3">
            Error 419 &bull; Sesión caducada
        </span>

        <h1 class="text-xl font-bold text-brand-navy mb-2">Tu sesión ha expirado</h1>
        <p class="text-xs text-gray-600 leading-relaxed mb-6">
            Por motivos de seguridad institucional y protección de datos, la sesión se cerró tras un período de inactividad o el token del formulario expiró.
        </p>

        <div class="space-y-2.5">
            <a href="{{ route('login') }}"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-brand-navy text-white text-sm font-semibold hover:bg-brand-navy-700 transition shadow-xs">
                <i class="fas fa-sign-in-alt text-xs"></i>
                <span>Iniciar Sesión Nuevamente</span>
            </a>

            <button type="button" onclick="window.location.reload()"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 transition cursor-pointer">
                <i class="fas fa-rotate text-xs"></i>
                <span>Recargar Página</span>
            </button>
        </div>
    </div>
</body>
</html>
