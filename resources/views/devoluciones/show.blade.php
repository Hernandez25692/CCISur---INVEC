<x-app-layout>
    <div class="py-6 space-y-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Navegación superior y acción principal --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-brand-navy">Inicio</a>
                    <span>/</span>
                    <a href="{{ route('devoluciones.index') }}" class="hover:text-brand-navy">Devoluciones</a>
                    <span>/</span>
                    <span class="text-brand-navy font-semibold">{{ $correlativo }}</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-navy tracking-tight">Acta de Devolución de Bien</h1>
                    @if ($devolucion->trashed())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20">
                            <i class="fas fa-ban text-xs text-rose-500" aria-hidden="true"></i>
                            Devolución Anulada
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Devolución Registrada (Finiquito)
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Único botón de descarga/impresión oficial --}}
                <a href="{{ route('devoluciones.pdf', $devolucion) }}"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand-navy hover:bg-brand-navy-800 text-white rounded-lg text-xs font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                    <svg class="w-4 h-4 text-brand-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar Acta PDF
                </a>
                
                <a href="{{ route('devoluciones.index') }}"
                    class="inline-flex items-center justify-center gap-1.5 px-3 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition">
                    Volver
                </a>
            </div>
        </div>

        @if ($devolucion->trashed())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 sm:p-5 text-rose-900 shadow-sm">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="p-2 rounded-lg bg-rose-100 text-rose-600 shrink-0">
                            <i class="fas fa-ban text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-rose-950">Acta de Devolución Anulada (Dada de Baja)</h3>
                            <p class="text-xs text-rose-800 mt-0.5">
                                Anulada el <strong>{{ $devolucion->deleted_at->format('d/m/Y h:i A') }}</strong> por <strong>{{ $devolucion->anuladoPor?->name ?? 'Administración' }}</strong>.
                            </p>
                            @if ($devolucion->motivo_anulacion)
                                <p class="text-xs text-rose-900 mt-1.5 bg-rose-100/70 p-2 rounded-md font-medium">
                                    <strong>Motivo:</strong> {{ $devolucion->motivo_anulacion }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @can('admin')
                        <button type="button" id="btn-reactivar-devolucion" class="shrink-0 px-3 py-2 bg-rose-700 hover:bg-rose-800 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                            <i class="fas fa-redo-alt mr-1"></i> Reactivar Devolución
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
                        <h2 class="text-xl sm:text-2xl font-bold mt-1 text-white">Acta Formal de Devolución y Recepción</h2>
                        <p class="text-xs text-gray-300 mt-0.5">Sistema Integrado de Control de Inventario (INVEC)</p>
                    </div>
                    <div class="bg-brand-navy-800/80 px-4 py-2.5 rounded-xl border border-brand-gold/30 text-left sm:text-right shrink-0">
                        <p class="text-[10px] uppercase font-bold tracking-wider text-brand-gold">Correlativo Oficial de Acta</p>
                        <p class="font-mono text-base sm:text-lg font-extrabold text-white tracking-wide mt-0.5">
                            {{ $correlativo }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                {{-- Ficha del Colaborador / Custodio que devuelve --}}
                <div class="p-4 rounded-xl border border-gray-200 bg-gray-50/50">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Colaborador / Custodio Saliente</p>
                    @if ($asignacion->es_area_comun)
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl shrink-0 font-bold">
                                AC
                            </div>
                            <div>
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Recepción desde Espacio / Área Común
                                </span>
                                <h3 class="text-base font-bold text-brand-navy mt-1">{{ $asignacion->area }}</h3>
                                <p class="text-xs text-gray-500">Bien retirado del uso común institucional de CCISur.</p>
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
                                    <strong>Identidad:</strong> {{ $asignacion->empleado->identidad_formateada ?? $asignacion->empleado->identidad ?? 'No registrada' }} &bull;
                                    <strong>Área / Gerencia:</strong> {{ $asignacion->area }}
                                </p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">Información de custodia no disponible.</p>
                    @endif
                </div>

                {{-- Detalle del Bien Recibido --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Especificaciones del Bien Devuelto</h3>
                    @if ($item)
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-sm font-bold text-brand-navy">{{ $item->etiqueta ?? 'Sin Placa' }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $asignacion->tipo === 'dispositivo' ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ ucfirst($asignacion->tipo) }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">{{ $item->tipo ?? 'General' }}</span>
                            </div>
                            <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                                <div>
                                    <span class="text-gray-500 block">Nombre / Descripción:</span>
                                    <span class="font-semibold text-gray-900">{{ $item->nombre }}</span>
                                </div>
                                @if ($asignacion->tipo === 'dispositivo')
                                    <div>
                                        <span class="text-gray-500 block">Marca / Modelo:</span>
                                        <span class="font-semibold text-gray-900">{{ $item->marca ?? '---' }} {{ $item->modelo ?? '' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block">Número de Serie:</span>
                                        <span class="font-mono font-semibold text-gray-900">{{ $item->n_serie ?? 'Sin serie registrada' }}</span>
                                    </div>
                                @else
                                    <div>
                                        <span class="text-gray-500 block">Material:</span>
                                        <span class="font-semibold text-gray-900">{{ $item->material ?? '---' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block">Color:</span>
                                        <span class="font-semibold text-gray-900">{{ $item->color ?? '---' }}</span>
                                    </div>
                                @endif
                                <div>
                                    <span class="text-gray-500 block">Estado Físico en Devolución:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-bold mt-0.5 ring-1 ring-inset {{ \App\Models\Dispositivo::ESTADOS_CLASES[$devolucion->estado_devuelto] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $devolucion->estado_devuelto ?? $item->estado }}
                                    </span>
                                    @if (! empty($devolucion->estado_anterior))
                                        <div class="text-[11px] text-gray-400 mt-1">
                                            <span>Estado previo:</span> <span class="text-gray-600 font-medium">{{ $devolucion->estado_anterior }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Disponibilidad Actual en Inventario:</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-2xs font-bold mt-0.5 ring-1 ring-inset {{ \App\Models\Dispositivo::DISPONIBILIDADES_CLASES[$item->disponibilidad] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $item->disponibilidad }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Ubicación de Resguardo:</span>
                                    <span class="font-semibold text-gray-900">{{ $item->ubicacion ?? 'Bodega / Inventario Central' }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 text-amber-800 text-xs">
                            El bien asociado a esta devolución ya no se encuentra en el catálogo activo (ha sido eliminado o reclasificado).
                        </div>
                    @endif
                </div>

                {{-- Datos del Acta y Recepción --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl border border-gray-200 bg-white">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Datos de Recepción</p>
                        <div class="space-y-1.5 text-xs text-gray-700">
                            <div><strong>Fecha de Devolución:</strong> {{ $devolucion->fecha_devolucion ? $devolucion->fecha_devolucion->format('d/m/Y') : 'N/A' }}</div>
                            <div><strong>Recibido por:</strong> {{ $devolucion->recibido_por }}</div>
                            <div><strong>Acta de Asignación Origen:</strong> #{{ $asignacion->id }} ({{ $asignacion->fecha_entrega ? $asignacion->fecha_entrega->format('d/m/Y') : 'S/F' }})</div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl border border-gray-200 bg-white">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Observaciones Registradas</p>
                        <p class="text-xs text-gray-600 italic">
                            {{ $devolucion->observaciones ?: 'Sin observaciones adicionales registradas al momento de la entrega.' }}
                        </p>
                    </div>
                </div>

                {{-- Cláusula de Finiquito --}}
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed text-justify">
                    <p class="font-bold text-brand-navy mb-1">Constancia de Recepción y Finiquito:</p>
                    Por medio de la presente acta se hace constar que el custodio ha entregado formalmente a la <strong>Cámara de Comercio e Industrias del Sur (CCISUR)</strong> el bien antes descrito, quedando plenamente liberado de la custodia material y responsabilidad directa sobre el mismo a partir de la fecha suscrita.
                </div>
            </div>
        </div>
    </div>

    @if ($devolucion->trashed())
        <form id="form-reactivar-devolucion" action="{{ route('devoluciones.restore', $devolucion->id) }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="password" id="input-password-reactivar-dev">
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const btn = document.getElementById('btn-reactivar-devolucion');
                if (btn) {
                    btn.addEventListener('click', () => {
                        Swal.fire({
                            title: '¿Reactivar esta Devolución?',
                            text: 'Al reactivar el acta, la devolución volverá a ser válida y el bien actualizará su estado en el inventario.',
                            icon: 'question',
                            input: 'password',
                            inputPlaceholder: 'Ingrese su contraseña de administrador...',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, Reactivar Devolución',
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
                                document.getElementById('input-password-reactivar-dev').value = result.value;
                                document.getElementById('form-reactivar-devolucion').submit();
                            }
                        });
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
