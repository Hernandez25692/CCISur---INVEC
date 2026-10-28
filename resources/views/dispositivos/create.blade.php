<x-app-layout>
    <div class="py-10 max-w-3xl mx-auto px-4 sm:px-6">
        <h2 class="text-2xl font-bold text-blue-700 mb-6">Registrar Nuevo Dispositivo</h2>

        <form action="{{ route('dispositivos.store') }}" method="POST" class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
            @csrf

            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del dispositivo</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('nombre') border-red-500 @enderror"
                    placeholder="Ej. Laptop HP ProBook">
                @error('nombre')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo de Dispositivo</label>
                <select name="tipo" id="tipo" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('tipo') border-red-500 @enderror">
                    <option value="">-- Seleccionar tipo de dispositivo --</option>
                    @php
                        $tipos = [
                            'Laptop', 'Computadora de escritorio', 'Proyector', 'Impresora', 'Escáner',
                            'Monitor', 'Teclado', 'Mouse', 'Tablet', 'Switch de red', 'Router',
                            'Access Point (AP)', 'Servidor', 'Disco duro externo', 'No-break / UPS',
                            'Cámara Web', 'Micrófono', 'Sistema de sonido', 'Pantalla interactiva',
                            'Control remoto', 'Cargador de laptop', 'Cable HDMI', 'Otro'
                        ];
                    @endphp
                    @foreach ($tipos as $tipoOption)
                        <option value="{{ $tipoOption }}" {{ old('tipo') == $tipoOption ? 'selected' : '' }}>
                            {{ $tipoOption }}
                        </option>
                    @endforeach
                </select>
                @error('tipo')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="marca" class="block text-sm font-medium text-gray-700">Marca</label>
                <input type="text" name="marca" id="marca" value="{{ old('marca') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('marca') border-red-500 @enderror"
                    placeholder="Ej. HP, Dell, Lenovo, Epson...">
                @error('marca')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="modelo" class="block text-sm font-medium text-gray-700">Modelo</label>
                <input type="text" name="modelo" id="modelo" value="{{ old('modelo') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('modelo') border-red-500 @enderror"
                    placeholder="Ej. 14-dq2055wm, L3250...">
                @error('modelo')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="n_serie" class="block text-sm font-medium text-gray-700">Número de Serie</label>
                <input type="text" name="n_serie" id="n_serie" value="{{ old('n_serie') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('n_serie') border-red-500 @enderror"
                    placeholder="Ej. 5CD1234XYZ">
                @error('n_serie')
                    <p class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion" value="{{ old('ubicacion') }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('ubicacion') border-red-500 @enderror"
                    placeholder="Ej. Bodega General, Oficina Dirección...">
                @error('ubicacion')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700">Estado Físico</label>
                <select name="estado" id="estado" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('estado') border-red-500 @enderror">
                    <option value="">-- Seleccionar --</option>
                    @php
                        $estados = [
                            'Nuevo / En perfectas condiciones',
                            'Con pequeños detalles / Imperfecciones leves',
                            'Usado / Segunda mano',
                            'Dañado / Defectuoso',
                            'En reparación / En revisión',
                            'Producto incompleto',
                            'Caducado / No apto para uso'
                        ];
                    @endphp
                    @foreach ($estados as $est)
                        <option value="{{ $est }}" {{ old('estado') == $est ? 'selected' : '' }}>
                            {{ $est }}
                        </option>
                    @endforeach
                </select>
                @error('estado')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="fecha_registro" class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                <input type="date" name="fecha_registro" id="fecha_registro" value="{{ old('fecha_registro', date('Y-m-d')) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('fecha_registro') border-red-500 @enderror">
                @error('fecha_registro')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <a href="{{ route('dispositivos.index') }}" class="text-sm text-gray-500 hover:underline">← Volver al listado</a>
                <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 font-semibold shadow-sm transition">
                    Guardar Dispositivo
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
