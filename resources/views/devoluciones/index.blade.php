<x-app-layout>
    <style>
        /* Estilos generales */
        .devoluciones-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
            font-family: 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
            color: #333;
        }

        .devoluciones-header {
            margin-bottom: 1.5rem;
        }

        .devoluciones-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        /* Barra de herramientas */
        .toolbar {
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 768px) {
            .toolbar {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 0.5rem;
        }

        /* Botones */
        .btn-primary {
            display: inline-flex;
            align-items: center;
            background-color: #2563eb;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Tabla */
        .card-table {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background-color: #f8fafc;
        }

        .data-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .data-table td {
            padding: 1rem;
            font-size: 0.875rem;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background-color: #f8fafc;
        }

        /* Acciones */
        .action-links {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .action-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
            white-space: nowrap;
        }

        .action-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .action-link.pdf {
            color: #10b981;
        }

        .action-link.pdf:hover {
            color: #059669;
        }

        /* Estado vacío */
        .empty-state {
            padding: 2rem;
            text-align: center;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .empty-state p {
            color: #64748b;
            font-size: 0.875rem;
        }
    </style>

    <x-slot name="header">
        <h2 class="devoluciones-title">Devoluciones Registradas</h2>
    </x-slot>

    <div class="devoluciones-container">
        <div class="toolbar">
            <h3 class="section-title">Listado de Actas de Devolución</h3>
            <a href="{{ route('devoluciones.create') }}" class="btn-primary">
                ➕ Nueva Devolución
            </a>
        </div>

        <!-- Formulario de búsqueda -->
        <form method="GET" action="{{ route('devoluciones.index') }}" class="mb-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <input type="text" name="buscar" placeholder="Buscar por colaborador o etiqueta"
                    value="{{ request('buscar') }}"
                    class="w-full sm:w-72 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300">
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Buscar</button>
                <a href="{{ route('devoluciones.index') }}" class="text-sm text-gray-500 hover:underline">Limpiar</a>
            </div>
        </form>

        @if ($devoluciones->isEmpty())
            <div class="empty-state">
                <p>No hay devoluciones registradas.</p>
            </div>
        @else
            <div class="card-table">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Colaborador</th>
                                <th>Tipo</th>
                                <th>Elemento</th>
                                <th>Etiqueta</th>
                                <th>Fecha de Devolución</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($devoluciones as $devolucion)
                                @php
                                    $asig = $devolucion->asignacion;
                                    $item = null;

                                    if ($asig) {
                                        $item =
                                            $asig->tipo === 'mobiliario'
                                                ? \App\Models\Mobiliario::find($asig->id_referencia)
                                                : \App\Models\Dispositivo::find($asig->id_referencia);
                                    }
                                @endphp

                                @if ($asig)
                                    <tr>
                                        <td>{{ $asig->empleado->nombre_completo ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($asig->tipo) }}</td>
                                        <td>{{ $item->nombre ?? 'N/A' }}</td>
                                        <td>{{ $item->etiqueta ?? 'N/A' }}</td>
                                        <td>{{ $devolucion->fecha_devolucion }}</td>
                                        <td>
                                            <div class="action-links">
                                                <a href="{{ route('devoluciones.show', $devolucion->id) }}"
                                                    class="action-link">Ver</a>
                                                <a href="{{ route('devoluciones.pdf', $devolucion->id) }}"
                                                    target="_blank" class="action-link pdf">PDF</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="mt-6">
                {{ $devoluciones->withQueryString()->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
