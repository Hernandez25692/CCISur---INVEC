<x-app-layout>
    <div class="py-6 space-y-6 max-w-4xl mx-auto">
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
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
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

            <div class="flex flex-wrap items-center gap-2">
                {{-- Único botón de descarga/impresión oficial --}}
                <a href="{{ route('devoluciones.pdf', $devolucion) }}"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-brand-navy hover:bg-brand-navy-700 text-white rounded-lg text-xs font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
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
                        @php
                            $motivoNoReactivable = $devolucion->motivoNoReactivable(auth()->user());
                        @endphp
                        @if ($motivoNoReactivable)
                            <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2 bg-rose-100/90 text-rose-800 text-xs font-semibold rounded-lg border border-rose-300 cursor-not-allowed select-none max-w-sm text-center"
                                title="{{ $motivoNoReactivable }}">
                                <i class="fas fa-lock text-xs"></i>
                                <span>{{ $motivoNoReactivable }}</span>
                            </span>
                        @else
                            <button type="button" id="btn-reactivar-devolucion"
                                data-es-superadmin="{{ auth()->user()?->esSuperAdmin() ? '1' : '0' }}"
                                data-dias-anulado="{{ $devolucion->deleted_at ? (int) $devolucion->deleted_at->diffInDays(now()) : 0 }}"
                                class="shrink-0 px-3 py-2 bg-rose-700 hover:bg-rose-800 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                <i class="fas fa-redo-alt mr-1"></i> Reactivar Devolución
                            </button>
                        @endif
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
                    <div class="bg-brand-navy-900/80 px-4 py-2.5 rounded-xl border border-brand-gold/30 text-left sm:text-right shrink-0">
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
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex flex-wrap justify-between items-center gap-2">
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
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold mt-0.5 ring-1 ring-inset {{ \App\Models\Dispositivo::ESTADOS_CLASES[$devolucion->estado_devuelto] ?? 'bg-gray-100 text-gray-700' }}">
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
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold mt-0.5 ring-1 ring-inset {{ \App\Models\Dispositivo::DISPONIBILIDADES_CLASES[$item->disponibilidad] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $item->disponibilidad }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 block">Ubicación de Resguardo:</span>
                                    <span class="font-semibold text-gray-900">{{ $item->ubicacion ?? 'Bodega / Inventario Central' }}</span>
                                </div>
                            </div>
                        </div>

                        @if ((($asignacion->cambiosHardwareVigentes && $asignacion->cambiosHardwareVigentes->isNotEmpty()) || $mantenimientosCustodia->isNotEmpty()))
                            <div class="mt-4 p-4 rounded-xl border border-amber-200 bg-amber-50/60 space-y-3">
                                <div class="flex items-center gap-2 text-xs font-bold text-amber-900 uppercase tracking-wider">
                                    <i class="fas fa-tools text-brand-gold-600" aria-hidden="true"></i>
                                    <span>Intervenciones Técnicas Registradas Durante la Custodia</span>
                                </div>
                                <p class="text-xs text-amber-800">
                                    Durante el período en que el custodio mantuvo este equipo, se registraron las siguientes intervenciones técnicas autorizadas:
                                </p>
                                <ul class="divide-y divide-amber-200/60 bg-white rounded-lg border border-amber-200/60 overflow-hidden text-xs">
                                    @foreach ($asignacion->cambiosHardwareVigentes ?? [] as $cambio)
                                        <li class="p-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                            <div>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200 font-mono">⚡ Hardware #HW-{{ str_pad($cambio->id, 5, '0', STR_PAD_LEFT) }}</span>
                                                <span class="text-gray-500 text-xs ml-1">({{ $cambio->created_at->format('d/m/Y') }}):</span>
                                                @php
                                                    $cambiosStr = collect($cambio->cambios ?? [])
                                                        ->map(fn($c) => ($c['etiqueta'] ?? $c['campo']) . ': ' . ($c['antes'] ?? 'N/A') . ' → ' . ($c['despues'] ?? 'N/A'))
                                                        ->implode(' · ');
                                                @endphp
                                                <span class="text-gray-800 font-medium ml-1">{{ $cambiosStr }}</span>
                                                @if ($cambio->motivo)
                                                    <span class="block text-gray-500 italic text-[11px] mt-0.5">Motivo: {{ $cambio->motivo }}</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('dispositivos.cambios-hardware.pdf', $cambio->id) }}" target="_blank"
                                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-brand-navy hover:text-brand-gold transition shrink-0">
                                                <i class="fas fa-file-pdf text-brand-gold-600"></i> Ver Constancia
                                            </a>
                                        </li>
                                    @endforeach
                                    @foreach ($mantenimientosCustodia as $mCustodia)
                                        <li class="p-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                            <div>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200 font-mono">🛠️ Mantenimiento #{{ $mCustodia->numero_secuencial }}</span>
                                                @if ($mCustodia->estado === 'cancelado')
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600 border border-gray-300 ml-1">Cancelado</span>
                                                @endif
                                                <span class="text-gray-500 text-xs ml-1">({{ $mCustodia->fecha_inicio?->format('d/m/Y') }}):</span>
                                                <span class="text-gray-800 font-medium ml-1">{{ $mCustodia->tipo }} - Falla: {{ $mCustodia->motivo_falla }}</span>
                                                @if ($mCustodia->solucion_aplicada)
                                                    <span class="block text-gray-600 text-[11px] mt-0.5"><strong>Solución:</strong> {{ $mCustodia->solucion_aplicada }}</span>
                                                @endif
                                                @if ($mCustodia->taller_proveedor)
                                                    <span class="block text-gray-400 text-[10px] mt-0.5">Taller: {{ $mCustodia->taller_proveedor }}</span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
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
                            @if (!empty($devolucion->observaciones_pdf))
                                {{ $devolucion->observaciones_pdf }}
                            @elseif ($devolucion->estado_devuelto === 'Dañado / Defectuoso')
                                El bien se recibe formalmente con averías físicas o fallas no reparables, procediendo a su desincorporación y pase al archivo de bajas.
                            @elseif ($devolucion->estado_devuelto === 'En reparación / En revisión')
                                El bien se recibe formalmente para su respectiva inspección y revisión técnica en taller.
                            @else
                                Sin observaciones adicionales registradas al momento de la entrega.
                            @endif
                        </p>
                    </div>

                    @if (!empty($devolucion->notas_reactivacion))
                        <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/70 text-xs text-blue-900 shadow-xs">
                            <p class="font-bold flex items-center gap-1.5 mb-1.5 text-blue-950">
                                <i class="fas fa-history text-blue-600"></i> Historial de Reactivación del Acta
                            </p>
                            <div class="space-y-1.5 mt-2">
                                @foreach ($devolucion->notas_reactivacion as $nota)
                                    <div class="font-mono text-xs text-blue-800 bg-white/80 p-2.5 rounded-lg border border-blue-200/60 shadow-xs flex items-center gap-2">
                                        <i class="fas fa-info-circle text-blue-500 text-xs shrink-0"></i>
                                        <span>{{ $nota }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Cláusula de Finiquito --}}
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed text-left sm:text-justify">
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
                        const esSuperAdmin = btn.getAttribute('data-es-superadmin') === '1';
                        const diasAnulado = parseInt(btn.getAttribute('data-dias-anulado') || '0', 10);

                        let avisoEspecial = '';
                        if (esSuperAdmin && diasAnulado > 10) {
                            avisoEspecial = `
                                <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs rounded-lg p-2.5 mb-3 text-left flex items-start gap-2">
                                    <i class="fas fa-shield-alt text-amber-600 mt-0.5"></i>
                                    <span><strong>Autorización Especial:</strong> Esta devolución tiene ${diasAnulado} días de anulada (plazo estándar de 10 días vencido). Como Superadministrador, usted puede autorizar la reactivación extemporánea con su contraseña.</span>
                                </div>
                            `;
                        }

                        Swal.fire({
                            title: '¿Reactivar esta Devolución?',
                            html: `
                                ${avisoEspecial}
                                <p class="text-sm text-gray-600 mb-3">
                                    Al reactivar el acta, la devolución volverá a ser válida y el bien actualizará su estado en el inventario.
                                </p>
                                <div class="text-left mt-3">
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">
                                        Ingrese su contraseña de usuario para autorizar <span class="text-red-500">*</span>:
                                    </label>
                                    <input type="password" id="swal-password-reactivar-dev" class="w-full text-xs p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-navy" placeholder="Su contraseña actual de sesión...">
                                </div>
                            `,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, Reactivar Devolución',
                            cancelButtonText: 'Cancelar',
                            confirmButtonColor: '#002855',
                            cancelButtonColor: '#6B7280',
                            focusConfirm: false,
                            preConfirm: () => {
                                const password = document.getElementById('swal-password-reactivar-dev')?.value;
                                if (!password) {
                                    Swal.showValidationMessage('Debe ingresar su contraseña para autorizar la reactivación.');
                                    return false;
                                }
                                return password;
                            },
                            customClass: {
                                popup: 'rounded-xl text-left',
                                confirmButton: 'rounded-lg text-xs font-semibold px-4 py-2',
                                cancelButton: 'rounded-lg text-xs font-semibold px-4 py-2'
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
