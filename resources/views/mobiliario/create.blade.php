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
                <select name="tipo" id="tipo" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">-- Seleccionar tipo --</option>
                    <option value="Escritorio">Escritorio</option>
                    <option value="Silla">Silla</option>
                    <option value="Archivador">Archivador</option>
                    <option value="Mesa">Mesa</option>
                    <option value="Estantería">Estantería</option>
                    <option value="Gabinete">Gabinete</option>
                    <option value="Banco">Banco</option>
                    <option value="Otro">Otro</option>
                </select>
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
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">-- Seleccionar --</option>
                    <option value="Nuevo / En perfectas condiciones">Nuevo / En perfectas condiciones</option>
                    <option value="Con pequeños detalles / Imperfecciones leves">Con pequeños detalles / Imperfecciones
                        leves</option>
                    <option value="Usado / Segunda mano">Usado / Segunda mano</option>
                    <option value="Dañado / Defectuoso">Dañado / Defectuoso</option>
                    <option value="En reparación / En revisión">En reparación / En revisión</option>
                    <option value="Producto incompleto">Producto incompleto</option>
                    <option value="Caducado / No apto para uso">Caducado / No apto para uso</option>
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
