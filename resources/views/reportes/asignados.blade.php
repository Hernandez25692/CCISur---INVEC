<x-app-layout>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reporte de Bienes Asignados</title>
        <style>
            :root {
                --primary-color: #3b82f6;
                --primary-dark: #2563eb;
                --secondary-color: #64748b;
                --success-color: #10b981;
                --warning-color: #f59e0b;
                --danger-color: #ef4444;
                --light-color: #f8fafc;
                --dark-color: #1e293b;
                --border-color: #e2e8f0;
                --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                --rounded-sm: 0.125rem;
                --rounded-md: 0.375rem;
                --rounded-lg: 0.5rem;
                --rounded-xl: 0.75rem;
            }
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                line-height: 1.5;
                color: var(--dark-color);
                background-color: #f1f5f9;
                -webkit-font-smoothing: antialiased;
            }
            
            /* Layout */
            .report-container {
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
                padding: 2rem 1.5rem;
            }
            
            /* Header */
            .report-header {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin-bottom: 2.5rem;
                text-align: center;
            }
            
            .company-logo {
                height: 60px;
                margin-bottom: 1rem;
            }
            
            .report-title {
                font-size: 1.875rem;
                font-weight: 700;
                color: var(--primary-dark);
                margin-bottom: 0.5rem;
                line-height: 1.2;
            }
            
            .report-subtitle {
                font-size: 1rem;
                color: var(--secondary-color);
                font-weight: 400;
                margin-bottom: 1.5rem;
            }
            
            .report-meta {
                display: flex;
                justify-content: space-between;
                width: 100%;
                max-width: 600px;
                margin-top: 1rem;
                font-size: 0.875rem;
                color: var(--secondary-color);
            }
            
            /* Card */
            .report-card {
                background-color: white;
                border-radius: var(--rounded-xl);
                box-shadow: var(--shadow-md);
                overflow: hidden;
                margin-bottom: 2rem;
            }
            
            .card-header {
                padding: 1.25rem 1.5rem;
                background-color: var(--primary-color);
                color: white;
                font-weight: 600;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .card-body {
                padding: 0;
            }
            
            /* Table */
            .data-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.875rem;
            }
            
            .data-table thead {
                background-color: #f1f5f9;
            }
            
            .data-table th {
                padding: 0.875rem 1rem;
                text-align: left;
                font-weight: 600;
                color: var(--dark-color);
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                border-bottom: 1px solid var(--border-color);
            }
            
            .data-table tbody tr {
                transition: background-color 0.15s ease;
            }
            
            .data-table tbody tr:hover {
                background-color: #f8fafc;
            }
            
            .data-table td {
                padding: 1rem;
                border-bottom: 1px solid var(--border-color);
                color: var(--dark-color);
                vertical-align: middle;
            }
            
            /* Status Badges */
            .badge {
                display: inline-block;
                padding: 0.25rem 0.5rem;
                border-radius: var(--rounded-sm);
                font-size: 0.75rem;
                font-weight: 500;
                text-transform: capitalize;
            }
            
            .badge-primary {
                background-color: #dbeafe;
                color: var(--primary-dark);
            }
            
            .badge-success {
                background-color: #dcfce7;
                color: #166534;
            }
            
            .badge-warning {
                background-color: #fef3c7;
                color: #92400e;
            }
            
            /* Empty State */
            .empty-state {
                padding: 3rem 1rem;
                text-align: center;
                color: var(--secondary-color);
            }
            
            .empty-icon {
                font-size: 2.5rem;
                margin-bottom: 1rem;
                color: #cbd5e1;
            }
            
            /* Footer */
            .report-footer {
                text-align: center;
                margin-top: 2rem;
                padding-top: 1.5rem;
                border-top: 1px solid var(--border-color);
                color: var(--secondary-color);
                font-size: 0.75rem;
            }
            
            /* Responsive */
            @media (max-width: 768px) {
                .report-container {
                    padding: 1.5rem 1rem;
                }
                
                .report-title {
                    font-size: 1.5rem;
                }
                
                .report-meta {
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .data-table {
                    display: block;
                    overflow-x: auto;
                }
            }
            
            /* Print Styles */
            @media print {
                body {
                    background-color: white;
                    font-size: 12pt;
                }
                
                .report-container {
                    padding: 0;
                    max-width: 100%;
                }
                
                .report-card {
                    box-shadow: none;
                    border: 1px solid #ddd;
                }
                
                .data-table {
                    font-size: 10pt;
                }
                
                .no-print {
                    display: none;
                }
            }
        </style>
    </head>
    <body>
        <div class="report-container">
            <header class="report-header">
                <div class="company-logo">
                    <!-- Reemplazar con logo de la empresa -->
                    <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="60" height="60" rx="12" fill="#3b82f6"/>
                        <path d="M30 40C35.5228 40 40 35.5228 40 30C40 24.4772 35.5228 20 30 20C24.4772 20 20 24.4772 20 30C20 35.5228 24.4772 40 30 40Z" fill="white"/>
                        <path d="M30 33.3333C31.8409 33.3333 33.3333 31.8409 33.3333 30C33.3333 28.159 31.8409 26.6666 30 26.6666C28.159 26.6666 26.6666 28.159 26.6666 30C26.6666 31.8409 28.159 33.3333 30 33.3333Z" fill="#3b82f6"/>
                    </svg>
                </div>
                <h1 class="report-title">Reporte de Bienes Asignados</h1>
                <p class="report-subtitle">Control de activos y recursos institucionales</p>
                <div class="report-meta">
                    <span>Generado: {{ now()->format('d/m/Y H:i') }}</span>
                    <span>Total registros: {{ $asignaciones->count() }}</span>
                </div>
            </header>
            
            <main>
                <div class="report-card">
                    <div class="card-header">
                        <span>Listado detallado de asignaciones</span>
                        <button onclick="window.print()" class="no-print" style="background: white; color: var(--primary-color); border: none; padding: 0.25rem 0.75rem; border-radius: var(--rounded-sm); font-weight: 500; cursor: pointer;">
                            Imprimir Reporte
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Empleado</th>
                                    <th>Tipo</th>
                                    <th>Elemento</th>
                                    <th>Área</th>
                                    <th>Fecha Asignación</th>
                                    <th>Estado</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($asignaciones as $a)
                                    @php
                                        $ref = $a->tipo === 'mobiliario'
                                            ? \App\Models\Mobiliario::find($a->id_referencia)
                                            : \App\Models\Dispositivo::find($a->id_referencia);
                                            
                                        $statusClass = $a->devolucion ? 'badge-success' : 'badge-primary';
                                        $statusText = $a->devolucion ? 'Devuelto' : 'Activo';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $a->empleado->nombre_completo ?? 'N/A' }}</strong>
                                            @if($a->empleado)
                                            <div class="text-muted" style="font-size: 0.75rem; margin-top: 0.25rem;">
                                                {{ $a->empleado->cargo ?? 'Sin cargo especificado' }}
                                            </div>
                                            @endif
                                        </td>
                                        <td class="capitalize">{{ $a->tipo }}</td>
                                        <td>
                                            {{ $ref->nombre ?? 'N/A' }}
                                            @if($ref && $ref->etiqueta)
                                            <div class="text-muted" style="font-size: 0.75rem; margin-top: 0.25rem;">
                                                Serial: {{ $ref->etiqueta }}
                                            </div>
                                            @endif
                                        </td>
                                        <td>{{ $a->area }}</td>
                                        <td>{{ date('d/m/Y', strtotime($a->fecha_entrega)) }}</td>
                                        <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                        <td>{{ $a->observaciones ?: '---' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if ($asignaciones->isEmpty())
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 7h-3a2 2 0 0 1-2-2V2"></path>
                                        <path d="M9 6a2 2 0 0 1-2 2H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-3"></path>
                                        <path d="M9 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </div>
                                <h3>No hay bienes asignados actualmente</h3>
                                <p>No se encontraron registros de asignaciones en el sistema</p>
                            </div>
                        @endif
                    </div>
                </div>
            </main>
            
            <footer class="report-footer">
                <p>Sistema de Gestión de Activos - Generado el {{ now()->format('d/m/Y') }}</p>
                <p class="text-muted" style="margin-top: 0.5rem;">© {{ date('Y') }} Todos los derechos reservados</p>
            </footer>
        </div>
    </body>
    </html>
</x-app-layout>