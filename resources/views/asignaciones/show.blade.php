<x-app-layout>
    <div class="py-6 space-y-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @php
            $correlativoOficial = 'ACT-INV-' . ($asignacion->fecha_entrega?->format('Y') ?? date('Y')) . '-' . str_pad((string) $asignacion->id, 4, '0', STR_PAD_LEFT);
        @endphp

        {{-- Navegación superior y acción principal --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-brand-navy">Inicio</a>
                    <span>/</span>
                    <a href="{{ route('asignaciones.index') }}" class="hover:text-brand-navy">Asignaciones</a>
                    <span>/</span>
                    <span class="text-brand-navy font-semibold">{{ $correlativoOficial }}</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-navy tracking-tight">Acta de Entrega de Bienes</h1>
                    @if ($asignacion->trashed())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20">
                            <i class="fas fa-ban text-xs text-rose-500" aria-hidden="true"></i>
                            Asignación Anulada
                        </span>
                    @elseif ($asignacion->devolucion)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                            <i class="fas fa-check text-xs text-gray-500" aria-hidden="true"></i>
                            Devuelto (Concluido)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Asignación Activa
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Único botón de descarga/impresión oficial --}}
                <a href="{{ route('asignaciones.pdf', $asignacion->id) }}"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand-navy hover:bg-brand-navy-800 text-white rounded-lg text-xs font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                    <i class="fas fa-file-pdf text-brand-gold text-xs" aria-hidden="true"></i>
                    Descargar Acta PDF
                </a>
                @if (! $asignacion->devolucion)
                    <a href="{{ route('devoluciones.create', ['asignacion_id' => $asignacion->id]) }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                        <i class="fas fa-undo text-xs" aria-hidden="true"></i>
                        Devolver Bien
                    </a>
                @endif
            </div>
        </div>

        @if ($asignacion->trashed())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 sm:p-5 text-rose-900 shadow-sm">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="p-2 rounded-lg bg-rose-100 text-rose-600 shrink-0">
                            <i class="fas fa-ban text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-rose-950">Acta de Asignación Anulada (Dada de Baja)</h3>
                            <p class="text-xs text-rose-800 mt-0.5">
                                Anulada el <strong>{{ $asignacion->deleted_at->format('d/m/Y h:i A') }}</strong> por <strong>{{ $asignacion->anuladoPor?->name ?? 'Administración' }}</strong>.
                            </p>
                            @if ($asignacion->motivo_anulacion)
                                <p class="text-xs text-rose-900 mt-1.5 bg-rose-100/70 p-2 rounded-md font-medium">
                                    <strong>Motivo:</strong> {{ $asignacion->motivo_anulacion }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @can('admin')
                        <button type="button" id="btn-reactivar-asignacion" class="shrink-0 px-3 py-2 bg-rose-700 hover:bg-rose-800 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                            <i class="fas fa-redo-alt mr-1"></i> Reactivar Asignación
                        </button>
                    @endcan
                </div>
            </div>
        @endif

        {{-- Tarjeta Principal del Acta --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Encabezado Institucional SÓLIDO (Sin degradados blancos que dificulten lectura) --}}
            <div class="bg-brand-navy text-white p-6 sm:p-7 border-b-4 border-brand-gold">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-brand-gold">Cámara de Comercio e Industrias del Sur</p>
                        <h2 class="text-xl sm:text-2xl font-bold mt-1 text-white">Acta Formal de Asignación y Entrega</h2>
                        <p class="text-xs text-gray-300 mt-0.5">Sistema Integrado de Control de Inventario (INVEC)</p>
                    </div>
                    <div class="bg-brand-navy-800/80 px-4 py-2.5 rounded-xl border border-brand-gold/30 text-left sm:text-right shrink-0">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-brand-gold">Correlativo Oficial de Acta</p>
                        <p class="font-mono text-base sm:text-lg font-extrabold text-white tracking-wide mt-0.5">
                            {{ $correlativoOficial }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                {{-- Ficha del Destinatario --}}
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/50">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Custodia / Responsable</p>
                    @if ($asignacion->es_area_comun)
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl shrink-0">
                                <i class="fas fa-building" aria-hidden="true"></i>
                            </div>
                            <div>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Asignación a Espacio / Área Común
                                </span>
                                <h3 class="text-base font-bold text-brand-navy mt-1">{{ $asignacion->area }}</h3>
                                <p class="text-xs text-gray-500">Uso común institucional de CCISur (sin empleado exclusivo).</p>
                            </div>
                        </div>
                    @elseif ($asignacion->empleado)
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-lg shrink-0">
                                {{ strtoupper(substr($asignacion->empleado->nombre_completo, 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-brand-navy text-white">
                                        {{ $asignacion->empleado->codigo }}
                                    </span>
                                    <h3 class="text-base font-bold text-brand-navy">{{ $asignacion->empleado->nombre_completo }}</h3>
                                </div>
                                <p class="text-xs text-gray-600 mt-1">
                                    <strong>Identidad / DNI:</strong> {{ $asignacion->empleado->identidad_formateada ?? $asignacion->empleado->identidad ?? 'No registrada' }} &bull;
                                    <strong>Área / Gerencia:</strong> {{ $asignacion->area }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">Sin colaborador asociado</p>
                    @endif
                </div>

                {{-- Ficha del Bien Asignado --}}
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/50">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Información del Bien Entregado</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Descripción / Nombre del Bien</p>
                            <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $item->nombre ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Etiqueta de Inventario CCISur</p>
                            <p class="font-mono text-sm font-bold text-brand-navy mt-0.5">{{ $item->etiqueta ?? 'Sin etiqueta' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Tipo de Bien</p>
                            <p class="text-xs font-semibold text-gray-800 capitalize mt-0.5">
                                <i class="{{ $asignacion->tipo === 'dispositivo' ? 'fas fa-laptop text-blue-600' : 'fas fa-chair text-amber-600' }} mr-1" aria-hidden="true"></i>
                                {{ $asignacion->tipo }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Estado Físico Registrado</p>
                            <p class="text-xs font-semibold text-gray-800 mt-0.5">{{ $item->estado ?? 'No disponible' }}</p>
                        </div>
                        @if ($asignacion->tipo === 'dispositivo' && $item)
                            <div>
                                <p class="text-xs text-gray-500">Número de Serie</p>
                                <p class="font-mono text-xs font-bold text-gray-900 mt-0.5">{{ $item->n_serie ?: 'No aplica / No registrado' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Marca / Modelo</p>
                                <p class="text-xs font-medium text-gray-800 mt-0.5">{{ $item->marca ?? '---' }} {{ !empty($item->modelo) ? '/ '.$item->modelo : '' }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Datos de Entrega y Trazabilidad --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-gray-100 pt-4">
                    <div>
                        <p class="text-xs text-gray-500">Fecha de Entrega</p>
                        <p class="text-xs font-bold text-gray-900 mt-0.5">
                            <i class="far fa-calendar-alt text-brand-gold-700 mr-1" aria-hidden="true"></i>
                            {{ $asignacion->fecha_entrega?->format('d/m/Y') ?? '---' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Entregado por</p>
                        <p class="text-xs font-bold text-gray-900 mt-0.5">
                            <i class="far fa-user text-brand-gold-700 mr-1" aria-hidden="true"></i>
                            {{ $asignacion->entregado_por }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Identificador UUID</p>
                        <p class="font-mono text-[10px] text-gray-400 mt-0.5 truncate" title="{{ $asignacion->uuid }}">
                            {{ $asignacion->uuid }}
                        </p>
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Observaciones</p>
                    <p class="text-xs text-gray-700 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-200">
                        {{ $asignacion->observaciones ?: 'Sin observaciones particulares asentadas al momento de la entrega.' }}
                    </p>
                </div>
            </div>

            {{-- Pie de página con retorno limpio --}}
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                <a href="{{ route('asignaciones.index') }}" class="text-xs font-semibold text-gray-600 hover:text-brand-navy transition inline-flex items-center gap-1.5">
                    <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i> Volver a Asignaciones
                </a>
            </div>
        </div>
    </div>

    @if ($asignacion->trashed())
        <form id="form-reactivar-asignacion" action="{{ route('asignaciones.restore', $asignacion->id) }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="password" id="input-password-reactivar">
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const btn = document.getElementById('btn-reactivar-asignacion');
                if (btn) {
                    btn.addEventListener('click', () => {
                        Swal.fire({
                            title: '¿Reactivar esta Asignación?',
                            text: 'Al reactivar el acta, el bien asociado volverá a quedar Asignado a su custodio anterior.',
                            icon: 'question',
                            input: 'password',
                            inputPlaceholder: 'Ingrese su contraseña de usuario...',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, Reactivar Asignación',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#002855',
                            cancelButtonColor: '#6B7280',
                            preConfirm: (password) => {
                                if (!password) {
                                    Swal.showValidationMessage('Debe ingresar su contraseña para autorizar la reactivación.');
                                    return false;
                                }
                                return password;
                            }
                        }).then((result) => {
                            if (result.isConfirmed && result.value) {
                                document.getElementById('input-password-reactivar').value = result.value;
                                document.getElementById('form-reactivar-asignacion').submit();
                            }
                        });
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
