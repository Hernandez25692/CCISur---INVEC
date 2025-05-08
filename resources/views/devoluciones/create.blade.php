<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Registrar Devolución</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-10 px-4">
        <div class="bg-white p-6 rounded shadow">
            <form method="POST" action="{{ route('devoluciones.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="asignacion_id" class="block text-sm font-medium text-gray-700">Asignación</label>
                    <select name="asignacion_id" id="asignacion_id" required class="w-full rounded border-gray-300 shadow-sm mt-1">
                        <option value="">Seleccione una asignación</option>
                        @foreach ($asignaciones as $asignacion)
                            <option value="{{ $asignacion->id }}">
                                {{ $asignacion->colaborador }} - {{ ucfirst($asignacion->tipo) }} 
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="fecha_devolucion" class="block text-sm font-medium text-gray-700">Fecha de Devolución</label>
                    <input type="date" name="fecha_devolucion" required class="w-full rounded border-gray-300 shadow-sm mt-1">
                </div>

                <div>
                    <label for="recibido_por" class="block text-sm font-medium text-gray-700">Recibido por</label>
                    <input type="text" name="recibido_por" required class="w-full rounded border-gray-300 shadow-sm mt-1">
                </div>

                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                    <textarea name="observaciones" rows="3" class="w-full rounded border-gray-300 shadow-sm mt-1"></textarea>
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">
                    Guardar Devolución
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
