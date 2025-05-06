<x-app-layout>
    <div class="py-10 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Editar Mueble</h2>

        <form action="{{ route('mobiliario.update', $mobiliario->id) }}" method="POST"
            class="bg-white p-6 rounded shadow space-y-4">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Mueble</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $mobiliario->nombre) }}"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Tipo -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                <input type="text" name="tipo" id="tipo" value="{{ old('tipo', $mobiliario->tipo) }}"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Ubicación -->
            <div>
                <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion"
                    value="{{ old('ubicacion', $mobiliario->ubicacion) }}" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Estado -->
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="estado" id="estado" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccionar --</option>
                    <option value="bueno" {{ $mobiliario->estado == 'bueno' ? 'selected' : '' }}>Bueno</option>
                    <option value="regular" {{ $mobiliario->estado == 'regular' ? 'selected' : '' }}>Regular</option>
                    <option value="dañado" {{ $mobiliario->estado == 'dañado' ? 'selected' : '' }}>Dañado</option>
                </select>
            </div>

            <!-- Fecha -->
            <div>
                <label for="fecha_registro" class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                <input type="date" name="fecha_registro" id="fecha_registro"
                    value="{{ old('fecha_registro', $mobiliario->fecha_registro) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Botones -->
            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('mobiliario.index') }}" class="text-sm text-gray-500 hover:underline">← Cancelar</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Actualizar</button>
            </div>
        </form>
    </div>
</x-app-layout>
