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
            class="fixed inset-x-4 bottom-4 sm:inset-x-auto sm:right-6 sm:bottom-6 max-w-[calc(100vw-2rem)] sm:max-w-md bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl shadow-md z-50 flex items-center gap-2.5 text-sm font-medium">
            <i class="fas fa-check-circle text-emerald-600 shrink-0" aria-hidden="true"></i>
            <span class="break-words">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div id="flash-error" role="alert"
            class="fixed inset-x-4 bottom-4 sm:inset-x-auto sm:right-6 sm:bottom-6 max-w-[calc(100vw-2rem)] sm:max-w-md bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-xl shadow-md z-50 flex items-center gap-2.5 text-sm font-medium">
            <i class="fas fa-exclamation-circle text-red-600 shrink-0" aria-hidden="true"></i>
            <span class="break-words">{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div id="flash-errors" role="alert"
            class="fixed inset-x-4 bottom-4 sm:inset-x-auto sm:left-6 sm:bottom-6 max-w-[calc(100vw-2rem)] sm:max-w-md bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-xl shadow-md z-50">
            <strong class="font-semibold text-sm">¡Revisá los datos ingresados!</strong>
            <ul class="mt-1.5 list-disc list-inside text-xs space-y-0.5 break-words">
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

            // Confirmación SweetAlert2 para dar de baja a un bien (mobiliario/dispositivo)
            document.addEventListener('submit', function (event) {
                const form = event.target.closest('form[data-swal-confirm-baja-bien]');
                if (!form || form.dataset.swalConfirmed === 'true') {
                    return;
                }

                event.preventDefault();

                const nombre = form.dataset.swalNombre || 'este bien';
                const etiqueta = form.dataset.swalEtiqueta ? ` (${form.dataset.swalEtiqueta})` : '';

                if (typeof window.Swal === 'undefined') {
                    const motivo = window.prompt(`¿Está seguro de dar de baja «${nombre}»${etiqueta}?\nIngrese el motivo de la baja (mínimo 3 caracteres):`);
                    if (motivo === null) {
                        return; // Usuario canceló
                    }
                    const motivoTrimmed = motivo.trim();
                    if (motivoTrimmed.length < 3) {
                        window.alert('Debe ingresar un motivo válido de al menos 3 caracteres.');
                        return;
                    }
                    let inputMotivo = form.querySelector('input[name="motivo_baja"]');
                    if (!inputMotivo) {
                        inputMotivo = document.createElement('input');
                        inputMotivo.type = 'hidden';
                        inputMotivo.name = 'motivo_baja';
                        form.appendChild(inputMotivo);
                    }
                    inputMotivo.value = motivoTrimmed;
                    form.dataset.swalConfirmed = 'true';
                    form.submit();
                    return;
                }

                window.Swal.fire({
                    icon: 'warning',
                    title: '¿Dar de baja este bien?',
                    html: `
                        <p class="text-sm text-gray-600 mb-3 text-left">
                            «<strong id="swal-baja-bien-nombre"></strong>»<span id="swal-baja-bien-etiqueta"></span> pasará al archivo de bienes dados de baja. Su historial de actas y préstamos se conservará intacto.
                        </p>
                        <div class="text-left">
                            <label for="swal-select-motivo-baja" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1">
                                Motivo de la baja <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <select id="swal-select-motivo-baja" aria-required="true" class="w-full text-sm border border-gray-300 rounded-lg p-2.5 bg-white text-gray-800 focus:ring-2 focus:ring-amber-500 focus:outline-none mb-2">
                                <option value="" disabled selected>Seleccione un motivo sugerido...</option>
                                <option value="Daño irreparable / Falla técnica definitiva">Daño irreparable / Falla técnica definitiva</option>
                                <option value="Obsolescencia técnica / Fin de vida útil">Obsolescencia técnica / Fin de vida útil</option>
                                <option value="Pérdida / Extravío o Robo reportado">Pérdida / Extravío o Robo reportado</option>
                                <option value="Donación o Descarte formal">Donación o Descarte formal</option>
                                <option value="Reemplazo por renovación institucional">Reemplazo por renovación institucional</option>
                                <option value="OTRO">Otro motivo personalizado...</option>
                            </select>
                            <textarea id="swal-text-motivo-baja" aria-label="Motivo detallado de la baja" class="w-full text-sm border border-gray-300 rounded-lg p-2.5 text-gray-800 focus:ring-2 focus:ring-amber-500 focus:outline-none hidden" rows="2" placeholder="Describa el motivo detallado de la baja..."></textarea>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Sí, dar de baja',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#031940',
                    reverseButtons: true,
                    focusCancel: true,
                    didOpen: () => {
                        const nombreEl = document.getElementById('swal-baja-bien-nombre');
                        const etiquetaEl = document.getElementById('swal-baja-bien-etiqueta');
                        if (nombreEl) nombreEl.textContent = nombre;
                        if (etiquetaEl) etiquetaEl.textContent = etiqueta;

                        const select = document.getElementById('swal-select-motivo-baja');
                        const textarea = document.getElementById('swal-text-motivo-baja');
                        if (select) {
                            select.addEventListener('change', () => {
                                if (select.value === 'OTRO') {
                                    textarea.classList.remove('hidden');
                                    textarea.focus();
                                } else {
                                    textarea.classList.add('hidden');
                                }
                            });
                        }
                    },
                    preConfirm: () => {
                        const select = document.getElementById('swal-select-motivo-baja');
                        const textarea = document.getElementById('swal-text-motivo-baja');
                        let motivo = select ? select.value : '';
                        if (motivo === 'OTRO') {
                            motivo = textarea ? textarea.value.trim() : '';
                            if (motivo.length < 3) {
                                window.Swal.showValidationMessage('Por favor escriba el motivo detallado (mínimo 3 caracteres).');
                                return false;
                            }
                        } else if (!motivo) {
                            window.Swal.showValidationMessage('Debe seleccionar o escribir el motivo de la baja.');
                            return false;
                        }
                        return motivo;
                    }
                }).then(function (result) {
                    if (result.isConfirmed && result.value) {
                        let inputMotivo = form.querySelector('input[name="motivo_baja"]');
                        if (!inputMotivo) {
                            inputMotivo = document.createElement('input');
                            inputMotivo.type = 'hidden';
                            inputMotivo.name = 'motivo_baja';
                            form.appendChild(inputMotivo);
                        }
                        inputMotivo.value = result.value;
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                });
            });

            // Función utilitaria para sanitizar valores interpolados en HTML de SweetAlert2
            function escapeSwal(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // Confirmación SweetAlert2 para reactivar un bien
            document.addEventListener('submit', function (event) {
                const form = event.target.closest('form[data-swal-confirm-reactivar-bien]');
                if (!form || form.dataset.swalConfirmed === 'true') {
                    return;
                }

                event.preventDefault();

                const nombre = form.dataset.swalNombre || 'este bien';
                const etiqueta = form.dataset.swalEtiqueta ? ` (${form.dataset.swalEtiqueta})` : '';

                if (typeof window.Swal === 'undefined') {
                    if (window.confirm(`¿Reactivar «${nombre}»${etiqueta}? Volverá a estar en el inventario activo.`)) {
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                    return;
                }

                window.Swal.fire({
                    icon: 'question',
                    title: '¿Reactivar este bien?',
                    html: `
                        <div class="text-left text-xs text-gray-600 space-y-3 mt-2">
                            <p>«<strong>${escapeSwal(nombre)}</strong>»${escapeSwal(etiqueta)} volverá a aparecer en el inventario activo.</p>
                            <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                                <label class="flex items-start gap-2 cursor-pointer font-semibold text-amber-950 text-xs">
                                    <input type="checkbox" id="swal_enviar_a_taller" class="mt-0.5 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                    <span>¿Enviar directamente a Taller / Revisión técnica tras reactivar?</span>
                                </label>
                                <div id="swal_motivo_taller_wrapper" class="hidden mt-2.5">
                                    <label class="block text-[11px] font-medium text-amber-900 mb-1">Motivo o falla a revisar (opcional):</label>
                                    <input type="text" id="swal_motivo_taller" placeholder="Ej. Reingreso a taller para reemplazo de componentes..."
                                        class="w-full text-xs rounded-lg border border-amber-300 p-2 focus:ring-amber-500 focus:border-amber-500 bg-white">
                                </div>
                            </div>
                        </div>
                    `,
                    didOpen: () => {
                        const checkTaller = document.getElementById('swal_enviar_a_taller');
                        const wrapper = document.getElementById('swal_motivo_taller_wrapper');
                        const inputMotivo = document.getElementById('swal_motivo_taller');
                        if (checkTaller && wrapper) {
                            checkTaller.addEventListener('change', () => {
                                if (checkTaller.checked) {
                                    wrapper.classList.remove('hidden');
                                    inputMotivo?.focus();
                                } else {
                                    wrapper.classList.add('hidden');
                                }
                            });
                        }
                    },
                    preConfirm: () => {
                        const checkTaller = document.getElementById('swal_enviar_a_taller');
                        const inputMotivo = document.getElementById('swal_motivo_taller');
                        return {
                            enviarATaller: checkTaller ? checkTaller.checked : false,
                            motivoTaller: inputMotivo ? inputMotivo.value.trim() : ''
                        };
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Sí, reactivar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#031940',
                    reverseButtons: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        if (result.value && result.value.enviarATaller) {
                            let inputTaller = form.querySelector('input[name="enviar_a_taller"]');
                            if (!inputTaller) {
                                inputTaller = document.createElement('input');
                                inputTaller.type = 'hidden';
                                inputTaller.name = 'enviar_a_taller';
                                form.appendChild(inputTaller);
                            }
                            inputTaller.value = '1';

                            let inputMotivo = form.querySelector('input[name="motivo_taller"]');
                            if (!inputMotivo) {
                                inputMotivo = document.createElement('input');
                                inputMotivo.type = 'hidden';
                                inputMotivo.name = 'motivo_taller';
                                form.appendChild(inputMotivo);
                            }
                            inputMotivo.value = result.value.motivoTaller;
                        }
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                });
            });

            // Confirmación SweetAlert2 para cancelar registro de mantenimiento
            document.addEventListener('submit', function (event) {
                const form = event.target.closest('form[data-swal-confirm-cancelar-mantenimiento]');
                if (!form || form.dataset.swalConfirmed === 'true') {
                    return;
                }

                event.preventDefault();

                const bien = form.dataset.swalBien || 'este bien';
                const etiqueta = form.dataset.swalEtiqueta ? ` (${form.dataset.swalEtiqueta})` : '';
                const estadoAnterior = form.dataset.swalEstadoAnterior || 'su estado previo';
                const tieneCustodio = form.dataset.swalTieneCustodio === 'true';
                const custodioNombre = form.dataset.swalCustodioNombre || 'el colaborador';

                if (typeof window.Swal === 'undefined') {
                    if (window.confirm(`¿Cancelar este registro de mantenimiento para «${bien}»${etiqueta}? El bien volverá a estado: ${estadoAnterior}.`)) {
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                    return;
                }

                const custodiaTexto = tieneCustodio
                    ? `<li class="flex items-start gap-1.5"><i class="fas fa-user-check text-emerald-600 mt-0.5"></i> <span>La custodia de <strong>${escapeSwal(custodioNombre)}</strong> se mantendrá activa sin alteraciones.</span></li>`
                    : `<li class="flex items-start gap-1.5"><i class="fas fa-warehouse text-blue-600 mt-0.5"></i> <span>El bien volverá a quedar disponible en bodega institucional.</span></li>`;

                window.Swal.fire({
                    icon: 'warning',
                    title: '¿Cancelar Mantenimiento?',
                    html: `
                        <div class="text-left text-xs text-gray-600 space-y-3 mt-2">
                            <p>Estás a punto de anular el ticket de taller de «<strong>${escapeSwal(bien)}</strong>»${escapeSwal(etiqueta)}.</p>

                            <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-amber-900 space-y-1.5">
                                <p class="font-semibold text-xs flex items-center gap-1.5">
                                    <i class="fas fa-info-circle text-amber-600"></i> Acciones automáticas al cancelar:
                                </p>
                                <ul class="space-y-1 text-[11px] text-amber-800 list-none pl-1">
                                    <li class="flex items-start gap-1.5"><i class="fas fa-ban text-amber-600 mt-0.5"></i> <span>El ticket quedará marcado como <strong>Cancelado</strong> en la bitácora técnica.</span></li>
                                    <li class="flex items-start gap-1.5"><i class="fas fa-undo text-amber-600 mt-0.5"></i> <span>El bien retornará a su estado físico previo: <strong>${escapeSwal(estadoAnterior)}</strong>.</span></li>
                                    ${custodiaTexto}
                                </ul>
                            </div>

                            <div>
                                <label for="swal_motivo_cancelacion" class="block font-semibold text-gray-700 mb-1">
                                    Motivo de cancelación (opcional):
                                </label>
                                <input type="text" id="swal_motivo_cancelacion" placeholder="Ej. Registro ingresado por error, no requería taller..."
                                    class="w-full text-xs rounded-lg border border-gray-300 p-2 focus:ring-brand-gold focus:border-brand-navy bg-white">
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cancelar mantenimiento',
                    cancelButtonText: 'Volver',
                    confirmButtonColor: '#b91c1c',
                    cancelButtonColor: '#031940',
                    reverseButtons: true,
                    preConfirm: () => {
                        const inputMotivo = document.getElementById('swal_motivo_cancelacion');
                        return {
                            motivoCancelacion: inputMotivo ? inputMotivo.value.trim() : ''
                        };
                    }
                }).then(function (result) {
                    if (result.isConfirmed) {
                        if (result.value && result.value.motivoCancelacion) {
                            let inputMotivo = form.querySelector('input[name="motivo_cancelacion"]');
                            if (!inputMotivo) {
                                inputMotivo = document.createElement('input');
                                inputMotivo.type = 'hidden';
                                inputMotivo.name = 'motivo_cancelacion';
                                form.appendChild(inputMotivo);
                            }
                            inputMotivo.value = result.value.motivoCancelacion;
                        }
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                });
            });

            // Confirmación SweetAlert2 para revertir cambio de hardware
            document.addEventListener('submit', function (event) {
                const form = event.target.closest('form[data-swal-confirm-revertir-hardware]');
                if (!form || form.dataset.swalConfirmed === 'true') {
                    return;
                }

                event.preventDefault();

                const etiqueta = form.dataset.swalEtiqueta ? ` en ${form.dataset.swalEtiqueta}` : '';

                if (typeof window.Swal === 'undefined') {
                    if (window.confirm(`¿Estás seguro de revertir esta modificación de hardware? Se restaurarán los componentes anteriores${etiqueta}.`)) {
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                    return;
                }

                window.Swal.fire({
                    icon: 'warning',
                    title: '¿Revertir modificación de hardware?',
                    text: `Se restaurarán las especificaciones técnicas previas a este cambio${etiqueta}. La acción quedará asentada en la bitácora.`,
                    input: 'text',
                    inputLabel: 'Motivo de la reversión (opcional):',
                    inputPlaceholder: 'Ej. Error de captura o pieza defectuosa',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, revertir cambio',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#031940',
                    reverseButtons: true,
                    focusCancel: true
                }).then(function (result) {
                    if (result.isConfirmed) {
                        if (result.value && result.value.trim() !== '') {
                            let motivoInput = form.querySelector('input[name="motivo_reversion"]');
                            if (!motivoInput) {
                                motivoInput = document.createElement('input');
                                motivoInput.type = 'hidden';
                                motivoInput.name = 'motivo_reversion';
                                form.appendChild(motivoInput);
                            }
                            motivoInput.value = result.value.trim();
                        }
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

            // Confirmación SweetAlert2 para habilitar/deshabilitar usuario
            document.addEventListener('submit', function (event) {
                const form = event.target.closest('form[data-swal-toggle-usuario]');
                if (!form || form.dataset.swalConfirmed === 'true') {
                    return;
                }

                event.preventDefault();

                const nombre = form.dataset.swalNombre || 'este usuario';
                const accion = form.dataset.swalAccion || 'deshabilitar';
                const esDeshabilitar = accion === 'deshabilitar';
                const esSuperadmin = form.dataset.swalEsSuperadmin === 'true';

                if (esSuperadmin) {
                    const tituloSuper = esDeshabilitar ? '¿Desactivar Superadministrador?' : '¿Activar Superadministrador?';
                    const htmlSuper = `
                        <p class="text-sm text-gray-600 mb-3">
                            Estás a punto de ${esDeshabilitar ? 'desactivar' : 'activar'} la cuenta con privilegios de Superadministrador de <strong>«${escapeSwal(nombre)}»</strong>.
                        </p>
                        <p class="text-xs text-purple-900 bg-purple-50 p-3 rounded-lg border border-purple-200 text-left font-medium">
                            <i class="fas fa-crown text-amber-500 mr-1.5"></i> <strong>Autorización requerida:</strong> Por seguridad institucional, ingresa tu contraseña actual de Superadministrador para autorizar esta acción.
                        </p>
                    `;

                    if (typeof window.Swal === 'undefined') {
                        const pwd = window.prompt(`${tituloSuper}\n\nIngresa tu contraseña actual de Superadministrador para autorizar:`);
                        if (pwd) {
                            let inputPwd = form.querySelector('input[name="superadmin_password"]');
                            if (!inputPwd) {
                                inputPwd = document.createElement('input');
                                inputPwd.type = 'hidden';
                                inputPwd.name = 'superadmin_password';
                                form.appendChild(inputPwd);
                            }
                            inputPwd.value = pwd;
                            form.dataset.swalConfirmed = 'true';
                            form.submit();
                        }
                        return;
                    }

                    window.Swal.fire({
                        icon: 'warning',
                        title: tituloSuper,
                        html: htmlSuper,
                        input: 'password',
                        inputPlaceholder: 'Tu contraseña actual de Superadministrador',
                        inputAttributes: {
                            autocapitalize: 'off',
                            autocorrect: 'off',
                            autocomplete: 'current-password'
                        },
                        showCancelButton: true,
                        confirmButtonText: esDeshabilitar ? 'Sí, desactivar' : 'Sí, activar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: esDeshabilitar ? '#dc2626' : '#059669',
                        cancelButtonColor: '#031940',
                        reverseButtons: true,
                        focusCancel: true,
                        preConfirm: (password) => {
                            if (!password) {
                                window.Swal.showValidationMessage('Debes ingresar tu contraseña para autorizar.');
                            }
                            return password;
                        }
                    }).then(function (result) {
                        if (result.isConfirmed && result.value) {
                            let inputPwd = form.querySelector('input[name="superadmin_password"]');
                            if (!inputPwd) {
                                inputPwd = document.createElement('input');
                                inputPwd.type = 'hidden';
                                inputPwd.name = 'superadmin_password';
                                form.appendChild(inputPwd);
                            }
                            inputPwd.value = result.value;
                            form.dataset.swalConfirmed = 'true';
                            form.submit();
                        }
                    });
                    return;
                }

                const titulo = esDeshabilitar ? '¿Deshabilitar cuenta de usuario?' : '¿Habilitar cuenta de usuario?';
                const texto = esDeshabilitar
                    ? `«${nombre}» ya no podrá iniciar sesión en INVEC y sus sesiones activas serán cerradas de inmediato.`
                    : `«${nombre}» volverá a tener acceso al sistema con sus credenciales habituales.`;
                const confirmText = esDeshabilitar ? 'Sí, deshabilitar' : 'Sí, habilitar';
                const confirmColor = esDeshabilitar ? '#dc2626' : '#059669';

                if (typeof window.Swal === 'undefined') {
                    if (window.confirm(`${titulo}\n\n${texto}`)) {
                        form.dataset.swalConfirmed = 'true';
                        form.submit();
                    }
                    return;
                }

                window.Swal.fire({
                    icon: esDeshabilitar ? 'warning' : 'question',
                    title: titulo,
                    text: texto,
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: confirmColor,
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
                        title: 'Error de Autorización',
                        text: @json(session('error')),
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#031940'
                    });
                }
            @endif

            @if ($errors->any() && !session('error'))
                if (typeof window.Swal !== 'undefined') {
                    const fallbackErrors = document.getElementById('flash-errors');
                    if (fallbackErrors) fallbackErrors.classList.add('sr-only');
                    window.Swal.fire({
                        icon: 'error',
                        title: '¡Revisá los datos ingresados!',
                        html: `{!! '<ul class="text-left text-sm text-gray-700 list-disc list-inside space-y-1.5 my-2">' . implode('', array_map(fn($e) => '<li>' . e($e) . '</li>', $errors->all())) . '</ul>' !!}`,
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#031940'
                    });
                }
            @endif
        });
    </script>

    @if (auth()->check() && auth()->user()->debeCambiarPassword())
        @include('auth.partials.primer-cambio-password-modal')
    @endif

    @include('auth.partials.inactivity-monitor')

    @stack('scripts')
</body>

</html>
