@auth
    <x-app-layout>
        <div class="py-2 sm:py-4 max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Encabezado con franja institucional --}}
                <div class="h-1.5 bg-gradient-to-r from-brand-navy via-brand-gold to-brand-navy"></div>

                <div class="p-6 sm:p-7 text-center">
                    {{-- Icono representativo compacto --}}
                    <div class="mx-auto w-13 h-13 rounded-xl bg-amber-50 border border-amber-200/70 flex items-center justify-center text-amber-600 shadow-2xs mb-3">
                        <i class="fas fa-lock text-xl" aria-hidden="true"></i>
                    </div>

                    {{-- Badges de Estado --}}
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-semibold mb-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Error 403 &bull; Módulo no autorizado
                    </div>

                    <h2 class="text-xl sm:text-2xl font-bold text-brand-navy tracking-tight mb-1.5">
                        No tienes permisos para acceder a esta sección
                    </h2>

                    <p class="text-xs text-gray-500 max-w-md mx-auto leading-relaxed mb-4">
                        {{ $exception->getMessage() && !str_contains($exception->getMessage(), 'This action is unauthorized')
                            ? $exception->getMessage()
                            : 'El recurso o módulo al que intentas ingresar está restringido según las políticas de seguridad institucional de CCISUR.' }}
                    </p>

                    {{-- Tarjeta informativa del perfil actual --}}
                    <div class="bg-gray-50/80 rounded-xl border border-gray-200/80 p-3.5 max-w-md mx-auto text-left mb-3.5 space-y-2">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-200 text-xs">
                            <span class="text-gray-500 font-medium">Usuario autenticado:</span>
                            <span class="font-semibold text-brand-navy">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-2 border-b border-gray-200 text-xs">
                            <span class="text-gray-500 font-medium">Correo institucional:</span>
                            <span class="font-mono text-gray-700">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 font-medium">Rol actual asignado:</span>
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full font-semibold {{ auth()->user()->esLectura() ? 'bg-sky-50 text-sky-800 border border-sky-200' : 'bg-brand-navy-50 text-brand-navy border border-brand-navy-200/60' }}">
                                <i class="fas {{ auth()->user()->esLectura() ? 'fa-eye' : 'fa-user-shield' }} text-[10px]"></i>
                                {{ auth()->user()->rol_nombre ?? ucfirst(auth()->user()->rol) }}
                            </span>
                        </div>
                    </div>

                    {{-- Mensaje de orientación según rol --}}
                    @if (auth()->user()->esLectura())
                        <div class="p-3 rounded-xl bg-blue-50/70 border border-blue-200/60 max-w-md mx-auto text-xs text-blue-900 text-left mb-4 flex items-start gap-2.5">
                            <i class="fas fa-info-circle text-blue-600 text-sm shrink-0 mt-0.5" aria-hidden="true"></i>
                            <div class="text-[11px] leading-relaxed">
                                <span class="font-semibold text-blue-900">Permisos de Solo Lectura:</span>
                                Puedes consultar inventarios y actas. Los módulos de administración de usuarios y mutaciones requieren nivel <strong>Administrador</strong> o <strong>Superadministrador</strong>.
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-xl bg-blue-50/70 border border-blue-200/60 max-w-md mx-auto text-xs text-blue-900 text-left mb-4 flex items-start gap-2.5">
                            <i class="fas fa-info-circle text-blue-600 text-sm shrink-0 mt-0.5" aria-hidden="true"></i>
                            <div class="text-[11px] leading-relaxed">
                                <span class="font-semibold text-blue-900">Control de Privilegios:</span>
                                Este módulo es de acceso exclusivo para el personal con el rol jerárquico correspondiente.
                            </div>
                        </div>
                    @endif

                    {{-- Botón de Navegación --}}
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
        <title>Acceso Denegado (Error 403) - INVEC</title>
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
                <i class="fas fa-lock text-2xl" aria-hidden="true"></i>
            </div>

            <span class="inline-block px-3 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold mb-3">
                Error 403 &bull; No autorizado
            </span>

            <h1 class="text-xl font-bold text-brand-navy mb-2">Acceso Restringido</h1>
            <p class="text-xs text-gray-600 leading-relaxed mb-6">
                No tienes autorización para acceder a este recurso. Por favor inicia sesión con una cuenta autorizada para continuar.
            </p>

            <a href="{{ route('login') }}"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-brand-navy text-white text-sm font-semibold hover:bg-brand-navy-700 transition shadow-xs">
                <i class="fas fa-sign-in-alt text-xs"></i>
                <span>Iniciar Sesión</span>
            </a>
        </div>
    </body>
    </html>
@endauth
