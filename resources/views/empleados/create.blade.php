<x-app-layout>
    <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Encabezado -->
        <div class="mb-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Registrar Nuevo Empleado</h2>
            <p class="text-gray-600">Complete el formulario para registrar un nuevo empleado en el sistema</p>
        </div>

        <!-- Alertas de error -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Corrija los siguientes errores:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Formulario -->
        <form method="POST" action="{{ route('empleados.store') }}" class="bg-white shadow rounded-lg overflow-hidden">
            @csrf
            
            <div class="px-6 py-5 space-y-6">
                <!-- Identidad -->
                <div>
                    <label for="identidad" class="block text-sm font-medium text-gray-700 mb-1">
                        Número de Identidad <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input 
                            type="text" 
                            name="identidad" 
                            id="identidad" 
                            maxlength="15" 
                            pattern="\d{4}-\d{4}-\d{5}"
                            placeholder="Ej: 0801-1990-12345"
                            class="block w-full pr-10 pl-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            required
                            x-data
                            x-on:input="
                                let val = $event.target.value.replace(/\D/g, '').slice(0, 13);
                                if (val.length >= 5) val = val.slice(0, 4) + '-' + val.slice(4);
                                if (val.length >= 10) val = val.slice(0, 9) + '-' + val.slice(9);
                                $event.target.value = val;
                            "
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Formato: 0000-0000-00000 (13 dígitos con guiones)</p>
                </div>

                <!-- Nombre completo -->
                <div>
                    <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nombre_completo" 
                        id="nombre_completo"
                        class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Ej: Juan Carlos Pérez López"
                        required
                        x-data
                        x-on:input="$event.target.value = $event.target.value.replace(/\b\w/g, l => l.toUpperCase())"
                    >
                </div>

                <!-- Gerencia -->
                <div>
                    <label for="gerencia" class="block text-sm font-medium text-gray-700 mb-1">
                        Gerencia/Departamento <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="gerencia" 
                        id="gerencia"
                        class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        required
                    >
                        <option value="" disabled selected>Seleccione una opción...</option>
                        <option>Dirección Ejecutiva</option>
                        <option>Gerencia Administrativa y Financiera</option>
                        <option>Gerencia de Operaciones Registrales</option>
                        <option>Gerencia de Servicios Empresariales y Afiliaciones</option>
                    </select>
                </div>

                <!-- Ubicación -->
                <div>
                    <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-1">
                        Ubicación <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="ubicacion" 
                        id="ubicacion"
                        class="block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        required
                    >
                        <option value="" disabled selected>Seleccione una ubicación...</option>
                        <option>Choluteca</option>
                        <option>Valle</option>
                    </select>
                </div>
            </div>

            <!-- Pie del formulario -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                <a 
                    href="{{ route('empleados.index') }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Cancelar
                </a>
                <button 
                    type="submit" 
                    class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Registrar Empleado
                </button>
            </div>
        </form>
    </div>
</x-app-layout>