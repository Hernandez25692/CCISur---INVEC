@auth
    <x-app-layout>
        <div class="py-2 sm:py-4 max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden text-center">
                {{-- Encabezado con franja institucional --}}
                <div class="h-1.5 bg-gradient-to-r from-brand-navy via-brand-gold to-brand-navy"></div>

                <div class="p-6 sm:p-8">
                    <div class="mx-auto w-13 h-13 rounded-xl bg-red-50 border border-red-200 flex items-center justify-center text-red-600 shadow-2xs mb-3">
                        <i class="fas fa-server text-xl" aria-hidden="true"></i>
                    </div>

                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-red-50 border border-red-200 text-red-800 text-[11px] font-semibold mb-2">
                        Error 500 &bull; Servidor
                    </div>

                    <h2 class="text-xl sm:text-2xl font-bold text-brand-navy tracking-tight mb-1.5">
                        Ocurrió un error inesperado
                    </h2>

                    <p class="text-xs text-gray-500 max-w-md mx-auto leading-relaxed mb-6">
                        El sistema encontró un inconveniente procesando la solicitud. Si el problema persiste, notifíquelo a soporte técnico.
                    </p>

                    <div class="flex items-center justify-center">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-brand-navy text-white text-xs font-semibold hover:bg-brand-navy-700 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition shadow-xs">
                            <i class="fas fa-home text-xs" aria-hidden="true"></i>
                            <span>Ir al Inicio</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
@else
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Error del Servidor (Error 500) - INVEC</title>
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

            <div class="mx-auto w-16 h-16 rounded-2xl bg-red-50 border border-red-200 flex items-center justify-center text-red-600 mb-4 shadow-xs">
                <i class="fas fa-triangle-exclamation text-2xl" aria-hidden="true"></i>
            </div>

            <span class="inline-block px-3 py-1 rounded-full bg-red-50 text-red-800 border border-red-200 text-xs font-semibold mb-3">
                Error 500 &bull; Servidor
            </span>

            <h1 class="text-xl font-bold text-brand-navy mb-2">Error Inesperado</h1>
            <p class="text-xs text-gray-600 leading-relaxed mb-6">
                Ocurrió un problema procesando la solicitud en el servidor. Por favor intenta nuevamente más tarde.
            </p>

            <a href="{{ route('login') }}"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-brand-navy text-white text-sm font-semibold hover:bg-brand-navy-700 transition shadow-xs">
                <i class="fas fa-home text-xs"></i>
                <span>Ir al Sistema</span>
            </a>
        </div>
    </body>
    </html>
@endauth
