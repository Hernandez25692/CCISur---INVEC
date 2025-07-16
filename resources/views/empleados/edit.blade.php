<x-app-layout>
    <div class="max-w-2xl mx-auto py-10">
        <div class="bg-gradient-to-r from-blue-100 via-blue-200 to-blue-300 rounded-xl shadow-lg p-8">
            <h2 class="text-3xl font-extrabold mb-8 text-blue-800 text-center tracking-tight">Editar Empleado</h2>

            <form method="POST" action="{{ route('empleados.update', $empleado->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-blue-700 mb-2">Identidad</label>
                    <input type="text" name="identidad" value="{{ old('identidad', $empleado->identidad) }}"
                        class="w-full px-4 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-blue-700 mb-2">Nombre Completo</label>
                    <input type="text" name="nombre_completo" value="{{ old('nombre_completo', $empleado->nombre_completo) }}"
                        class="w-full px-4 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-blue-700 mb-2">Gerencia</label>
                    <select name="gerencia"
                        class="w-full px-4 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
                        required>
                        <option value="">Seleccione...</option>
                        <option {{ $empleado->gerencia == 'Dirección Ejecutiva' ? 'selected' : '' }}>Dirección Ejecutiva</option>
                        <option {{ $empleado->gerencia == 'Gerencia Administrativa y Financiera' ? 'selected' : '' }}>Gerencia Administrativa y Financiera</option>
                        <option {{ $empleado->gerencia == 'Gerencia de Operaciones Registrales' ? 'selected' : '' }}>Gerencia de Operaciones Registrales</option>
                        <option {{ $empleado->gerencia == 'Gerencia de Servicios Empresariales y Afiliaciones' ? 'selected' : '' }}>Gerencia de Servicios Empresariales y Afiliaciones</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-blue-700 mb-2">Ubicación</label>
                    <select name="ubicacion"
                        class="w-full px-4 py-2 border border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
                        required>
                        <option value="">Seleccione...</option>
                        <option {{ $empleado->ubicacion == 'Choluteca' ? 'selected' : '' }}>Choluteca</option>
                        <option {{ $empleado->ubicacion == 'Valle' ? 'selected' : '' }}>Valle</option>
                    </select>
                </div>

                <div class="flex justify-between items-center pt-4">
                    <a href="{{ route('empleados.index') }}"
                        class="text-blue-600 hover:underline font-medium transition">Cancelar</a>
                    <button
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition font-semibold">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
