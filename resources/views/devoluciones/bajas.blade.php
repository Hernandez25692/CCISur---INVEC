<x-app-layout>
    <div class="py-4 space-y-6">
        {{-- Encabezado con navegación de regreso --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('devoluciones.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-navy hover:text-brand-gold-700 transition mb-2">
                    <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                    Volver a Actas de Devolución
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-navy">Actas de Devolución Anuladas (Bajas)</h1>
                <p class="mt-1 text-sm text-gray-500">Archivo histórico de recepciones y devoluciones anuladas por rectificación o error administrativo</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button"
                    onclick="const f = document.getElementById('filtroDevolucionesBajasForm'); if (f) { const oldAction = f.action; f.action = '{{ route('devoluciones.bajas.exportar') }}'; f.submit(); f.action = oldAction; } else { window.location.href = '{{ route('devoluciones.bajas.exportar', request()->query()) }}'; }"
                    class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-semibold shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition cursor-pointer"
                    title="Exportar archivo de devoluciones anuladas a Excel">
                    <i class="fas fa-file-excel text-sm" aria-hidden="true"></i>
                    <span>Exportar Excel</span>
                </button>
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-rose-50 text-rose-800 text-xs font-semibold border border-rose-200">
                    <i class="fas fa-archive text-rose-500" aria-hidden="true"></i>
                    <span>Total anuladas: <strong>{{ $devoluciones->total() }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Alertas de sesión --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-check-circle text-emerald-600 text-base" aria-hidden="true"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs sm:text-sm flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-exclamation-circle text-red-600 text-base" aria-hidden="true"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @php
            $hayFiltros = request()->anyFilled(['buscar', 'tipo', 'fecha_inicio', 'fecha_fin']);
        @endphp

        {{-- Filtros de búsqueda (colapsables en móvil) --}}
        <div x-data="{ mostrarFiltros: @js((bool) $hayFiltros) }"
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <button type="button" @click="mostrarFiltros = !mostrarFiltros"
                :aria-expanded="mostrarFiltros.toString()"
                class="sm:hidden w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-brand-navy bg-gray-50/70 hover:bg-gray-100/70 transition">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-sliders-h text-brand-gold text-xs" aria-hidden="true"></i>
                    Filtros de búsqueda
                    @if ($hayFiltros)
                        <span class="px-2 py-0.5 text-[11px] rounded-full bg-brand-gold text-brand-navy font-bold">Activos</span>
                    @endif
                </span>
                <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200"
                    :class="{ 'rotate-180': mostrarFiltros }" aria-hidden="true"></i>
            </button>

            <form id="filtroDevolucionesBajasForm" method="GET" action="{{ route('devoluciones.bajas') }}"
                :class="{ 'block': mostrarFiltros, 'hidden': !mostrarFiltros }"
                class="{{ $hayFiltros ? 'block' : 'hidden' }} sm:block p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="filtro_buscar" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Buscar
                        </label>
                        <input type="text" id="filtro_buscar" name="buscar" value="{{ request('buscar') }}"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="Correlativo, bien, custodio, motivo...">
                    </div>

                    <div>
                        <label for="filtro_tipo" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Tipo de Bien
                        </label>
                        <select id="filtro_tipo" name="tipo"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">Todos los tipos</option>
                            <option value="dispositivo" @selected(request('tipo') === 'dispositivo')>Dispositivos</option>
                            <option value="mobiliario" @selected(request('tipo') === 'mobiliario')>Mobiliario</option>
                        </select>
                    </div>

                    <div>
                        <label for="filtro_fecha_inicio" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Anulada Desde
                        </label>
                        <input type="date" id="filtro_fecha_inicio" name="fecha_inicio" value="{{ request('fecha_inicio') }}"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                    </div>

                    <div>
                        <label for="filtro_fecha_fin" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Anulada Hasta
                        </label>
                        <input type="date" id="filtro_fecha_fin" name="fecha_fin" value="{{ request('fecha_fin') }}"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-2.5 mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('devoluciones.bajas') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                        Limpiar
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-navy text-white text-sm font-semibold shadow-sm hover:bg-brand-navy-700 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                        <i class="fas fa-search text-xs" aria-hidden="true"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        {{-- Contenedor de Resultados --}}
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
            @if ($devoluciones->total() > 0)
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/60 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-600">
                    <p>
                        Mostrando <span class="font-semibold text-brand-navy">{{ $devoluciones->firstItem() }}–{{ $devoluciones->lastItem() }}</span>
                        de <span class="font-semibold text-brand-navy">{{ $devoluciones->total() }}</span>
                        {{ $devoluciones->total() === 1 ? 'acta anulada' : 'actas anuladas' }}
                    </p>
                    @if ($hayFiltros)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-brand-gold-50 text-brand-navy font-medium">
                            <i class="fas fa-filter text-[10px]" aria-hidden="true"></i>
                            Filtros aplicados
                        </span>
                    @endif
                </div>
            @endif

            @if ($devoluciones->isEmpty())
                <div class="px-6 py-12 text-center">
                    @if ($hayFiltros)
                        <i class="fas fa-search text-3xl text-gray-300 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No se encontraron actas de devolución anuladas con esos filtros</h3>
                        <p class="mt-1 text-xs text-gray-500">Probá ajustando los términos de búsqueda o limpiá los filtros.</p>
                        <div class="mt-4">
                            <a href="{{ route('devoluciones.bajas') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-brand-navy text-xs font-semibold shadow-sm hover:bg-gray-50 transition">
                                <i class="fas fa-undo" aria-hidden="true"></i>
                                Ver todo el archivo de bajas
                            </a>
                        </div>
                    @else
                        <i class="fas fa-archive text-3xl text-emerald-400 mb-2" aria-hidden="true"></i>
                        <h3 class="mt-1 text-sm font-semibold text-brand-navy">No hay actas de devolución anuladas</h3>
                        <p class="mt-1 text-xs text-gray-500">Todas las actas de devolución se encuentran activas y válidas en el inventario.</p>
                    @endif
                </div>
            @else
                {{-- Vista de Tabla para Escritorio y Tablet (md+) --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <caption class="sr-only">Listado de actas de devolución anuladas</caption>
                        <thead class="bg-gray-800 text-white">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider"># Acta</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Custodio Saliente / Área</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Bien Devuelto</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Fecha Anulación</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Anulado Por</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Motivo</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($devoluciones as $dev)
                                @php
                                    $asig = $dev->asignacion;
                                    $item = $asig ? ($asig->tipo === \App\Models\Asignacion::TIPO_MOBILIARIO 
                                        ? ($asig->mobiliario ?? \App\Models\Mobiliario::withTrashed()->find($asig->id_referencia)) 
                                        : ($asig->dispositivo ?? \App\Models\Dispositivo::withTrashed()->find($asig->id_referencia))) : null;

                                    $correlativo = 'DEV-INV-' . ($dev->fecha_devolucion ? $dev->fecha_devolucion->format('Y') : date('Y')) . '-' . str_pad((string) $dev->id, 4, '0', STR_PAD_LEFT);
                                @endphp
                                <tr class="even:bg-gray-50/50 hover:bg-rose-50/40 transition-colors">
                                    {{-- Correlativo --}}
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="font-mono font-bold text-brand-navy">{{ $correlativo }}</div>
                                        <div class="text-[11px] text-gray-400">Acta #{{ $dev->id }}</div>
                                    </td>

                                    {{-- Custodio Saliente / Área --}}
                                    <td class="px-4 py-3">
                                        @if ($asig && $asig->es_area_comun)
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 h-6 rounded bg-amber-50 text-brand-gold-700 flex items-center justify-center shrink-0">
                                                    <i class="fas fa-building text-[10px]"></i>
                                                </span>
                                                <div>
                                                    <p class="font-bold text-gray-900 text-xs">Área Común</p>
                                                    <p class="text-[11px] text-gray-500">{{ $asig->area }}</p>
                                                </div>
                                            </div>
                                        @elseif ($asig && $asig->empleado)
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-[10px] shrink-0">
                                                    {{ strtoupper(substr($asig->empleado->nombre_completo, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-brand-navy text-xs">{{ $asig->empleado->nombre_completo }}</p>
                                                    <p class="text-[11px] text-gray-500 font-mono">{{ $asig->empleado->codigo }} &bull; {{ $asig->area }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Asignación no disponible</span>
                                        @endif
                                    </td>

                                    {{-- Bien Devuelto --}}
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1.5">
                                            @if ($asig && $asig->tipo === 'dispositivo')
                                                <span class="text-blue-600 text-xs"><i class="fas fa-laptop"></i></span>
                                            @else
                                                <span class="text-amber-600 text-xs"><i class="fas fa-chair"></i></span>
                                            @endif
                                            <span class="font-medium text-gray-900 text-xs">{{ $item->nombre ?? 'N/A' }}</span>
                                        </div>
                                        <div class="text-[11px] text-gray-500 mt-0.5">
                                            <span class="font-mono bg-gray-100 px-1 rounded">{{ $item->etiqueta ?? 'Sin placa' }}</span>
                                            @if (!empty($item?->n_serie))
                                                &bull; <span class="font-mono">S/N: {{ $item->n_serie }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Fecha de Anulación --}}
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700">
                                        {{ $dev->deleted_at?->format('d/m/Y h:i A') ?? 'N/A' }}
                                    </td>

                                    {{-- Anulado Por --}}
                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-700">
                                        <span class="inline-flex items-center gap-1 font-medium">
                                            <i class="fas fa-user-shield text-gray-400 text-[10px]"></i>
                                            {{ $dev->anuladoPor?->name ?? 'Administración' }}
                                        </span>
                                    </td>

                                    {{-- Motivo --}}
                                    <td class="px-4 py-3 text-xs text-gray-600 max-w-xs">
                                        <p class="truncate" title="{{ $dev->motivo_anulacion }}">
                                            {{ $dev->motivo_anulacion ?? 'Sin motivo registrado' }}
                                        </p>
                                    </td>

                                    {{-- Acciones --}}
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <div class="inline-flex items-center justify-end gap-1.5">
                                            <a href="{{ route('devoluciones.show', $dev->id) }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-gray-100 text-brand-navy text-xs font-semibold hover:bg-gray-200 transition"
                                                title="Ver detalle del acta y auditoría">
                                                <i class="fas fa-eye text-xs"></i>
                                                <span>Ver</span>
                                            </a>

                                            @can('admin')
                                                <button type="button"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-600 hover:text-white transition btn-reactivar-devolucion"
                                                    title="Reactivar acta de devolución"
                                                    data-id="{{ $dev->id }}"
                                                    data-correlativo="{{ $correlativo }}"
                                                    data-bien="{{ $item->nombre ?? 'Bien institucional' }}"
                                                    data-etiqueta="{{ $item->etiqueta ?? 'S/E' }}">
                                                    <i class="fas fa-undo text-xs"></i>
                                                    <span>Reactivar</span>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Vista de Tarjetas para Móvil (< md) --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach ($devoluciones as $dev)
                        @php
                            $asig = $dev->asignacion;
                            $item = $asig ? ($asig->tipo === \App\Models\Asignacion::TIPO_MOBILIARIO 
                                ? ($asig->mobiliario ?? \App\Models\Mobiliario::withTrashed()->find($asig->id_referencia)) 
                                : ($asig->dispositivo ?? \App\Models\Dispositivo::withTrashed()->find($asig->id_referencia))) : null;

                            $correlativo = 'DEV-INV-' . ($dev->fecha_devolucion ? $dev->fecha_devolucion->format('Y') : date('Y')) . '-' . str_pad((string) $dev->id, 4, '0', STR_PAD_LEFT);
                        @endphp
                        <div class="p-4 space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="inline-block px-2 py-0.5 bg-gray-100 text-brand-navy text-xs font-mono font-bold rounded mb-1">
                                        {{ $correlativo }}
                                    </span>
                                    <h3 class="text-sm font-bold text-gray-900">{{ $item->nombre ?? 'Bien' }}</h3>
                                    <p class="text-xs text-gray-500 font-mono">{{ $item->etiqueta ?? 'Sin placa' }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                    Anulada
                                </span>
                            </div>

                            <div class="pt-2 border-t border-gray-50 text-xs text-gray-600 space-y-1">
                                <p><span class="font-semibold text-brand-navy">Custodio Saliente:</span> {{ $asig ? ($asig->es_area_comun ? 'Área Común - '.$asig->area : ($asig->empleado?->nombre_completo ?? 'N/A')) : 'N/A' }}</p>
                                <p><span class="font-semibold text-brand-navy">Fecha anulación:</span> {{ $dev->deleted_at?->format('d/m/Y h:i A') ?? 'N/A' }}</p>
                                <p><span class="font-semibold text-brand-navy">Anulado por:</span> {{ $dev->anuladoPor?->name ?? 'Administración' }}</p>
                                @if ($dev->motivo_anulacion)
                                    <p class="bg-rose-50 text-rose-900 p-2 rounded text-[11px] mt-1">
                                        <strong>Motivo:</strong> {{ $dev->motivo_anulacion }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center justify-end gap-2 pt-2">
                                <a href="{{ route('devoluciones.show', $dev->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 text-brand-navy text-xs font-semibold hover:bg-gray-200 transition">
                                    <i class="fas fa-eye text-[11px]"></i> Ver
                                </a>

                                @can('admin')
                                    <button type="button"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold hover:bg-emerald-600 hover:text-white transition btn-reactivar-devolucion"
                                        data-id="{{ $dev->id }}"
                                        data-correlativo="{{ $correlativo }}"
                                        data-bien="{{ $item->nombre ?? 'Bien institucional' }}"
                                        data-etiqueta="{{ $item->etiqueta ?? 'S/E' }}">
                                        <i class="fas fa-undo text-[11px]"></i> Reactivar
                                    </button>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($devoluciones->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $devoluciones->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Formulario oculto para reactivar devolución con contraseña --}}
    <form id="form-reactivar-devolucion" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="password" id="input-password-reactivar">
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            function escapeHtml(str) {
                const div = document.createElement('div');
                div.textContent = str || '';
                return div.innerHTML;
            }

            const botonesReactivar = document.querySelectorAll('.btn-reactivar-devolucion');
            const formReactivar = document.getElementById('form-reactivar-devolucion');
            const inputPassword = document.getElementById('input-password-reactivar');

            botonesReactivar.forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    const correlativo = escapeHtml(btn.getAttribute('data-correlativo') || '');
                    const bien = escapeHtml(btn.getAttribute('data-bien') || 'Bien institucional');
                    const etiqueta = escapeHtml(btn.getAttribute('data-etiqueta') || '');

                    Swal.fire({
                        title: '¿Reactivar Acta de Devolución?',
                        html: `
                            <p class="text-sm text-gray-600 mb-3">
                                Al reactivar el acta <strong>${correlativo}</strong> (${bien} - ${etiqueta}), el bien volverá al estado de devolución formal en el inventario.
                            </p>
                            <div class="text-left mt-3">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">
                                    Ingrese su contraseña de usuario para autorizar <span class="text-red-500">*</span>:
                                </label>
                                <input type="password" id="swal-password-reactivar" class="w-full text-xs p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-gold focus:border-brand-navy" placeholder="Su contraseña actual de sesión...">
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#002855',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Sí, reactivar devolución',
                        cancelButtonText: 'Cancelar',
                        focusConfirm: false,
                        preConfirm: () => {
                            const password = document.getElementById('swal-password-reactivar')?.value;
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
                            formReactivar.action = `/devoluciones/${id}/restore`;
                            inputPassword.value = result.value;
                            formReactivar.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
