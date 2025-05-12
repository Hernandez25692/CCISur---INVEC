<x-app-layout>
    <div class="max-w-2xl mx-auto py-10">
        <h2 class="text-2xl font-bold mb-6 text-blue-700">Editar Empleado</h2>

        <form method="POST" action="{{ route('empleados.update', $empleado->id) }}" class="bg-white p-6 rounded shadow space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block">Identidad</label>
                <input type="text" name="identidad" value="{{ old('identidad', $empleado->identidad) }}" class="w-full border-gray-300 rounded mt-1" required>
            </div>

            <div>
                <label class="block">Nombre Completo</label>
                <input type="text" name="nombre_completo" value="{{ old('nombre_completo', $empleado->nombre_completo) }}" class="w-full border-gray-300 rounded mt-1" required>
            </div>

            <div>
                <label class="block">Gerencia</label>
                <select name="gerencia" class="w-full border-gray-300 rounded mt-1" required>
                    <option value="">Seleccione...</option>
                    <option {{ $empleado->gerencia == 'Dirección Ejecutiva' ? 'selected' : '' }}>Dirección Ejecutiva</option>
                    <option {{ $empleado->gerencia == 'Gerencia Administrativa y Financiera' ? 'selected' : '' }}>Gerencia Administrativa y Financiera</option>
                    <option {{ $empleado->gerencia == 'Gerencia de Operaciones Registrales' ? 'selected' : '' }}>Gerencia de Operaciones Registrales</option>
                    <option {{ $empleado->gerencia == 'Gerencia de Servicios Empresariales y Afiliaciones' ? 'selected' : '' }}>Gerencia de Servicios Empresariales y Afiliaciones</option>
                </select>
            </div>

            <div>
                <label class="block">Ubicación</label>
                <select name="ubicacion" class="w-full border-gray-300 rounded mt-1" required>
                    <option value="">Seleccione...</option>
                    <option {{ $empleado->ubicacion == 'Choluteca' ? 'selected' : '' }}>Choluteca</option>
                    <option {{ $empleado->ubicacion == 'Valle' ? 'selected' : '' }}>Valle</option>
                </select>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Actualizar</button>
        </form>
    </div>
</x-app-layout>
