<x-app-layout>
    <div class="max-w-2xl mx-auto py-10">
        <h2 class="text-2xl font-bold mb-6 text-blue-700">Registrar Empleado</h2>

        <form method="POST" action="{{ route('empleados.store') }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf

            <div>
                <label class="block">Identidad</label>
                <input type="text" name="identidad" class="w-full border-gray-300 rounded mt-1" required>
            </div>

            <div>
                <label class="block">Nombre Completo</label>
                <input type="text" name="nombre_completo" class="w-full border-gray-300 rounded mt-1" required>
            </div>

            <div>
                <label class="block">Gerencia</label>
                <select name="gerencia" class="w-full border-gray-300 rounded mt-1" required>
                    <option value="">Seleccione...</option>
                    <option>Dirección Ejecutiva</option>
                    <option>Gerencia Administrativa y Financiera</option>
                    <option>Gerencia de Operaciones Registrales</option>
                    <option>Gerencia de Servicios Empresariales y Afiliaciones</option>
                </select>
            </div>

            <div>
                <label class="block">Ubicación</label>
                <select name="ubicacion" class="w-full border-gray-300 rounded mt-1" required>
                    <option value="">Seleccione...</option>
                    <option>Choluteca</option>
                    <option>Valle</option>
                </select>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar</button>
        </form>
    </div>
</x-app-layout>
