<x-app-layout>
    <div class="py-4 space-y-6 sm:space-y-8">
        <!-- Encabezado con saludo y accesos rápidos -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-navy">Panel de Control</h1>
                <p class="mt-1 text-xs sm:text-sm text-gray-500">
                    Hola, <span class="font-semibold text-brand-navy">{{ auth()->user()->name }}</span> &bull; Resumen general del inventario CCISUR
                </p>
            </div>

            @can('admin')
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 sm:gap-2.5">
                    <a href="{{ route('asignaciones.create') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-brand-gold text-brand-navy text-xs sm:text-sm font-semibold shadow-sm hover:bg-brand-gold-400 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                        <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                        <span>Nueva Asignación</span>
                    </a>
                    <a href="{{ route('devoluciones.create') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-lg bg-white border border-gray-200 text-brand-navy text-xs sm:text-sm font-medium shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                        <i class="fas fa-undo text-xs text-brand-gold-600" aria-hidden="true"></i>
                        <span>Devolución</span>
                    </a>
                </div>
            @endcan
        </div>

        <!-- Tarjetas de métricas (KPIs interactivos) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6">
            <!-- Total Mobiliario -->
            <a href="{{ route('mobiliario.index') }}"
                class="group bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-100 border-l-4 border-l-brand-navy flex items-center justify-between sm:justify-start gap-3.5 sm:gap-4 transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-brand-navy-50 text-brand-navy flex items-center justify-center shrink-0 group-hover:bg-brand-navy group-hover:text-white transition">
                    <i class="fas fa-couch text-xl sm:text-2xl" aria-hidden="true"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wide truncate">Total Mobiliario</p>
                    <p class="text-2xl sm:text-3xl font-bold text-brand-navy mt-0.5">{{ number_format($countMobiliario) }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5 hidden sm:block truncate">Muebles y estaciones de trabajo</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs sm:hidden shrink-0" aria-hidden="true"></i>
            </a>

            <!-- Total Dispositivos -->
            <a href="{{ route('dispositivos.index') }}"
                class="group bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-100 border-l-4 border-l-blue-600 flex items-center justify-between sm:justify-start gap-3.5 sm:gap-4 transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:bg-blue-600 group-hover:text-white transition">
                    <i class="fas fa-laptop text-xl sm:text-2xl" aria-hidden="true"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wide truncate">Total Dispositivos</p>
                    <p class="text-2xl sm:text-3xl font-bold text-brand-navy mt-0.5">{{ number_format($countDispositivos) }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5 hidden sm:block truncate">Equipos informáticos y de red</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs sm:hidden shrink-0" aria-hidden="true"></i>
            </a>

            <!-- Asignaciones Activas (Dorado = Asignado) -->
            <a href="{{ route('asignaciones.index') }}"
                class="group bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-100 border-l-4 border-l-brand-gold flex items-center justify-between sm:justify-start gap-3.5 sm:gap-4 transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-brand-gold-50 text-brand-gold-700 flex items-center justify-center shrink-0 group-hover:bg-brand-gold group-hover:text-brand-navy transition">
                    <i class="fas fa-handshake text-xl sm:text-2xl" aria-hidden="true"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm font-semibold text-brand-gold-700 uppercase tracking-wide truncate">Asignaciones Activas</p>
                    <p class="text-2xl sm:text-3xl font-bold text-brand-gold-700 mt-0.5">{{ number_format($totalAsignado) }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5 hidden sm:block truncate">Bienes en uso bajo custodia</p>
                </div>
                <i class="fas fa-chevron-right text-gray-300 text-xs sm:hidden shrink-0" aria-hidden="true"></i>
            </a>
        </div>

        <!-- Últimas Asignaciones Activas -->
        <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-4 sm:mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-brand-gold-50 text-brand-gold flex items-center justify-center shrink-0">
                        <i class="fas fa-clock text-base" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-brand-navy">Últimas Asignaciones Activas</h2>
                        <p class="text-xs text-gray-500">Bienes institucionales bajo custodia vigente</p>
                    </div>
                </div>
                <a href="{{ route('asignaciones.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-navy hover:text-brand-gold transition">
                    <span>Ver todas</span>
                    <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i>
                </a>
            </div>

            <!-- Vista en tarjetas para pantallas móviles (< md) -->
            <div class="md:hidden divide-y divide-gray-100">
                @forelse ($ultimasAsignaciones as $asig)
                    @php
                        $referencia = $asig->tipo === 'mobiliario' ? $asig->mobiliario : $asig->dispositivo;
                    @endphp
                    <div class="py-3.5 space-y-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                    <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-brand-navy-50 text-brand-navy border border-brand-navy/10 shrink-0">
                                        #{{ $asig->id }}
                                    </span>
                                    @if ($referencia?->etiqueta)
                                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-gray-100 text-brand-navy border border-gray-200 shrink-0">
                                            {{ $referencia->etiqueta }}
                                        </span>
                                    @endif
                                    @if ($asig->tipo === 'dispositivo')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 shrink-0">
                                            <i class="fas fa-laptop text-[9px]" aria-hidden="true"></i> Dispositivo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 shrink-0">
                                            <i class="fas fa-chair text-[9px]" aria-hidden="true"></i> Mobiliario
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 break-words mt-1">
                                    {{ $asig->nombre_bien }}
                                </h3>
                            </div>
                            <span class="text-[11px] text-gray-400 font-medium shrink-0">
                                {{ $asig->fecha_entrega->format('d/m/Y') }}
                            </span>
                        </div>

                        <div class="bg-gray-50/80 rounded-lg p-2.5 text-xs flex items-center justify-between border border-gray-100">
                            <div class="min-w-0 flex-1 flex items-center gap-2">
                                @if ($asig->es_area_comun)
                                    <span class="w-6 h-6 rounded bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                        <i class="fas fa-building text-[10px]"></i>
                                    </span>
                                    <span class="font-semibold text-amber-900 truncate">Área Común: {{ $asig->area }}</span>
                                @elseif ($asig->empleado)
                                    <div class="w-6 h-6 rounded bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-[10px] shrink-0">
                                        {{ strtoupper(substr($asig->empleado->nombre_completo, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-brand-navy truncate">{{ $asig->empleado->nombre_completo }}</p>
                                        <p class="text-[10px] text-gray-400 font-mono">{{ $asig->empleado->codigo }} &bull; {{ $asig->area }}</p>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">No asignado</span>
                                @endif
                            </div>

                            <a href="{{ route('asignaciones.show', $asig->id) }}"
                                class="ml-2 inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-white border border-gray-200 text-brand-navy text-xs font-semibold hover:bg-gray-50 shadow-2xs transition shrink-0"
                                title="Ver acta de entrega">
                                <i class="fas fa-eye text-[10px]" aria-hidden="true"></i>
                                <span>Ver</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <div class="mx-auto w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-2">
                            <i class="fas fa-inbox text-lg" aria-hidden="true"></i>
                        </div>
                        <p class="text-xs font-semibold text-gray-700">No hay asignaciones activas</p>
                        <p class="text-[11px] text-gray-400 mt-1">Los nuevos registros de custodia se mostrarán aquí.</p>
                    </div>
                @endforelse
            </div>

            <!-- Tabla en pantallas medianas y superiores (>= md) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <caption class="sr-only">Listado de las últimas asignaciones activas</caption>
                    <thead class="bg-gray-50/80 text-gray-600 text-xs uppercase font-semibold border-b border-gray-100">
                        <tr>
                            <th scope="col" class="py-3 px-4">#</th>
                            <th scope="col" class="py-3 px-4">Custodio / Destino</th>
                            <th scope="col" class="py-3 px-4">Tipo</th>
                            <th scope="col" class="py-3 px-4">Elemento</th>
                            <th scope="col" class="py-3 px-4">Fecha</th>
                            <th scope="col" class="py-3 px-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($ultimasAsignaciones as $asig)
                            @php
                                $referencia = $asig->tipo === 'mobiliario' ? $asig->mobiliario : $asig->dispositivo;
                            @endphp
                            <tr class="hover:bg-brand-navy-50/30 transition">
                                <td class="py-3 px-4 font-mono font-bold text-brand-navy text-xs">#{{ $asig->id }}</td>
                                <td class="py-3 px-4">
                                    @if ($asig->es_area_comun)
                                        <div class="flex items-center gap-2">
                                            <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                                                <i class="fas fa-building text-xs" aria-hidden="true"></i>
                                            </span>
                                            <div>
                                                <p class="font-bold text-gray-900 text-xs">Área Común</p>
                                                <p class="text-[11px] text-gray-500">{{ $asig->area }}</p>
                                            </div>
                                        </div>
                                    @elseif ($asig->empleado)
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-lg bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($asig->empleado->nombre_completo, 0, 1)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('empleados.historial', $asig->empleado_id) }}" class="font-semibold text-brand-navy hover:underline text-xs">
                                                    {{ $asig->empleado->nombre_completo }}
                                                </a>
                                                <p class="text-[11px] text-gray-500 font-mono">{{ $asig->empleado->codigo }} &bull; {{ $asig->area }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic text-xs">No asignado</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if ($asig->tipo === 'dispositivo')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20">
                                            <i class="fas fa-laptop text-[10px]" aria-hidden="true"></i>
                                            Dispositivo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                            <i class="fas fa-chair text-[10px]" aria-hidden="true"></i>
                                            Mobiliario
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-xs">
                                    <div class="font-semibold text-gray-900">{{ $asig->nombre_bien }}</div>
                                    @if ($referencia?->etiqueta)
                                        <span class="font-mono text-[10px] text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200">{{ $referencia->etiqueta }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-gray-600 text-xs">
                                    {{ $asig->fecha_entrega->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('asignaciones.show', $asig->id) }}"
                                            class="w-7 h-7 rounded inline-flex items-center justify-center text-gray-500 hover:text-brand-navy hover:bg-gray-100 transition"
                                            title="Ver acta de entrega">
                                            <i class="fas fa-eye text-xs" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{ route('asignaciones.pdf', $asig->id) }}" target="_blank"
                                            class="w-7 h-7 rounded inline-flex items-center justify-center text-purple-600 hover:text-purple-900 hover:bg-purple-50 transition"
                                            title="Descargar PDF">
                                            <i class="fas fa-file-pdf text-xs" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 px-4 text-center text-gray-400 text-xs">
                                    No hay asignaciones activas en este momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 2 Gráficos Circulares: Mobiliario y Dispositivos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
            <!-- Gráfico 1: Disponibilidad de Mobiliario -->
            <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                            <i class="fas fa-couch text-sm" aria-hidden="true"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-brand-navy">Disponibilidad de Mobiliario</h2>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Muebles aptos para asignación (excluye dados de baja o no asignables)
                    </p>
                </div>

                @if ($asignadoMobiliario + $disponibleMobiliario > 0)
                    <div class="relative h-48 sm:h-56">
                        <canvas id="mobiliarioChart"
                            data-asignado="{{ $asignadoMobiliario }}"
                            data-disponible="{{ $disponibleMobiliario }}"
                            role="img"
                            aria-label="Disponibilidad de mobiliario — Asignados: {{ $asignadoMobiliario }} · Disponibles: {{ $disponibleMobiliario }}"></canvas>
                    </div>
                @else
                    <div class="h-48 sm:h-56 flex items-center justify-center text-sm text-gray-400 text-center">
                        Sin mobiliario asignable registrado para graficar.
                    </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-2.5 sm:gap-3 text-center">
                    <a href="{{ route('asignaciones.index', ['tipo' => 'mobiliario']) }}"
                        class="p-2 sm:p-2.5 rounded-lg bg-brand-gold-50/70 hover:bg-brand-gold-100 transition focus:outline-none focus:ring-2 focus:ring-brand-gold group block"
                        title="Ver asignaciones activas de mobiliario">
                        <span class="inline-flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold text-brand-gold-800">
                            <span class="w-2 h-2 rounded-full bg-brand-gold" aria-hidden="true"></span>
                            Asignados
                        </span>
                        <p class="text-base sm:text-lg font-bold text-brand-navy mt-0.5">{{ number_format($asignadoMobiliario) }}</p>
                    </a>
                    <a href="{{ route('mobiliario.index', ['disponibilidad' => 'Sin Asignar']) }}"
                        class="p-2 sm:p-2.5 rounded-lg bg-emerald-50/70 hover:bg-emerald-100 transition focus:outline-none focus:ring-2 focus:ring-emerald-500 group block"
                        title="Ver mobiliario disponible para asignar">
                        <span class="inline-flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold text-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
                            Disponibles
                        </span>
                        <p class="text-base sm:text-lg font-bold text-emerald-700 mt-0.5">{{ number_format($disponibleMobiliario) }}</p>
                    </a>
                </div>
            </section>

            <!-- Gráfico 2: Disponibilidad de Dispositivos -->
            <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                <div class="mb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-laptop text-sm" aria-hidden="true"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-brand-navy">Disponibilidad de Dispositivos</h2>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Equipos aptos para asignación (excluye en taller o dados de baja)
                    </p>
                </div>

                @if ($asignadoDispositivos + $disponibleDispositivos > 0)
                    <div class="relative h-48 sm:h-56">
                        <canvas id="dispositivosChart"
                            data-asignado="{{ $asignadoDispositivos }}"
                            data-disponible="{{ $disponibleDispositivos }}"
                            role="img"
                            aria-label="Disponibilidad de dispositivos — Asignados: {{ $asignadoDispositivos }} · Disponibles: {{ $disponibleDispositivos }}"></canvas>
                    </div>
                @else
                    <div class="h-48 sm:h-56 flex items-center justify-center text-sm text-gray-400 text-center">
                        Sin dispositivos asignables registrados para graficar.
                    </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-2.5 sm:gap-3 text-center">
                    <a href="{{ route('asignaciones.index', ['tipo' => 'dispositivo']) }}"
                        class="p-2 sm:p-2.5 rounded-lg bg-brand-gold-50/70 hover:bg-brand-gold-100 transition focus:outline-none focus:ring-2 focus:ring-brand-gold group block"
                        title="Ver asignaciones activas de dispositivos">
                        <span class="inline-flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold text-brand-gold-800">
                            <span class="w-2 h-2 rounded-full bg-brand-gold" aria-hidden="true"></span>
                            Asignados
                        </span>
                        <p class="text-base sm:text-lg font-bold text-brand-navy mt-0.5">{{ number_format($asignadoDispositivos) }}</p>
                    </a>
                    <a href="{{ route('dispositivos.index', ['disponibilidad' => 'Sin Asignar']) }}"
                        class="p-2 sm:p-2.5 rounded-lg bg-emerald-50/70 hover:bg-emerald-100 transition focus:outline-none focus:ring-2 focus:ring-emerald-500 group block"
                        title="Ver dispositivos disponibles para asignar">
                        <span class="inline-flex items-center gap-1.5 text-[11px] sm:text-xs font-semibold text-emerald-800">
                            <span class="w-2 h-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
                            Disponibles
                        </span>
                        <p class="text-base sm:text-lg font-bold text-emerald-700 mt-0.5">{{ number_format($disponibleDispositivos) }}</p>
                    </a>
                </div>
            </section>
        </div>
    </div>

    @if (($asignadoMobiliario + $disponibleMobiliario) > 0 || ($asignadoDispositivos + $disponibleDispositivos) > 0)
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"
                integrity="sha384-9nhczxUqK87bcKHh20fSQcTGD4qq5GhayNYSYWqwBkINBhOfQLg/P5HG5lF1urn4"
                crossorigin="anonymous"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof window.Chart === 'undefined') return;

                    const rootStyles = getComputedStyle(document.documentElement);
                    const colorGold = (rootStyles.getPropertyValue('--ccisur-gold') || '#AF9730').trim();
                    const colorEmerald = '#10b981';

                    function renderDonut(canvasId) {
                        const canvas = document.getElementById(canvasId);
                        if (!canvas) return;

                        const asignado = parseInt(canvas.dataset.asignado || '0', 10);
                        const disponible = parseInt(canvas.dataset.disponible || '0', 10);

                        new Chart(canvas.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: ['Asignado', 'Disponible'],
                                datasets: [{
                                    data: [asignado, disponible],
                                    backgroundColor: [colorGold, colorEmerald],
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            font: { size: 12 },
                                            padding: 12
                                        }
                                    },
                                    tooltip: {
                                        bodyFont: { size: 12 },
                                        titleFont: { size: 13 }
                                    }
                                },
                                cutout: '70%',
                                borderRadius: 8,
                                spacing: 4
                            }
                        });
                    }

                    renderDonut('mobiliarioChart');
                    renderDonut('dispositivosChart');
                });
            </script>
        @endpush
    @endif
</x-app-layout>