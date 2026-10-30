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

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Estilos del Sidebar -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}?v={{ filemtime(public_path('css/sidebar.css')) }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-800">
    <div class="min-h-screen">
        <!-- Menú de navegación (sidebar) -->
        @include('layouts.navigation')

        <!-- Área de contenido (en escritorio se corre a la derecha del sidebar) -->
        <div class="inv-main">
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

    </div>

    <!-- Alertas accesibles de respaldo -->
    @if (session('success'))
        <div id="flash-success" role="status"
            class="fixed bottom-6 right-6 bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl shadow-md z-50 flex items-center gap-2.5 text-sm font-medium">
            <i class="fas fa-check-circle text-emerald-600" aria-hidden="true"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div id="flash-error" role="alert"
            class="fixed bottom-6 right-6 bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-xl shadow-md z-50 flex items-center gap-2.5 text-sm font-medium max-w-md">
            <i class="fas fa-exclamation-circle text-red-600 shrink-0" aria-hidden="true"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div role="alert"
            class="fixed bottom-6 left-6 bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-xl shadow-md z-50 max-w-md">
            <strong class="font-semibold text-sm">¡Revisá los datos ingresados!</strong>
            <ul class="mt-1.5 list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Confirmación SweetAlert2 para formularios de eliminación permitida
            document.addEventListener('submit', function (event) {
                const form = event.target.closest('form[data-swal-delete]');
                if (!form || form.dataset.swalConfirmed === 'true') {
                    return;
                }

                event.preventDefault();

                const nombre = form.dataset.swalNombre || 'este registro';
                const etiqueta = form.dataset.swalEtiqueta ? ` (${form.dataset.swalEtiqueta})` : '';

                if (typeof window.Swal === 'undefined') {
                    if (window.confirm(`¿Estás seguro de eliminar ${nombre}${etiqueta}?`)) {
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                    return;
                }

                window.Swal.fire({
                    icon: 'warning',
                    title: '¿Eliminar este bien?',
                    text: `Se eliminará definitivamente «${nombre}»${etiqueta}. Esta acción no se puede deshacer.`,
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#031940',
                    reverseButtons: true,
                    focusCancel: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                });
            });

            // Confirmación SweetAlert2 para dar de baja a un colaborador
            document.addEventListener('submit', function (event) {
                const form = event.target.closest('form[data-swal-confirm-baja]');
                if (!form || form.dataset.swalConfirmed === 'true') {
                    return;
                }

                event.preventDefault();

                const nombre = form.dataset.swalNombre || 'este colaborador';
                const etiqueta = form.dataset.swalEtiqueta ? ` (${form.dataset.swalEtiqueta})` : '';

                if (typeof window.Swal === 'undefined') {
                    if (window.confirm(`¿Está seguro de dar de baja a ${nombre}${etiqueta}? Pasará al listado de colaboradores inactivos.`)) {
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                    return;
                }

                window.Swal.fire({
                    icon: 'warning',
                    title: '¿Dar de baja a este colaborador?',
                    text: `«${nombre}»${etiqueta} pasará al listado de colaboradores inactivos. Su historial de actas y préstamos se conservará intacto.`,
                    showCancelButton: true,
                    confirmButtonText: 'Sí, dar de baja',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#031940',
                    reverseButtons: true,
                    focusCancel: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                });
            });

            // Confirmación SweetAlert2 para reactivar a un colaborador
            document.addEventListener('submit', function (event) {
                const form = event.target.closest('form[data-swal-confirm-reactivar]');
                if (!form || form.dataset.swalConfirmed === 'true') {
                    return;
                }

                event.preventDefault();

                const nombre = form.dataset.swalNombre || 'este colaborador';

                if (typeof window.Swal === 'undefined') {
                    if (window.confirm(`¿Reactivar a ${nombre}? Volverá a estar disponible para asignaciones.`)) {
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                    return;
                }

                window.Swal.fire({
                    icon: 'question',
                    title: '¿Reactivar colaborador?',
                    text: `«${nombre}» volverá a aparecer en el listado activo y podrá recibir nuevas asignaciones.`,
                    showCancelButton: true,
                    confirmButtonText: 'Sí, reactivar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#031940',
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                });
            });

            // Modal informativo cuando un bien está protegido y NO se puede eliminar
            document.addEventListener('click', function (event) {
                const lockedBtn = event.target.closest('[data-swal-locked]');
                if (!lockedBtn) {
                    return;
                }

                event.preventDefault();
                const motivo = lockedBtn.dataset.swalLocked || 'Este registro no cumple las condiciones para ser eliminado.';
                const titulo = lockedBtn.dataset.swalTitle || 'Eliminación bloqueada';

                if (typeof window.Swal === 'undefined') {
                    window.alert(`${titulo}: ${motivo}`);
                    return;
                }

                window.Swal.fire({
                    icon: 'info',
                    title: titulo,
                    text: motivo,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#031940'
                });
            });

            // Mostrar alertas de sesión con SweetAlert2
            @if (session('success'))
                if (typeof window.Swal !== 'undefined') {
                    const fallbackSuccess = document.getElementById('flash-success');
                    if (fallbackSuccess) fallbackSuccess.classList.add('sr-only');
                    window.Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: @json(session('success')),
                        showConfirmButton: false,
                        timer: 3500,
                        timerProgressBar: true
                    });
                }
            @endif

            @if (session('error'))
                if (typeof window.Swal !== 'undefined') {
                    const fallbackError = document.getElementById('flash-error');
                    if (fallbackError) fallbackError.classList.add('sr-only');
                    window.Swal.fire({
                        icon: 'error',
                        title: 'Acción no permitida',
                        text: @json(session('error')),
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#031940'
                    });
                }
            @endif
        });
    </script>

    @stack('scripts')
</body>

</html>
