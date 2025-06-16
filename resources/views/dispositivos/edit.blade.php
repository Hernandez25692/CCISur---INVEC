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

            <!-- Tipo -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                <select name="tipo" id="tipo" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">-- Seleccionar tipo de dispositivo --</option>
                    <option value="Laptop" {{ old('tipo', $dispositivo->tipo) == 'Laptop' ? 'selected' : '' }}>Laptop
                    </option>
                    <option value="Computadora de escritorio"
                        {{ old('tipo', $dispositivo->tipo) == 'Computadora de escritorio' ? 'selected' : '' }}>
                        Computadora de escritorio</option>
                    <option value="Proyector" {{ old('tipo', $dispositivo->tipo) == 'Proyector' ? 'selected' : '' }}>
                        Proyector</option>
                    <option value="Impresora" {{ old('tipo', $dispositivo->tipo) == 'Impresora' ? 'selected' : '' }}>
                        Impresora</option>
                    <option value="Escáner" {{ old('tipo', $dispositivo->tipo) == 'Escáner' ? 'selected' : '' }}>Escáner
                    </option>
                    <option value="Monitor" {{ old('tipo', $dispositivo->tipo) == 'Monitor' ? 'selected' : '' }}>Monitor
                    </option>
                    <option value="Teclado" {{ old('tipo', $dispositivo->tipo) == 'Teclado' ? 'selected' : '' }}>Teclado
                    </option>
                    <option value="Mouse" {{ old('tipo', $dispositivo->tipo) == 'Mouse' ? 'selected' : '' }}>Mouse
                    </option>
                    <option value="Tablet" {{ old('tipo', $dispositivo->tipo) == 'Tablet' ? 'selected' : '' }}>Tablet
                    </option>
                    <option value="Switch de red"
                        {{ old('tipo', $dispositivo->tipo) == 'Switch de red' ? 'selected' : '' }}>Switch de red
                    </option>
                    <option value="Router" {{ old('tipo', $dispositivo->tipo) == 'Router' ? 'selected' : '' }}>Router
                    </option>
                    <option value="Access Point (AP)"
                        {{ old('tipo', $dispositivo->tipo) == 'Access Point (AP)' ? 'selected' : '' }}>Access Point
                        (AP)</option>
                    <option value="Servidor" {{ old('tipo', $dispositivo->tipo) == 'Servidor' ? 'selected' : '' }}>
                        Servidor</option>
                    <option value="Disco duro externo"
                        {{ old('tipo', $dispositivo->tipo) == 'Disco duro externo' ? 'selected' : '' }}>Disco duro
                        externo</option>
                    <option value="No-break / UPS"
                        {{ old('tipo', $dispositivo->tipo) == 'No-break / UPS' ? 'selected' : '' }}>No-break / UPS
                    </option>
                    <option value="Cámara Web" {{ old('tipo', $dispositivo->tipo) == 'Cámara Web' ? 'selected' : '' }}>
                        Cámara Web</option>
                    <option value="Micrófono" {{ old('tipo', $dispositivo->tipo) == 'Micrófono' ? 'selected' : '' }}>
                        Micrófono</option>
                    <option value="Sistema de sonido"
                        {{ old('tipo', $dispositivo->tipo) == 'Sistema de sonido' ? 'selected' : '' }}>Sistema de
                        sonido</option>
                    <option value="Pantalla interactiva"
                        {{ old('tipo', $dispositivo->tipo) == 'Pantalla interactiva' ? 'selected' : '' }}>Pantalla
                        interactiva</option>
                    <option value="Control remoto"
                        {{ old('tipo', $dispositivo->tipo) == 'Control remoto' ? 'selected' : '' }}>Control remoto
                    </option>
                    <option value="Cargador de laptop"
                        {{ old('tipo', $dispositivo->tipo) == 'Cargador de laptop' ? 'selected' : '' }}>Cargador de
                        laptop</option>
                    <option value="Cable HDMI" {{ old('tipo', $dispositivo->tipo) == 'Cable HDMI' ? 'selected' : '' }}>
                        Cable HDMI</option>
                    <option value="Otro" {{ old('tipo', $dispositivo->tipo) == 'Otro' ? 'selected' : '' }}>Otro
                    </option>
                </select>
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
                <label for="disponibilidad" class="block text-sm font-medium text-gray-700">Disponibilidad</label>
                <select name="disponibilidad" id="disponibilidad" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    @foreach (['Asignado', 'Sin Asignar', 'No Aplica para asignación'] as $disp)
                        <option value="{{ $disp }}"
                            {{ $dispositivo->disponibilidad === $disp ? 'selected' : '' }}>
                            {{ $disp }}
                        </option>
                    @endforeach
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
