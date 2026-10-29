<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-brand-navy">Registrar Nuevo Mueble</h1>
            <p class="mt-1 text-sm text-gray-500">Completa los datos para incorporar un nuevo bien al inventario de mobiliario</p>
        </div>

        <form action="{{ route('mobiliario.store') }}" method="POST"
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-5">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-brand-navy">Nombre del Mueble</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                    placeholder="Ej. Escritorio ejecutivo en L"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('nombre') border-red-500 @enderror">
                @error('nombre')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-brand-navy">Tipo de Mobiliario</label>
                <select name="tipo" id="tipo" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('tipo') border-red-500 @enderror">
                    <option value="">-- Seleccionar tipo --</option>
                    @foreach (\App\Models\Mobiliario::TIPOS as $tipo)
                        <option value="{{ $tipo }}" @selected(old('tipo') === $tipo)>{{ $tipo }}</option>
                    @endforeach
                </select>
                @error('tipo')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ubicación -->
            <div>
                <label for="ubicacion" class="block text-sm font-medium text-brand-navy">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion" value="{{ old('ubicacion') }}" required
                    placeholder="Ej. Oficina Dirección, Bodega General"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('ubicacion') border-red-500 @enderror">
                @error('ubicacion')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado -->
            <div>
                <label for="estado" class="block text-sm font-medium text-brand-navy">Estado Físico</label>
                <select name="estado" id="estado" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('estado') border-red-500 @enderror">
                    <option value="">-- Seleccionar estado --</option>
                    @foreach (\App\Models\Mobiliario::ESTADOS as $estado)
                        <option value="{{ $estado }}" @selected(old('estado') === $estado)>{{ $estado }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    La disponibilidad se calculará automáticamente: los muebles en buen estado quedarán como <strong>Sin Asignar</strong> y los dañados o en reparación como <strong>No Aplica para asignación</strong>.
                </p>
                @error('estado')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Uso común / No asignable -->
            <div class="rounded-lg bg-gray-50 p-3.5 border border-gray-200">
                <label for="es_area_comun" class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="es_area_comun" id="es_area_comun" value="1"
                        @checked(old('es_area_comun'))
                        class="mt-0.5 rounded border-gray-300 text-brand-navy focus:ring-brand-gold">
                    <span class="text-sm">
                        <span class="font-medium text-brand-navy block">Mueble de área común (No aplica para asignación individual)</span>
                        <span class="text-xs text-gray-500">Marcá esta opción si pertenece a un espacio compartido (ej. sala de sesiones, recepción o comedor).</span>
                    </span>
                </label>
            </div>

            <!-- Fecha -->
            <div>
                <label for="fecha_registro" class="block text-sm font-medium text-brand-navy">Fecha de Registro</label>
                <input type="date" name="fecha_registro" id="fecha_registro"
                    value="{{ old('fecha_registro', date('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('fecha_registro') border-red-500 @enderror">
                @error('fecha_registro')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <a href="{{ route('mobiliario.index') }}"
                    class="text-sm font-medium text-gray-600 hover:text-brand-navy transition">
                    ← Volver al listado
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-brand-gold text-brand-navy px-5 py-2.5 rounded-lg hover:bg-brand-gold-400 font-semibold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                    Guardar Mueble
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
