<x-app-layout>
    <div class="py-10 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Editar Dispositivo</h2>

        <form action="{{ route('dispositivos.update', $dispositivo->id) }}" method="POST"
            class="bg-white p-6 rounded shadow space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $dispositivo->nombre) }}"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="etiqueta" class="block text-sm font-medium text-gray-700">Etiqueta de Inventario</label>
                <input type="text" id="etiqueta" name="etiqueta" value="{{ $dispositivo->etiqueta }}"
                    class="w-full mt-1 border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-600 cursor-not-allowed"
                    readonly>
            </div>

            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                <input type="text" name="tipo" id="tipo" value="{{ old('tipo', $dispositivo->tipo) }}"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="marca" class="block text-sm font-medium text-gray-700">Marca</label>
                <input type="text" name="marca" id="marca" value="{{ old('marca', $dispositivo->marca) }}"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="modelo" class="block text-sm font-medium text-gray-700">Modelo</label>
                <input type="text" name="modelo" id="modelo" value="{{ old('modelo', $dispositivo->modelo) }}"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="n_serie" class="block text-sm font-medium text-gray-700">Número de Serie</label>
                <input type="text" name="n_serie" id="n_serie" value="{{ old('n_serie', $dispositivo->n_serie) }}"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion"
                    value="{{ old('ubicacion', $dispositivo->ubicacion) }}" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="estado" id="estado" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccionar --</option>
                    <option value="bueno" {{ $dispositivo->estado == 'bueno' ? 'selected' : '' }}>Bueno</option>
                    <option value="regular" {{ $dispositivo->estado == 'regular' ? 'selected' : '' }}>Regular</option>
                    <option value="dañado" {{ $dispositivo->estado == 'dañado' ? 'selected' : '' }}>Dañado</option>
                </select>
            </div>

            <div>
                <label for="fecha_registro" class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                <input type="date" name="fecha_registro" id="fecha_registro"
                    value="{{ old('fecha_registro', $dispositivo->fecha_registro) }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('dispositivos.index') }}" class="text-sm text-gray-500 hover:underline">←
                    Cancelar</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Actualizar</button>
            </div>
        </form>
    </div>
</x-app-layout>
