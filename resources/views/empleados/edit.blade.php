<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-8 px-4">
        <div class="w-full max-w-xl bg-white rounded-xl shadow-lg p-8">
            <!-- Encabezado -->
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Editar Empleado</h2>
                <p class="text-gray-600">Modifique los datos del empleado y guarde los cambios</p>
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
            <form method="POST" action="{{ route('empleados.update', $empleado->id) }}" class="space-y-6" novalidate>
                @csrf
                @method('PUT')

                <!-- Identidad -->
                <div x-data="{ error: '' }">
                    <label for="identidad" class="block text-sm font-medium text-gray-700 mb-1">
                        Número de Identidad <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="identidad"
                        id="identidad"
                        maxlength="15"
                        pattern="\d{4}-\d{4}-\d{5}"
                        placeholder="Ej: 0801-1990-12345"
                        class="block w-full px-4 py-3 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition border-gray-300 @error('identidad') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        value="{{ old('identidad', $empleado->identidad) }}"
                        required
                        autocomplete="off"
                        x-on:input="
                            let val = $event.target.value.replace(/\D/g, '').slice(0, 13);
                            if (val.length >= 5) val = val.slice(0, 4) + '-' + val.slice(4);
                            if (val.length >= 10) val = val.slice(0, 9) + '-' + val.slice(9);
                            $event.target.value = val;
                            error = (val.replace(/-/g, '').length < 13 && val.length > 0) ? 'Debe ingresar los 13 dígitos del número de identidad.' : '';
                        "
                        x-on:blur="
                            if ($event.target.value.replace(/-/g, '').length < 13) {
                                error = 'Debe ingresar los 13 dígitos del número de identidad.';
                            } else {
                                error = '';
                            }
                        "
                        aria-describedby="identidad-error"
                    >
                    <p class="mt-2 text-sm text-gray-500">Formato: 0000-0000-00000 (13 dígitos con guiones)</p>
                    <template x-if="error">
                        <p class="mt-1 text-sm text-red-600" x-text="error"></p>
                    </template>
                    @error('identidad')
                        <p id="identidad-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nombre completo -->
                <div x-data="{ error: '' }">
                    <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nombre_completo"
                        id="nombre_completo"
                        class="block w-full px-4 py-3 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition border-gray-300 @error('nombre_completo') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        placeholder="Ej: Juan Carlos Pérez López"
                        value="{{ old('nombre_completo', $empleado->nombre_completo) }}"
                        required
                        autocomplete="off"
                        x-on:input="
                            let val = $event.target.value
                                .replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '')
                                .replace(/\s{2,}/g, ' ')
                                .replace(/\b\w/g, l => l.toUpperCase());
                            $event.target.value = val;
                            error = (/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/.test($event.target.value)) ? 'Solo se permiten letras y espacios.' : '';
                        "
                        x-on:blur="
                            if ($event.target.value.trim() === '') {
                                error = 'El nombre completo es obligatorio.';
                            }
                        "
                        aria-describedby="nombre_completo-error"
                    >
                    <template x-if="error">
                        <p class="mt-1 text-sm text-red-600" x-text="error"></p>
                    </template>
                    @error('nombre_completo')
                        <p id="nombre_completo-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gerencia -->
                <div>
                    <label for="gerencia" class="block text-sm font-medium text-gray-700 mb-1">
                        Gerencia/Departamento <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="gerencia"
                        id="gerencia"
                        class="block w-full px-4 py-3 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition border-gray-300 @error('gerencia') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        required
                        aria-describedby="gerencia-error"
                    >
                        <option value="" disabled {{ old('gerencia', $empleado->gerencia) ? '' : 'selected' }}>Seleccione una opción...</option>
                        <option {{ old('gerencia', $empleado->gerencia) == 'Dirección Ejecutiva' ? 'selected' : '' }}>Dirección Ejecutiva</option>
                        <option {{ old('gerencia', $empleado->gerencia) == 'Gerencia Administrativa y Financiera' ? 'selected' : '' }}>Gerencia Administrativa y Financiera</option>
                        <option {{ old('gerencia', $empleado->gerencia) == 'Gerencia de Operaciones Registrales' ? 'selected' : '' }}>Gerencia de Operaciones Registrales</option>
                        <option {{ old('gerencia', $empleado->gerencia) == 'Gerencia de Servicios Empresariales y Afiliaciones' ? 'selected' : '' }}>Gerencia de Servicios Empresariales y Afiliaciones</option>
                    </select>
                    @error('gerencia')
                        <p id="gerencia-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ubicación -->
                <div>
                    <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-1">
                        Ubicación <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="ubicacion"
                        id="ubicacion"
                        class="block w-full px-4 py-3 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition border-gray-300 @error('ubicacion') border-red-500 focus:ring-red-500 focus:border-red-500 @enderror"
                        required
                        aria-describedby="ubicacion-error"
                    >
                        <option value="" disabled {{ old('ubicacion', $empleado->ubicacion) ? '' : 'selected' }}>Seleccione una ubicación...</option>
                        <option {{ old('ubicacion', $empleado->ubicacion) == 'Choluteca' ? 'selected' : '' }}>Choluteca</option>
                        <option {{ old('ubicacion', $empleado->ubicacion) == 'Valle' ? 'selected' : '' }}>Valle</option>
                    </select>
                    @error('ubicacion')
                        <p id="ubicacion-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botón Guardar -->
                <div class="flex justify-end pt-2">
                    <a
                        href="{{ route('empleados.index') }}"
                        class="inline-flex items-center px-4 py-2 mr-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                    >
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Cancelar
                    </a>
                    <div x-data="{ showModal: false, errores: [] }">
                        <button
                            type="submit"
                            class="inline-flex items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                            x-on:click.prevent="
                                errores = [];
                                let identidad = document.getElementById('identidad').value.replace(/-/g, '');
                                let nombre = document.getElementById('nombre_completo').value.trim();
                                let gerencia = document.getElementById('gerencia').value;
                                let ubicacion = document.getElementById('ubicacion').value;

                                if (identidad.length !== 13) {
                                    errores.push('El número de identidad debe tener 13 dígitos.');
                                }
                                if (nombre === '') {
                                    errores.push('El nombre completo es obligatorio.');
                                }
                                if (!gerencia) {
                                    errores.push('Debe seleccionar una gerencia.');
                                }
                                if (!ubicacion) {
                                    errores.push('Debe seleccionar una ubicación.');
                                }

                                if (errores.length > 0) {
                                    showModal = true;
                                } else {
                                    $el.form.submit();
                                }
                            "
                        >
                            <span class="mr-2 text-lg">💾</span>
                            Actualizar
                        </button>

                        <!-- Modal personalizado de errores -->
                        <div
                            x-show="showModal"
                            style="display: none;"
                            class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40"
                        >
                            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
                                <div class="flex items-center mb-4">
                                    <svg class="h-6 w-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-1.414-1.414A9 9 0 105.636 18.364l1.414 1.414A9 9 0 1018.364 5.636z" />
                                    </svg>
                                    <h3 class="text-lg font-semibold text-red-700">No se puede guardar</h3>
                                </div>
                                <p class="text-gray-700 mb-3">Por favor corrija los siguientes errores antes de continuar:</p>
                                <ul class="list-disc pl-5 text-red-600 mb-4">
                                    <template x-for="error in errores" :key="error">
                                        <li x-text="error"></li>
                                    </template>
                                </ul>
                                <div class="flex justify-end">
                                    <button
                                        type="button"
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
                                        x-on:click="showModal = false"
                                    >
                                        Entendido
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
