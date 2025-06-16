<x-app-layout>
    <div class="py-10 max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Registrar Nuevo Dispositivo</h2>

        <form action="{{ route('dispositivos.store') }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del dispositivo</label>
                <input type="text" name="nombre" id="nombre" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <!-- Tipo -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                <select name="tipo" id="tipo" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">-- Seleccionar tipo de dispositivo --</option>
                    <option value="Laptop">Laptop</option>
                    <option value="Computadora de escritorio">Computadora de escritorio</option>
                    <option value="Proyector">Proyector</option>
                    <option value="Impresora">Impresora</option>
                    <option value="Escáner">Escáner</option>
                    <option value="Monitor">Monitor</option>
                    <option value="Teclado">Teclado</option>
                    <option value="Mouse">Mouse</option>
                    <option value="Tablet">Tablet</option>
                    <option value="Switch de red">Switch de red</option>
                    <option value="Router">Router</option>
                    <option value="Access Point (AP)">Access Point (AP)</option>
                    <option value="Servidor">Servidor</option>
                    <option value="Disco duro externo">Disco duro externo</option>
                    <option value="No-break / UPS">No-break / UPS</option>
                    <option value="Cámara Web">Cámara Web</option>
                    <option value="Micrófono">Micrófono</option>
                    <option value="Sistema de sonido">Sistema de sonido</option>
                    <option value="Pantalla interactiva">Pantalla interactiva</option>
                    <option value="Control remoto">Control remoto</option>
                    <option value="Cargador de laptop">Cargador de laptop</option>
                    <option value="Cable HDMI">Cable HDMI</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>


            <div>
                <label for="marca" class="block text-sm font-medium text-gray-700">Marca</label>
                <input type="text" name="marca" id="marca" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <div>
                <label for="modelo" class="block text-sm font-medium text-gray-700">Modelo</label>
                <input type="text" name="modelo" id="modelo" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <div>
                <label for="n_serie" class="block text-sm font-medium text-gray-700">Número de Serie</label>
                <input type="text" name="n_serie" id="n_serie" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <div>
                <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

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

            <div>
                <label for="fecha_registro" class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                <input type="date" name="fecha_registro" id="fecha_registro" value="{{ date('Y-m-d') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('dispositivos.index') }}" class="text-sm text-gray-500 hover:underline">← Volver</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Guardar</button>
            </div>
        </form>
    </div>
</x-app-layout>
