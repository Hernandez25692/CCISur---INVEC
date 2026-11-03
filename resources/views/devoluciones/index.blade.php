<x-app-layout>
    <div class="py-6 space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Encabezado principal --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-brand-navy tracking-tight">Actas de Devolución</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Control formal de recepciones, estado físico del bien y finiquito de responsabilidad</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('devoluciones.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand-navy hover:bg-brand-navy-800 text-white rounded-lg text-xs font-semibold shadow-xs focus:outline-none focus:ring-2 focus:ring-brand-gold transition">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                    Registrar Devolución
                </a>
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

        {{-- Tarjetas de Métricas ejecutivas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Devoluciones --}}
            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Devoluciones</p>
                        <p class="text-2xl font-bold text-brand-navy mt-1">{{ number_format($metricas['total']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-brand-navy/10 text-brand-navy flex items-center justify-center">
                        <i class="fas fa-file-invoice text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">Actas de entrega formal emitidas</p>
            </div>

            {{-- Devoluciones Este Mes --}}
            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Devoluciones del Mes</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($metricas['mes']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="mt-2 text-xs text-gray-500">Registradas durante el mes en curso</p>
            </div>

            {{-- Recibidos Operativos --}}
            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Recibidos Operativos</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($metricas['buen_estado']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-check-double text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-emerald-700 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Disponibles en inventario</span>
                </div>
            </div>

            {{-- Dañados o En Revisión --}}
            <div class="p-4 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Dañados / En Revisión</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($metricas['no_asignables']) }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                        <i class="fas fa-tools text-lg" aria-hidden="true"></i>
                    </div>
                </div>
                <p class="mt-2 text-xs text-red-600 font-medium">No aplican para asignación</p>
            </div>
        </div>

        @php
            $hayFiltros = request()->anyFilled(['buscar', 'tipo', 'fecha_inicio', 'fecha_fin', 'estado']);
        @endphp

        {{-- Filtros de búsqueda (colapsables en móvil, visibles siempre desde sm+) --}}
        <div x-data="{ mostrarFiltros: @js((bool) $hayFiltros) }"
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <button type="button" @click="mostrarFiltros = !mostrarFiltros"
                :aria-expanded="mostrarFiltros.toString()"
                class="sm:hidden w-full flex items-center justify-between px-4 py-3 text-xs font-semibold text-brand-navy bg-gray-50/70 hover:bg-gray-100/70 transition">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-sliders-h text-brand-gold text-xs" aria-hidden="true"></i>
                    Filtros de búsqueda
                    @if ($hayFiltros)
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-brand-gold text-brand-navy font-bold">Activos</span>
                    @endif
                </span>
                <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200"
                    :class="{ 'rotate-180': mostrarFiltros }" aria-hidden="true"></i>
            </button>

            <form method="GET" action="{{ route('devoluciones.index') }}"
                :class="{ 'block': mostrarFiltros, 'hidden': !mostrarFiltros }"
                class="{{ $hayFiltros ? 'block' : 'hidden' }} sm:block p-4 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                    {{-- Buscador general --}}
                    <div class="sm:col-span-2 lg:col-span-4">
                        <label for="buscar" class="block text-xs font-semibold text-gray-600 mb-1">Buscar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <i class="fas fa-search text-xs" aria-hidden="true"></i>
                            </div>
                            <input type="text" name="buscar" id="buscar"
                                value="{{ request('buscar') }}"
                                placeholder="Correlativo (DEV-0001, INV-DIS-0001), bien, colaborador..."
                                class="block w-full pl-9 pr-3 py-2 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs placeholder-gray-400">
                        </div>
                    </div>

                    {{-- Tipo de Bien --}}
                    <div class="lg:col-span-2">
                        <label for="tipo" class="block text-xs font-semibold text-gray-600 mb-1">Tipo de Bien</label>
                        <select name="tipo" id="tipo"
                            class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                            <option value="">Todos los tipos</option>
                            <option value="dispositivo" {{ request('tipo') === 'dispositivo' ? 'selected' : '' }}>Dispositivos</option>
                            <option value="mobiliario" {{ request('tipo') === 'mobiliario' ? 'selected' : '' }}>Mobiliario</option>
                        </select>
                    </div>

                    {{-- Estado al Devolver --}}
                    <div class="lg:col-span-2">
                        <label for="estado" class="block text-xs font-semibold text-gray-600 mb-1">Estado al Recibir</label>
                        <select name="estado" id="estado"
                            class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                            <option value="">Todos los estados</option>
                            @foreach (\App\Models\Devolucion::ESTADOS_DEVOLUCION as $est)
                                <option value="{{ $est }}" {{ request('estado') === $est ? 'selected' : '' }}>
                                    {{ \App\Models\Dispositivo::ESTADOS_CORTOS[$est] ?? $est }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fecha Inicio --}}
                    <div class="lg:col-span-2">
                        <label for="fecha_inicio" class="block text-xs font-semibold text-gray-600 mb-1">Desde</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}"
                            class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                    </div>

                    {{-- Fecha Fin --}}
                    <div class="lg:col-span-2">
                        <label for="fecha_fin" class="block text-xs font-semibold text-gray-600 mb-1">Hasta</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}"
                            class="block w-full py-2 px-3 text-xs rounded-lg border-gray-300 focus:border-brand-navy focus:ring-brand-navy shadow-xs">
                    </div>
                </div>

                {{-- Botones de acción claramente visibles --}}
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <a href="{{ route('devoluciones.index') }}"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-xs font-semibold hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold transition shadow-2xs"
                        title="Restablecer todos los filtros">
                        <i class="fas fa-undo text-[11px] text-gray-500" aria-hidden="true"></i>
                        <span>Limpiar Filtros</span>
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-brand-navy text-white text-xs font-semibold rounded-lg shadow-xs hover:bg-brand-navy-800 focus:outline-none focus:ring-2 focus:ring-brand-gold transition"
                        title="Aplicar filtros de búsqueda">
                        <i class="fas fa-filter text-[11px]" aria-hidden="true"></i>
                        <span>Filtrar</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabla de Actas de Devolución --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if ($devoluciones->total() > 0)
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/60 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-600">
                    <p>
                        Mostrando <span class="font-semibold text-brand-navy">{{ $devoluciones->firstItem() }}–{{ $devoluciones->lastItem() }}</span>
                        de <span class="font-semibold text-brand-navy">{{ $devoluciones->total() }}</span>
                        {{ $devoluciones->total() === 1 ? 'acta de devolución' : 'actas de devolución' }}
                    </p>
                    @if ($hayFiltros ?? false)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-brand-gold-50 text-brand-navy font-medium">
                            <i class="fas fa-filter text-[10px]" aria-hidden="true"></i>
                            Filtros aplicados
                        </span>
                    @endif
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider">
                        <tr>
                            <th scope="col" class="px-4 py-3.5 whitespace-nowrap">Correlativo</th>
                            <th scope="col" class="px-4 py-3.5">Custodio Saliente</th>
                            <th scope="col" class="px-4 py-3.5">Tipo</th>
                            <th scope="col" class="px-4 py-3.5">Bien Devuelto</th>
                            <th scope="col" class="px-4 py-3.5">Etiqueta</th>
                            <th scope="col" class="px-4 py-3.5">Estado al Recibir</th>
                            <th scope="col" class="px-4 py-3.5">Fecha</th>
                            <th scope="col" class="px-4 py-3.5 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($devoluciones as $dev)
                            @php
                                $asig = $dev->asignacion;
                                $item = $asig ? ($asig->tipo === \App\Models\Asignacion::TIPO_MOBILIARIO 
                                    ? ($asig->mobiliario ?? \App\Models\Mobiliario::withTrashed()->find($asig->id_referencia)) 
                                    : ($asig->dispositivo ?? \App\Models\Dispositivo::withTrashed()->find($asig->id_referencia))) : null;

                                $correlativo = 'DEV-INV-' . ($dev->fecha_devolucion ? $dev->fecha_devolucion->format('Y') : date('Y')) . '-' . str_pad((string) $dev->id, 4, '0', STR_PAD_LEFT);
                                $clasesEstado = \App\Models\Dispositivo::ESTADOS_CLASES[$dev->estado_devuelto] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                            @endphp
                            <tr class="hover:bg-brand-navy-50/30 transition">
                                {{-- Correlativo --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-mono font-bold text-brand-navy">{{ $correlativo }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">Acta #{{ $dev->id }}</div>
                                </td>

                                {{-- Custodio Saliente / Área --}}
                                <td class="px-4 py-3">
                                    @if ($asig && $asig->es_area_comun)
                                        <div class="flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-lg bg-amber-50 text-brand-gold-700 flex items-center justify-center shrink-0">
                                                <i class="fas fa-building text-xs" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <p class="font-bold text-gray-900">Área Común</p>
                                                <p class="text-[11px] text-gray-500">{{ $asig->area }}</p>
                                            </div>
                                        </div>
                                    @elseif ($asig && $asig->empleado)
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($asig->empleado->nombre_completo, 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-brand-navy">
                                                    {{ $asig->empleado->nombre_completo }}
                                                </p>
                                                <p class="text-[11px] text-gray-500">
                                                    <span class="font-mono">{{ $asig->empleado->codigo }}</span> &bull; {{ $asig->area }}
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Asignación no disponible</span>
                                    @endif
                                </td>

                                {{-- Tipo --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($asig && $asig->tipo === 'dispositivo')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                            <i class="fas fa-laptop text-[10px]" aria-hidden="true"></i>
                                            Dispositivo
                                        </span>
                                    @elseif ($asig && $asig->tipo === 'mobiliario')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                            <i class="fas fa-chair text-[10px]" aria-hidden="true"></i>
                                            Mobiliario
                                        </span>
                                    @else
                                        <span class="text-gray-400">---</span>
                                    @endif
                                </td>

                                {{-- Bien Devuelto --}}
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $item->nombre ?? 'N/A' }}</div>
                                    <div class="text-[11px] text-gray-500">
                                        @if ($asig && $asig->tipo === 'dispositivo' && !empty($item?->marca))
                                            <span>{{ $item->marca }} {{ $item->modelo ?? '' }}</span>
                                        @else
                                            <span>{{ $item->tipo ?? 'Bien CCISUR' }}</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Etiqueta / Placa --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="font-mono font-semibold text-brand-navy bg-gray-100 px-2 py-0.5 rounded border border-gray-200">
                                        {{ $item->etiqueta ?? 'Sin placa' }}
                                    </span>
                                </td>

                                {{-- Estado al Recibir --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-medium ring-1 ring-inset {{ $clasesEstado }}">
                                        {{ $dev->estado_devuelto ?? 'No especificado' }}
                                    </span>
                                </td>

                                {{-- Fecha Devolución --}}
                                <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                    {{ $dev->fecha_devolucion ? $dev->fecha_devolucion->format('d/m/Y') : '---' }}
                                </td>

                                {{-- Acciones --}}
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        {{-- Ver Acta --}}
                                        <a href="{{ route('devoluciones.show', $dev) }}"
                                            class="w-7 h-7 rounded inline-flex items-center justify-center text-gray-500 hover:text-brand-navy hover:bg-gray-100 transition"
                                            title="Ver acta de devolución">
                                            <i class="fas fa-eye text-xs" aria-hidden="true"></i>
                                        </a>

                                        {{-- Descargar PDF --}}
                                        <a href="{{ route('devoluciones.pdf', $dev) }}"
                                            target="_blank"
                                            class="w-7 h-7 rounded inline-flex items-center justify-center text-purple-600 hover:text-purple-900 hover:bg-purple-50 transition"
                                            title="Descargar Acta en PDF">
                                            <i class="fas fa-file-pdf text-xs" aria-hidden="true"></i>
                                        </a>

                                        {{-- Anular Devolución con confirmación protegida --}}
                                        <button type="button"
                                            class="w-7 h-7 rounded inline-flex items-center justify-center text-red-600 hover:text-red-900 hover:bg-red-50 transition btn-anular"
                                            title="Anular acta de devolución"
                                            data-id="{{ $dev->id }}"
                                            data-correlativo="{{ $correlativo }}"
                                            data-bien="{{ $item->nombre ?? 'Bien institucional' }}"
                                            data-etiqueta="{{ $item->etiqueta ?? 'S/E' }}">
                                            <i class="fas fa-trash-alt text-xs" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="mx-auto w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                                        <i class="fas fa-inbox text-xl" aria-hidden="true"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">No hay actas de devolución registradas</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $hayFiltros ? 'No se encontraron actas con los criterios de búsqueda aplicados.' : 'Cuando un colaborador entregue un bien asignado, regístrelo para concluir la asignación.' }}
                                    </p>
                                    @if ($hayFiltros)
                                        <div class="mt-3">
                                            <a href="{{ route('devoluciones.index') }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-xs font-semibold text-brand-navy hover:bg-gray-50 transition shadow-2xs">
                                                <i class="fas fa-undo text-[11px]" aria-hidden="true"></i> Limpiar filtros y ver todas
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($devoluciones->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $devoluciones->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Formulario oculto para anular devolución con contraseña --}}
    <form id="form-anular-devolucion" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
        <input type="hidden" name="password" id="input-password-anulacion">
    </form>

    {{-- Script seguro contra XSS utilizando data attributes y SweetAlert2 --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const botonesAnular = document.querySelectorAll('.btn-anular');
            const formAnular = document.getElementById('form-anular-devolucion');
            const inputPassword = document.getElementById('input-password-anulacion');

            botonesAnular.forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-id');
                    const correlativo = btn.getAttribute('data-correlativo') || '';
                    const bien = btn.getAttribute('data-bien') || 'Bien institucional';
                    const etiqueta = btn.getAttribute('data-etiqueta') || '';

                    Swal.fire({
                        title: '¿Anular Acta de Devolución?',
                        text: `Al anular el acta ${correlativo} (${bien} - ${etiqueta}), el bien volverá a estar asignado a su custodio anterior.`,
                        icon: 'warning',
                        input: 'password',
                        inputLabel: 'Ingrese su contraseña para autorizar:',
                        inputPlaceholder: 'Contraseña de usuario actual',
                        inputAttributes: {
                            autocapitalize: 'off',
                            autocorrect: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonColor: '#002855',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Sí, anular devolución',
                        cancelButtonText: 'Cancelar',
                        preConfirm: (password) => {
                            if (!password) {
                                Swal.showValidationMessage('Debe ingresar su contraseña.');
                            }
                            return password;
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            formAnular.action = `/devoluciones/${id}`;
                            inputPassword.value = result.value;
                            formAnular.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
