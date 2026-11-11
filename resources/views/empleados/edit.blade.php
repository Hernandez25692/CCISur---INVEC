<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto">
        @php
            $volverAExpediente = request('from') === 'expediente' || request('from') === 'historial' || str_contains(url()->previous(), "/asignaciones/empleado/{$empleado->id}");
            $cancelarUrl = $volverAExpediente ? route('asignaciones.historial', $empleado->id) : route('empleados.index');
        @endphp

        <div class="mb-6">
            <a href="{{ $cancelarUrl }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-navy hover:text-brand-gold-700 transition mb-2">
                <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                {{ $volverAExpediente ? 'Volver a Expediente' : 'Volver a Colaboradores' }}
            </a>
            <h1 class="text-2xl font-bold text-brand-navy">Editar Colaborador</h1>
            <p class="mt-1 text-sm text-gray-500">Actualiza la información personal, gerencia o sede del colaborador seleccionado</p>
        </div>

        <form method="POST" action="{{ route('empleados.update', $empleado->id) }}"
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-5">
            @csrf
            @method('PUT')

            @if ($volverAExpediente)
                <input type="hidden" name="from" value="expediente">
            @endif

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
                    value="{{ $errors->has('identidad') ? '' : old('identidad', $empleado->identidad_formateada) }}" required
                    @if ($errors->has('identidad')) autofocus @endif
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

                @if ($tieneAsignacionActiva ?? ($empleado->asignaciones_activas_count > 0))
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200/80 space-y-2">
                        <div class="flex items-start gap-2 text-xs text-amber-900">
                            <i class="fas fa-lock text-amber-600 mt-0.5" aria-hidden="true"></i>
                            <div>
                                <span class="font-semibold">Sede bloqueada por asignaciones activas:</span>
                                <p class="mt-0.5 text-amber-800">
                                    Este colaborador tiene bienes institucionales en custodia activa. Para transferirlo a otra sede, registre primero la devolución de sus equipos o mobiliario.
                                </p>
                            </div>
                        </div>
                        <div>
                            <label for="ubicacion_bloqueada" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Sede Actual
                            </label>
                            <input type="hidden" name="ubicacion" value="{{ $empleado->ubicacion }}">
                            <input type="text" id="ubicacion_bloqueada" value="Sede {{ $empleado->ubicacion }}" readonly
                                class="w-full border-amber-200 rounded-lg bg-white/80 text-gray-700 text-sm cursor-not-allowed">
                        </div>
                    </div>
                @else
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
                @endif
            </div>

            <!-- Botones -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-100">
                <a href="{{ $cancelarUrl }}"
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
