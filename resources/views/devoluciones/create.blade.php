<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">➕ Nueva Devolución</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-10 px-4">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('devoluciones.store') }}" class="space-y-5">
                @csrf

                <!-- Select2 de asignaciones -->
                <div>
                    <label for="asignacion_id" class="block text-sm font-medium text-gray-700">Buscar por colaborador o
                        etiqueta</label>
                    <select name="asignacion_id" id="asignacion_id"
                        class="select2 w-full rounded border-gray-300 shadow-sm mt-1" required>
                        <option value="">Seleccione una asignación</option>
                        @foreach ($asignaciones as $asignacion)
                            @php
                                $item =
                                    $asignacion->tipo === 'mobiliario'
                                        ? $asignacion->mobiliario
                                        : $asignacion->dispositivo;
                                $etiqueta = $item->etiqueta ?? 'Sin etiqueta';
                                $nombre = $item->nombre ?? 'Sin nombre';
                            @endphp
                            <option value="{{ $asignacion->id }}">
                                {{ $asignacion->colaborador }} - {{ ucfirst($asignacion->tipo) }} - {{ $nombre }}
                                - {{ $etiqueta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="fecha_devolucion" class="block text-sm font-medium text-gray-700">Fecha de
                        Devolución</label>
                    <input type="date" name="fecha_devolucion" value="{{ date('Y-m-d') }}" required
                        class="w-full rounded border-gray-300 shadow-sm mt-1">
                </div>

                <div>
                    <label for="recibido_por" class="block text-sm font-medium text-gray-700">Recibido por</label>
                    <input type="text" name="recibido_por" value="{{ Auth::user()->name }}" required
                        class="w-full rounded border-gray-300 shadow-sm mt-1">
                </div>

                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                    <textarea name="observaciones" rows="3" class="w-full rounded border-gray-300 shadow-sm mt-1"></textarea>
                </div>

                <div class="text-right">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                        Guardar Devolución
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CDN de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Activar Select2 -->
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Buscar asignación...",
                allowClear: true,
                width: 'resolve'
            });
        });
    </script>
</x-app-layout>
