<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-brand-navy">Editar Mueble</h1>
            <p class="mt-1 text-sm text-gray-500">Actualiza la información o estado del bien seleccionado</p>
        </div>

        <form action="{{ route('mobiliario.update', $mobiliario->id) }}" method="POST"
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-5">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-brand-navy">Nombre del Mueble</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $mobiliario->nombre) }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('nombre') border-red-500 @enderror">
                @error('nombre')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Etiqueta y Disponibilidad actual (solo lectura) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="etiqueta" class="block text-sm font-medium text-brand-navy">Etiqueta de Inventario</label>
                    <input type="text" id="etiqueta" value="{{ $mobiliario->etiqueta }}" readonly
                        class="w-full mt-1 border-gray-200 rounded-lg shadow-sm bg-gray-100 text-gray-600 font-mono text-sm cursor-not-allowed">
                </div>

                <div>
                    <span class="block text-sm font-medium text-brand-navy">Disponibilidad Actual</span>
                    <div class="mt-1 flex items-center h-[38px] px-3 rounded-lg bg-gray-50 border border-gray-200">
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset {{ $mobiliario->clase_disponibilidad }}">
                            {{ $mobiliario->disponibilidad_real }}
                        </span>
                        <span class="ml-2 text-xs text-gray-500">(Automática)</span>
                    </div>
                </div>
            </div>

            <!-- Tipo -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-brand-navy">Tipo de Mobiliario</label>
                <select name="tipo" id="tipo" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('tipo') border-red-500 @enderror">
                    <option value="">-- Seleccionar tipo --</option>
                    @foreach (\App\Models\Mobiliario::TIPOS as $tipo)
                        <option value="{{ $tipo }}" @selected(old('tipo', $mobiliario->tipo) === $tipo)>{{ $tipo }}</option>
                    @endforeach
                </select>
                @error('tipo')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ubicación -->
            <div>
                <label for="ubicacion" class="block text-sm font-medium text-brand-navy">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion" value="{{ old('ubicacion', $mobiliario->ubicacion) }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('ubicacion') border-red-500 @enderror">
                @error('ubicacion')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado Físico -->
            @if ($mobiliario->estaAsignado())
                <div class="rounded-lg bg-amber-50/70 border border-amber-200 p-4 space-y-2">
                    <div class="flex items-start gap-2.5">
                        <i class="fas fa-lock text-amber-600 mt-0.5" aria-hidden="true"></i>
                        <div class="text-xs text-amber-900">
                            <p class="font-semibold">Estado físico protegido mientras el mueble está asignado</p>
                            <p class="mt-0.5 text-amber-800">
                                Este bien se encuentra asignado a un colaborador. Para cambiar su estado físico (por daño, revisión o desgaste) o liberarlo, registrá primero su devolución.
                            </p>
                        </div>
                    </div>
                    <div>
                        <label for="estado_bloqueado" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Estado Físico Actual
                        </label>
                        <input type="hidden" name="estado" value="{{ $mobiliario->estado }}">
                        <input type="text" id="estado_bloqueado" value="{{ $mobiliario->estado }}" readonly
                            class="w-full border-amber-200 rounded-lg bg-white/80 text-gray-700 text-sm cursor-not-allowed">
                    </div>
                </div>
            @else
                <div>
                    <label for="estado" class="block text-sm font-medium text-brand-navy">Estado Físico</label>
                    <select name="estado" id="estado" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('estado') border-red-500 @enderror">
                        @foreach (\App\Models\Mobiliario::ESTADOS as $estado)
                            <option value="{{ $estado }}" @selected(old('estado', $mobiliario->estado) === $estado)>
                                {{ $estado }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        Si seleccionás un estado de daño, reparación, incompleto o caducado, la disponibilidad pasará automáticamente a <strong>No Aplica para asignación</strong>.
                    </p>
                    @error('estado')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Uso común / No asignable -->
                <div class="rounded-lg bg-gray-50 p-3.5 border border-gray-200">
                    <label for="es_area_comun" class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="es_area_comun" id="es_area_comun" value="1"
                            @checked(old('_token') !== null ? old('es_area_comun') : ($mobiliario->disponibilidad === \App\Models\Mobiliario::DISPONIBILIDAD_NO_APLICA && \App\Models\Mobiliario::esEstadoAsignable((string) $mobiliario->estado)))
                            class="mt-0.5 rounded border-gray-300 text-brand-navy focus:ring-brand-gold">
                        <span class="text-sm">
                            <span class="font-medium text-brand-navy block">Mueble de área común (No aplica para asignación individual)</span>
                            <span class="text-xs text-gray-500">Marcá esta opción si pertenece a un espacio compartido (ej. sala de sesiones, recepción o comedor).</span>
                        </span>
                    </label>
                </div>
            @endif

            <!-- Fecha de Registro -->
            <div>
                <label for="fecha_registro" class="block text-sm font-medium text-brand-navy">Fecha de Registro</label>
                <input type="date" name="fecha_registro" id="fecha_registro"
                    value="{{ old('fecha_registro', $mobiliario->fecha_registro?->format('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('fecha_registro') border-red-500 @enderror">
                @error('fecha_registro')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <a href="{{ route('mobiliario.index') }}"
                    class="text-sm font-medium text-gray-600 hover:text-brand-navy transition">
                    ← Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-brand-gold text-brand-navy px-5 py-2.5 rounded-lg hover:bg-brand-gold-400 font-semibold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                    Actualizar Mueble
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
