<x-app-layout>
    <div class="py-10 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Registrar Nuevo Mueble</h2>

        <form action="{{ route('mobiliario.store') }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Mueble</label>
                <input type="text" name="nombre" id="nombre" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Tipo -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                <input type="text" name="tipo" id="tipo" required
                    placeholder="Ej: Escritorio, silla, archivador..."
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Ubicación -->
            <div>
                <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Estado -->
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="estado" id="estado" required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Seleccionar --</option>
                    <option value="bueno">Bueno</option>
                    <option value="regular">Regular</option>
                    <option value="dañado">Dañado</option>
                </select>
            </div>

            <!-- Fecha -->
            <div>
                <label for="fecha_registro" class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                <input type="date" name="fecha_registro" id="fecha_registro" value="{{ date('Y-m-d') }}"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Botones -->
            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('mobiliario.index') }}" class="text-sm text-gray-500 hover:underline">← Volver</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Guardar</button>
            </div>
        </form>
    </div>
</x-app-layout>
