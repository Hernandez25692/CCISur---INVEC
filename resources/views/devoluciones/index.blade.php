<x-app-layout>
    <style>
        /* Estilos generales mejorados */
        .devoluciones-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', sans-serif;
            color: #1f2937;
        }

        .devoluciones-header {
            margin-bottom: 2rem;
        }

        .devoluciones-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        /* Barra de herramientas mejorada */
        .toolbar {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        @media (min-width: 768px) {
            .toolbar {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 0;
        }

        /* Botones mejorados */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            border: none;
            cursor: pointer;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-success {
            background-color: #10b981;
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        /* Tarjeta y tabla mejoradas */
        .card-table {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .data-table thead {
            background-color: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }

        .data-table th {
            padding: 1rem 1.25rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .data-table td {
            padding: 1.25rem;
            font-size: 0.875rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tr:hover {
            background-color: #f8fafc;
        }

        /* Estados mejorados */
        .status {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* Acciones mejoradas */
        .action-links {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .action-link {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            white-space: nowrap;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .action-link:hover {
            background-color: #eff6ff;
            text-decoration: none;
        }

        .action-link.pdf {
            color: #10b981;
        }

        .action-link.pdf:hover {
            background-color: #ecfdf5;
        }

        .action-link.danger {
            color: #ef4444;
        }

        .action-link.danger:hover {
            background-color: #fef2f2;
        }

        /* Estado vacío mejorado */
        .empty-state {
            padding: 3rem 2rem;
            text-align: center;
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px dashed #e5e7eb;
        }

        .empty-state-icon {
            margin-bottom: 1rem;
            color: #9ca3af;
        }

        .empty-state p {
            color: #6b7280;
            font-size: 0.9375rem;
            margin-bottom: 1.5rem;
        }

        /* Formulario de búsqueda mejorado */
        .search-form {
            margin-bottom: 2rem;
        }

        .search-input-container {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
        }

        @media (min-width: 640px) {
            .search-input-container {
                flex-direction: row;
                align-items: center;
            }
        }

        .search-input {
            flex-grow: 1;
            padding: 0.625rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        /* Modal de confirmación */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 50;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .modal-overlay.active {
            opacity: 1;
            pointer-events: all;
        }

        .modal-content {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            padding: 1.5rem;
            transform: translateY(10px);
            transition: transform 0.2s;
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #111827;
        }

        .modal-body {
            margin-bottom: 1.5rem;
            color: #4b5563;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* Paginación mejorada */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination .page-item {
            margin: 0 0.25rem;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #3b82f6;
            text-decoration: none;
            transition: all 0.2s;
        }

        .pagination .page-link:hover {
            background-color: #eff6ff;
        }

        .pagination .active .page-link {
            background-color: #3b82f6;
            color: white;
        }

        .pagination .disabled .page-link {
            color: #9ca3af;
            pointer-events: none;
        }

        /* Badge para etiquetas */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-radius: 0.25rem;
            background-color: #e0e7ff;
            color: #4338ca;
        }
    </style>

    <x-slot name="header">
        <h2 class="devoluciones-title">Devoluciones Registradas</h2>
    </x-slot>

    <div class="devoluciones-container">
        <div class="toolbar">
            <h3 class="section-title">Listado de Actas de Devolución</h3>
            <a href="{{ route('devoluciones.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Nueva Devolución
            </a>
        </div>

        <!-- Formulario de búsqueda mejorado -->
        <form method="GET" action="{{ route('devoluciones.index') }}" class="search-form">
            <div class="search-input-container">
                <input type="text" name="buscar" placeholder="Buscar por empleado o etiqueta"
                    value="{{ request('buscar') }}"
                    class="search-input">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    Buscar
                </button>
                @if(request('buscar'))
                    <a href="{{ route('devoluciones.index') }}" class="btn">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Limpiar
                    </a>
                @endif
            </div>
        </form>

        @if ($devoluciones->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p>No se encontraron devoluciones registradas.</p>
                <a href="{{ route('devoluciones.create') }}" class="btn btn-primary inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Registrar primera devolución
                </a>
            </div>
        @else
            <div class="card-table">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Empleado</th>
                                <th>Tipo</th>
                                <th>Elemento</th>
                                <th>Etiqueta</th>
                                <th>Fecha</th>
                                
                                <th class="text-right">Acciones</th>
                                <th>Evidencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($devoluciones as $devolucion)
                                @php
                                    $asig = $devolucion->asignacion;
                                    $item = null;
                                    $statusClass = 'status-pending'; // Default
                                    
                                    if ($asig) {
                                        $item =
                                            $asig->tipo === 'mobiliario'
                                                ? \App\Models\Mobiliario::find($asig->id_referencia)
                                                : \App\Models\Dispositivo::find($asig->id_referencia);
                                        
                                        
                                    }
                                @endphp

                                @if ($asig)
                                    <tr>
                                        <td class="font-medium">{{ $asig->empleado->nombre_completo ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($asig->tipo) }}</td>
                                        <td>{{ $item->nombre ?? 'N/A' }}</td>
                                        <td>
                                            @if($item && $item->etiqueta)
                                                <span class="badge">{{ $item->etiqueta }}</span>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($devolucion->fecha_devolucion)->format('d/m/Y') }}</td>
                                        
                                        <td>
                                            <div class="action-links">
                                                <a href="{{ route('devoluciones.show', $devolucion->id) }}"
                                                    class="action-link">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                               |     Ver
                                                </a>
                                                <a href="{{ route('devoluciones.pdf', $devolucion->id) }}"
                                                    target="_blank" class="action-link pdf">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                                    </svg>
                                                    PDF
                                                </a>
                                                @if(!$devolucion->completada && !$devolucion->cancelada)
                                                    <button onclick="confirmDelete({{ $devolucion->id }})" 
                                                            class="action-link danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Eliminar
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if ($devolucion->adjunto)
                                                <a href="{{ asset('storage/' . $devolucion->adjunto) }}"
                                                    target="_blank"
                                                    class="action-link text-green-600 hover:underline flex items-center gap-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Ver evidencia
                                                </a>
                                            @else
                                                <!-- Formulario inline para subir -->
                                                <form action="{{ route('devoluciones.adjuntar', $devolucion->id) }}"
                                                    method="POST" enctype="multipart/form-data"
                                                    class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="file" name="adjunto" accept=".pdf,.jpg,.png,.jpeg"
                                                        required class="text-sm text-gray-600 text-sm">
                                                    <button type="submit"
                                                        class="text-blue-600 hover:underline text-sm flex items-center gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                        </svg>
                                                        Subir
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación mejorada -->
            <div class="pagination">
                {{ $devoluciones->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div id="confirmModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="modal-title">Confirmar eliminación</h3>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar esta devolución? Esta acción no se puede deshacer.
            </div>
            <div class="modal-actions">
                <button onclick="closeModal()" class="btn">
                    Cancelar
                </button>
                <form id="deleteForm" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
                <button onclick="submitDelete()" class="btn btn-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Eliminar
                </button>
            </div>
        </div>
    </div>

    <script>
        // Funciones para el modal de confirmación
        function confirmDelete(id) {
            const modal = document.getElementById('confirmModal');
            const form = document.getElementById('deleteForm');
            
            form.action = `/devoluciones/${id}`;
            modal.classList.add('active');
        }

        function closeModal() {
            const modal = document.getElementById('confirmModal');
            modal.classList.remove('active');
        }

        function submitDelete() {
            document.getElementById('deleteForm').submit();
        }

        // Cerrar modal al hacer clic fuera del contenido
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</x-app-layout>