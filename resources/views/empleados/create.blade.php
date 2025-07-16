<x-app-layout>
    <div class="max-w-2xl mx-auto py-10">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-extrabold mb-8 text-white text-center drop-shadow-lg">Registrar Empleado</h2>

            <form method="POST" action="{{ route('empleados.store') }}" class="bg-white p-8 rounded-lg shadow-xl space-y-6">
                @csrf

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Identidad</label>
                    <input type="text" name="identidad" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nombre Completo</label>
                    <input type="text" name="nombre_completo" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Gerencia</label>
                    <select name="gerencia" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                        <option value="">Seleccione...</option>
                        <option>Dirección Ejecutiva</option>
                        <option>Gerencia Administrativa y Financiera</option>
                        <option>Gerencia de Operaciones Registrales</option>
                        <option>Gerencia de Servicios Empresariales y Afiliaciones</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Ubicación</label>
                    <select name="ubicacion" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                        <option value="">Seleccione...</option>
                        <option>Choluteca</option>
                        <option>Valle</option>
                    </select>
                </div>

                <div class="flex justify-center">
                    <button class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold px-8 py-3 rounded-lg shadow hover:from-blue-700 hover:to-indigo-700 transition transform hover:scale-105">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
