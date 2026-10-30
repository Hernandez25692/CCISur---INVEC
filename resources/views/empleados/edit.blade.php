<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-brand-navy">Editar Colaborador</h1>
            <p class="mt-1 text-sm text-gray-500">Actualiza la información personal, gerencia o sede del colaborador seleccionado</p>
        </div>

        <form method="POST" action="{{ route('empleados.update', $empleado->id) }}"
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-5">
            @csrf
            @method('PUT')

            <!-- Código y Bienes activos (solo lectura) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="codigo" class="block text-sm font-medium text-brand-navy">Código de Colaborador</label>
                    <input type="text" id="codigo" value="{{ $empleado->codigo }}" readonly
                        class="w-full mt-1 border-gray-200 rounded-lg shadow-sm bg-gray-100 text-gray-600 font-mono text-sm cursor-not-allowed">
                </div>

                <div>
                    <span class="block text-sm font-medium text-brand-navy">Bienes a Cargo Actualmente</span>
                    <div class="mt-1 flex items-center justify-between h-[38px] px-3 rounded-lg bg-gray-50 border border-gray-200">
                        <span class="text-xs font-medium text-brand-navy">
                            {{ $empleado->asignaciones_activas_count ?? 0 }} {{ ($empleado->asignaciones_activas_count ?? 0) === 1 ? 'bien activo' : 'bienes activos' }}
                        </span>
                        @if (($empleado->asignaciones_count ?? 0) > 0)
                            <a href="{{ route('asignaciones.historial', $empleado->id) }}"
                                class="text-xs font-semibold text-brand-gold-700 hover:underline">
                                Ver historial →
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Nombre Completo -->
            <div>
                <label for="nombre_completo" class="block text-sm font-medium text-brand-navy">Nombre Completo</label>
                <input type="text" name="nombre_completo" id="nombre_completo"
                    value="{{ old('nombre_completo', $empleado->nombre_completo) }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('nombre_completo') border-red-500 @enderror">
                @error('nombre_completo')
                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Identidad (DNI) -->
            <div>
                <label for="identidad" class="block text-sm font-medium text-brand-navy">Número de Identidad (DNI)</label>
                <input type="text" name="identidad" id="identidad"
                    value="{{ old('identidad', $empleado->identidad) }}" required
                    inputmode="numeric" maxlength="15"
                    class="mt-1 block w-full rounded-lg border-gray-300 font-mono text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('identidad') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">
                    Debe contener exactamente 13 dígitos numéricos (los guiones o espacios se limpian automáticamente).
                </p>
                @error('identidad')
                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gerencia y Ubicación -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="gerencia" class="block text-sm font-medium text-brand-navy">Gerencia</label>
                    <select name="gerencia" id="gerencia" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('gerencia') border-red-500 @enderror">
                        <option value="">-- Seleccionar gerencia --</option>
                        @foreach (\App\Models\Empleado::GERENCIAS as $gerencia)
                            <option value="{{ $gerencia }}" @selected(old('gerencia', $empleado->gerencia) === $gerencia)>
                                {{ $gerencia }}
                            </option>
                        @endforeach
                    </select>
                    @error('gerencia')
                        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ubicacion" class="block text-sm font-medium text-brand-navy">Sede / Ubicación</label>
                    <select name="ubicacion" id="ubicacion" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('ubicacion') border-red-500 @enderror">
                        <option value="">-- Seleccionar sede --</option>
                        @foreach (\App\Models\Empleado::UBICACIONES as $ubicacion)
                            <option value="{{ $ubicacion }}" @selected(old('ubicacion', $empleado->ubicacion) === $ubicacion)>
                                {{ $ubicacion }}
                            </option>
                        @endforeach
                    </select>
                    @error('ubicacion')
                        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <a href="{{ route('empleados.index') }}"
                    class="text-sm font-medium text-gray-600 hover:text-brand-navy transition">
                    ← Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-brand-gold text-brand-navy px-5 py-2.5 rounded-lg hover:bg-brand-gold-400 font-semibold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                    Actualizar Colaborador
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
