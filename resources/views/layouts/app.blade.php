<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div class="min-h-screen flex flex-col">
        <!-- Menú de navegación -->
        @include('layouts.navigation')

        <!-- Cabecera de página si existe -->
        @isset($header)
            <header class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Contenido de la página -->
        <main class="py-8 px-4 sm:px-6 lg:px-8 w-full">
            <div class="max-w-screen-xl mx-auto">
                {{ $slot }}
            </div>
        </main>

    </div>

    <!-- Alertas -->
    @if (session('success'))
        <div
            class="fixed bottom-6 right-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow z-50">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="fixed bottom-6 left-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50">
            <strong>¡Error!</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @stack('scripts')
</body>

</html>
