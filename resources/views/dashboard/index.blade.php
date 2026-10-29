<x-app-layout>
    <div class="py-4 space-y-8">
        <!-- Encabezado con saludo y accesos rápidos -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-navy">Panel de Control</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Hola, <span class="font-medium text-gray-700">{{ auth()->user()->name }}</span> · Resumen operativo del inventario CCISur
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('asignaciones.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-gold text-brand-navy text-sm font-semibold shadow-sm hover:bg-brand-gold-400 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                    Nueva Asignación
                </a>
                <a href="{{ route('devoluciones.create') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-gray-200 text-brand-navy text-sm font-medium shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                    <i class="fas fa-undo text-xs text-brand-gold-600" aria-hidden="true"></i>
                    Devolución
                </a>
                <a href="{{ route('reportes.disponibles') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-white border border-gray-200 text-brand-navy text-sm font-medium shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                    <i class="fas fa-clipboard-check text-xs text-emerald-600" aria-hidden="true"></i>
                    Ver Reportes
                </a>
            </div>
        </div>

        <!-- Tarjetas de métricas (KPIs interactivos) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
            <!-- Total Mobiliario -->
            <a href="{{ route('mobiliario.index') }}"
                class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 border-l-4 border-l-brand-navy flex items-center gap-4 transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                <div class="w-14 h-14 rounded-full bg-brand-navy-50 text-brand-navy flex items-center justify-center shrink-0 group-hover:bg-brand-navy group-hover:text-white transition">
                    <i class="fas fa-couch text-2xl" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-brand-navy">Total Mobiliario</p>
                    <p class="text-3xl font-bold text-brand-navy">{{ $countMobiliario }}</p>
                </div>
            </a>

            <!-- Total Dispositivos -->
            <a href="{{ route('dispositivos.index') }}"
                class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 border-l-4 border-l-brand-navy-700 flex items-center gap-4 transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                <div class="w-14 h-14 rounded-full bg-brand-navy-50 text-brand-navy-700 flex items-center justify-center shrink-0 group-hover:bg-brand-navy-700 group-hover:text-white transition">
                    <i class="fas fa-laptop text-2xl" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-brand-navy-700">Total Dispositivos</p>
                    <p class="text-3xl font-bold text-brand-navy">{{ $countDispositivos }}</p>
                </div>
            </a>

            <!-- Asignaciones Activas (Dorado = Asignado) -->
            <a href="{{ route('asignaciones.index') }}"
                class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 border-l-4 border-l-brand-gold flex items-center gap-4 transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                <div class="w-14 h-14 rounded-full bg-brand-gold-50 text-brand-gold-600 flex items-center justify-center shrink-0 group-hover:bg-brand-gold group-hover:text-brand-navy transition">
                    <i class="fas fa-handshake text-2xl" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-brand-gold-700">Asignaciones Activas</p>
                    <p class="text-3xl font-bold text-brand-gold-700">{{ $totalAsignado }}</p>
                </div>
            </a>

            <!-- Bienes Disponibles (Verde = Disponible) -->
            <a href="{{ route('reportes.disponibles') }}"
                class="group bg-white rounded-xl p-6 shadow-sm border border-gray-100 border-l-4 border-l-emerald-500 flex items-center gap-4 transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-brand-gold">
                <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-600 group-hover:text-white transition">
                    <i class="fas fa-check-circle text-2xl" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-emerald-700">Bienes Disponibles</p>
                    <p class="text-3xl font-bold text-emerald-600">{{ $totalDisponible }}</p>
                </div>
            </a>
        </div>

        <!-- Últimas 5 Asignaciones -->
        <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    <i class="fas fa-clock text-brand-gold text-lg" aria-hidden="true"></i>
                    <h2 class="text-lg font-semibold text-brand-navy">Últimas 5 Asignaciones</h2>
                </div>
                <a href="{{ route('asignaciones.index') }}" class="text-xs font-semibold text-brand-navy hover:text-brand-gold-700 transition">
                    Ver todas →
                </a>
            </div>

            <!-- Vista compacta en tarjetas para pantallas muy pequeñas (< sm) -->
            <div class="sm:hidden space-y-3">
                @forelse ($ultimasAsignaciones as $asig)
                    <div class="p-3.5 rounded-lg border border-gray-100 bg-gray-50/60 space-y-1">
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span class="font-semibold text-brand-navy">#{{ $asig->id }} · <span class="capitalize">{{ $asig->tipo }}</span></span>
                            <span>{{ $asig->fecha_entrega->format('d/m/Y') }}</span>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">{{ $asig->nombre_bien }}</p>
                        <p class="text-xs text-gray-600">{{ $asig->empleado->nombre_completo ?? 'N/A' }}</p>
                    </div>
                @empty
                    <p class="py-6 text-center text-sm text-gray-400">No hay asignaciones registradas aún.</p>
                @endforelse
            </div>

            <!-- Tabla en pantallas sm y superiores -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm text-left border-collapse">
                    <caption class="sr-only">Listado de las últimas 5 asignaciones registradas</caption>
                    <thead class="bg-gray-50 text-gray-600 border-b-2 border-gray-200">
                        <tr>
                            <th scope="col" class="py-3 px-4 font-semibold">#</th>
                            <th scope="col" class="py-3 px-4 font-semibold">Colaborador</th>
                            <th scope="col" class="py-3 px-4 font-semibold">Tipo</th>
                            <th scope="col" class="py-3 px-4 font-semibold">Elemento</th>
                            <th scope="col" class="py-3 px-4 font-semibold">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-700">
                        @forelse ($ultimasAsignaciones as $asig)
                            <tr class="hover:bg-brand-gold-50/50 transition">
                                <td class="py-3 px-4 font-medium text-gray-900">{{ $asig->id }}</td>
                                <td class="py-3 px-4">{{ $asig->empleado->nombre_completo ?? 'N/A' }}</td>
                                <td class="py-3 px-4 capitalize">{{ $asig->tipo }}</td>
                                <td class="py-3 px-4">{{ $asig->nombre_bien }}</td>
                                <td class="py-3 px-4 whitespace-nowrap">{{ $asig->fecha_entrega->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 px-4 text-center text-gray-400">
                                    No hay asignaciones registradas aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 2 Gráficos Circulares: Mobiliario y Dispositivos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Gráfico 1: Disponibilidad de Mobiliario -->
            <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="mb-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-couch text-brand-gold text-lg" aria-hidden="true"></i>
                        <h2 class="text-lg font-semibold text-brand-navy">Disponibilidad de Mobiliario</h2>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Muebles aptos para asignación (excluye «No aplica»)
                    </p>
                </div>

                @if ($asignadoMobiliario + $disponibleMobiliario > 0)
                    <div class="relative h-56">
                        <canvas id="mobiliarioChart"
                            data-asignado="{{ $asignadoMobiliario }}"
                            data-disponible="{{ $disponibleMobiliario }}"
                            role="img"
                            aria-label="Disponibilidad de mobiliario — Asignados: {{ $asignadoMobiliario }} · Disponibles: {{ $disponibleMobiliario }}"></canvas>
                    </div>
                @else
                    <div class="h-56 flex items-center justify-center text-sm text-gray-400 text-center">
                        Sin mobiliario asignable registrado para graficar.
                    </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-3 text-center">
                    <div class="p-2.5 rounded-lg bg-brand-gold-50/70">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-gold-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-gold" aria-hidden="true"></span>
                            Asignados
                        </span>
                        <p class="text-lg font-bold text-brand-navy mt-0.5">{{ $asignadoMobiliario }}</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-emerald-50/70">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                            Disponibles
                        </span>
                        <p class="text-lg font-bold text-emerald-700 mt-0.5">{{ $disponibleMobiliario }}</p>
                    </div>
                </div>
            </section>

            <!-- Gráfico 2: Disponibilidad de Dispositivos -->
            <section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="mb-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-laptop text-brand-gold text-lg" aria-hidden="true"></i>
                        <h2 class="text-lg font-semibold text-brand-navy">Disponibilidad de Dispositivos</h2>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Equipos aptos para asignación (excluye «No aplica»)
                    </p>
                </div>

                @if ($asignadoDispositivos + $disponibleDispositivos > 0)
                    <div class="relative h-56">
                        <canvas id="dispositivosChart"
                            data-asignado="{{ $asignadoDispositivos }}"
                            data-disponible="{{ $disponibleDispositivos }}"
                            role="img"
                            aria-label="Disponibilidad de dispositivos — Asignados: {{ $asignadoDispositivos }} · Disponibles: {{ $disponibleDispositivos }}"></canvas>
                    </div>
                @else
                    <div class="h-56 flex items-center justify-center text-sm text-gray-400 text-center">
                        Sin dispositivos asignables registrados para graficar.
                    </div>
                @endif

                <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-3 text-center">
                    <div class="p-2.5 rounded-lg bg-brand-gold-50/70">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-gold-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-brand-gold" aria-hidden="true"></span>
                            Asignados
                        </span>
                        <p class="text-lg font-bold text-brand-navy mt-0.5">{{ $asignadoDispositivos }}</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-emerald-50/70">
                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                            Disponibles
                        </span>
                        <p class="text-lg font-bold text-emerald-700 mt-0.5">{{ $disponibleDispositivos }}</p>
                    </div>
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
                                            font: { size: 13 },
                                            padding: 16
                                        }
                                    },
                                    tooltip: {
                                        bodyFont: { size: 13 },
                                        titleFont: { size: 14 }
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
