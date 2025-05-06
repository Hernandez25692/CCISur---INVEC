<x-app-layout>
    <div class="py-6 px-4 max-w-7xl mx-auto space-y-6">
        <div class="flex items-center justify-center bg-blue-100 p-4 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-blue-800">
            <i class="fas fa-clock mr-2"></i>
            <span id="fechaHora"></span>
            </h2>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
            function actualizarFechaHora() {
                const ahora = new Date();
                const opciones = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
                document.getElementById('fechaHora').textContent = ahora.toLocaleDateString('es-ES', opciones);
            }
            actualizarFechaHora();
            setInterval(actualizarFechaHora, 1000);
            });
        </script>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Panel de Controll - INVEC</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <style>
                /* Reset y estilos base */
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
                }

                body {
                    background-color: #f3f4f6;
                    color: #1f2937;
                    line-height: 1.6;
                }

                .container {
                    max-width: 1400px;
                    margin: 0 auto;
                    padding: 24px 16px;
                }

                /* Header */
                .dashboard-header {
                    margin-bottom: 32px;
                }

                .dashboard-title {
                    font-size: 28px;
                    font-weight: 700;
                    color: #1e40af;
                    margin-bottom: 8px;
                }

                /* Cards de métricas */
                .metrics-grid {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 20px;
                    margin-bottom: 32px;
                }

                @media (min-width: 768px) {
                    .metrics-grid {
                        grid-template-columns: repeat(3, 1fr);
                    }
                }

                .metric-card {
                    background-color: white;
                    border-radius: 10px;
                    padding: 24px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                    border-left: 4px solid;
                }

                .metric-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
                }

                .metric-card.blue {
                    border-left-color: #3b82f6;
                }

                .metric-card.green {
                    border-left-color: #10b981;
                }

                .metric-card.amber {
                    border-left-color: #f59e0b;
                }

                .metric-label {
                    font-size: 14px;
                    color: #6b7280;
                    margin-bottom: 8px;
                    font-weight: 500;
                }

                .metric-value {
                    font-size: 32px;
                    font-weight: 700;
                }

                .metric-card.blue .metric-value {
                    color: #1d4ed8;
                }

                .metric-card.green .metric-value {
                    color: #059669;
                }

                .metric-card.amber .metric-value {
                    color: #b45309;
                }

                /* Secciones */
                .dashboard-section {
                    background-color: white;
                    border-radius: 10px;
                    padding: 24px;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                    margin-bottom: 32px;
                }

                .section-header {
                    display: flex;
                    align-items: center;
                    margin-bottom: 20px;
                }

                .section-title {
                    font-size: 20px;
                    font-weight: 600;
                    color: #374151;
                    margin-left: 12px;
                }

                /* Tabla */
                .data-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 14px;
                }

                .data-table thead {
                    background-color: #f9fafb;
                    color: #4b5563;
                    text-align: left;
                }

                .data-table th {
                    padding: 12px 16px;
                    font-weight: 600;
                    border-bottom: 2px solid #e5e7eb;
                }

                .data-table td {
                    padding: 12px 16px;
                    border-bottom: 1px solid #e5e7eb;
                }

                .data-table tr:last-child td {
                    border-bottom: none;
                }

                .data-table tr:hover {
                    background-color: #f9fafb;
                }

                .capitalize {
                    text-transform: capitalize;
                }

                /* Gráfico */
                .chart-container {
                    position: relative;
                    height: 300px;
                    margin-top: 20px;
                }

                /* Responsive */
                @media (max-width: 768px) {
                    .container {
                        padding: 16px 12px;
                    }

                    .dashboard-title {
                        font-size: 24px;
                    }

                    .metric-card {
                        padding: 20px;
                    }

                    .metric-value {
                        font-size: 28px;
                    }

                    .data-table th,
                    .data-table td {
                        padding: 10px 12px;
                    }
                }

                /* Animaciones */
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .dashboard-section {
                    animation: fadeIn 0.4s ease-out;
                }

                .metrics-grid {
                    animation: fadeIn 0.6s ease-out;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <div class="dashboard-header">
                    <h1 class="dashboard-title">Panel de Control</h1>
                </div>

                <!-- Métricas rápidas -->
                <div class="metrics-grid">
                    <div class="metric-card blue">
                        <div class="metric-label">Total Mobiliario</div>
                        <div class="metric-value">{{ \App\Models\Mobiliario::count() }}</div>
                    </div>

                    <div class="metric-card green">
                        <div class="metric-label">Total Dispositivos</div>
                        <div class="metric-value">{{ \App\Models\Dispositivo::count() }}</div>
                    </div>

                    <div class="metric-card amber">
                        <div class="metric-label">Total Asignaciones</div>
                        <div class="metric-value">{{ \App\Models\Asignacion::count() }}</div>
                    </div>
                </div>

                <!-- Últimas asignaciones -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <i class="fas fa-clock"></i>
                        <h2 class="section-title">Últimas 5 Asignaciones</h2>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Colaborador</th>
                                <th>Tipo</th>
                                <th>Elemento</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\Asignacion::latest()->take(5)->get() as $asig)
                                @php
                                    $ref =
                                        $asig->tipo === 'mobiliario'
                                            ? \App\Models\Mobiliario::find($asig->id_referencia)
                                            : \App\Models\Dispositivo::find($asig->id_referencia);
                                @endphp
                                <tr>
                                    <td>{{ $asig->id }}</td>
                                    <td>{{ $asig->colaborador }}</td>
                                    <td class="capitalize">{{ $asig->tipo }}</td>
                                    <td>{{ $ref->nombre ?? 'N/A' }}</td>
                                    <td>{{ $asig->fecha_entrega }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Gráfico de asignaciones -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <i class="fas fa-chart-pie"></i>
                        <h2 class="section-title">Estado General de Equipos</h2>
                    </div>

                    <div class="chart-container">
                        <canvas id="estadoEquiposChart"></canvas>
                    </div>
                </div>
            </div>

            <script>
                @php
                    $totalMobiliario = \App\Models\Mobiliario::count();
                    $asignadoMobiliario = \App\Models\Asignacion::where('tipo', 'mobiliario')->count();
                    $totalDispositivos = \App\Models\Dispositivo::count();
                    $asignadoDispositivos = \App\Models\Asignacion::where('tipo', 'dispositivo')->count();

                    $totalAsignado = $asignadoMobiliario + $asignadoDispositivos;
                    $totalDisponible = $totalMobiliario - $asignadoMobiliario + ($totalDispositivos - $asignadoDispositivos);
                @endphp

                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('estadoEquiposChart').getContext('2d');
                    const estadoEquiposChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Asignado', 'Disponible'],
                            datasets: [{
                                label: 'Equipos',
                                data: [{{ $totalAsignado }}, {{ $totalDisponible }}],
                                backgroundColor: ['#f59e0b', '#10b981'],
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
                                        font: {
                                            size: 14
                                        },
                                        padding: 20
                                    }
                                },
                                tooltip: {
                                    bodyFont: {
                                        size: 14
                                    },
                                    titleFont: {
                                        size: 16
                                    }
                                }
                            },
                            cutout: '70%',
                            borderRadius: 10,
                            spacing: 5
                        }
                    });
                });
            </script>
        </body>

        </html>
</x-app-layout>
