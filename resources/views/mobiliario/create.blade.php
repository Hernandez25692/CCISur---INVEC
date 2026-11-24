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
            <div x-data="{
                todosTipos: @js($tipos),
                busqueda: @js(old('tipo', '')),
                abierto: false,
                get tiposFiltrados() {
                    const q = (this.busqueda || '').trim().toLowerCase();
                    if (!q) {
                        return this.todosTipos;
                    }
                    return this.todosTipos.filter(t => t.toLowerCase().includes(q));
                },
                get coincidenciaExacta() {
                    const q = (this.busqueda || '').trim().toLowerCase();
                    return this.todosTipos.find(t => t.toLowerCase() === q);
                },
                seleccionar(t) {
                    this.busqueda = t;
                    this.abierto = false;
                },
                normalizarTexto(txt) {
                    if (!txt) return '';
                    const trim = txt.trim();
                    const match = this.todosTipos.find(t => t.toLowerCase() === trim.toLowerCase());
                    if (match) return match;
                    const menores = ['de', 'del', 'la', 'el', 'en', 'para', 'y', 'o', 'a'];
                    return trim.split(/\s+/).map((p, idx) => {
                        const low = p.toLowerCase();
                        if (idx > 0 && menores.includes(low)) return low;
                        return p.charAt(0).toUpperCase() + p.slice(1).toLowerCase();
                    }).join(' ');
                },
                onBlur() {
                    setTimeout(() => {
                        this.abierto = false;
                        if (this.busqueda) {
                            this.busqueda = this.normalizarTexto(this.busqueda);
                        }
                    }, 200);
                }
            }" class="relative">
                <label for="tipo" class="block text-sm font-medium text-brand-navy">Tipo de Mobiliario</label>
                <div class="relative mt-1">
                    <input type="text" name="tipo" id="tipo"
                        x-model="busqueda"
                        @focus="abierto = true"
                        @input="abierto = true"
                        @blur="onBlur()"
                        @keydown.escape="abierto = false"
                        autocomplete="off"
                        required
                        placeholder="Buscar o escribir tipo (ej. Escritorio, Silla, Podio...)"
                        class="block w-full rounded-lg border-gray-300 pr-10 shadow-sm focus:border-brand-gold focus:ring-brand-gold text-sm @error('tipo') border-red-500 @enderror">

                    <button type="button" @mousedown.prevent @click="abierto = !abierto" tabindex="-1"
                        class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i class="fas fa-chevron-down text-xs transition-transform duration-150" :class="abierto ? 'rotate-180' : ''" aria-hidden="true"></i>
                    </button>
                </div>

                <!-- Dropdown de tipos -->
                <div x-show="abierto" x-cloak
                    @mousedown.outside="abierto = false"
                    class="absolute z-30 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto text-sm divide-y divide-gray-100">

                    <div class="py-1">
                        <template x-for="t in tiposFiltrados" :key="t">
                            <button type="button"
                                @click="seleccionar(t)"
                                class="w-full text-left px-3 py-1.5 hover:bg-amber-50 hover:text-brand-navy flex items-center justify-between text-gray-700 transition-colors">
                                <span x-text="t"></span>
                                <span x-show="(busqueda || '').toLowerCase() === t.toLowerCase()" class="text-amber-600 font-bold text-xs">✓</span>
                            </button>
                        </template>
                        <template x-if="tiposFiltrados.length === 0">
                            <div class="px-3 py-2 text-xs text-gray-500 italic">
                                No se encontraron categorías con "<span x-text="busqueda"></span>".
                            </div>
                        </template>
                    </div>

                    <!-- Opción de agregar / crear tipo nuevo -->
                    <template x-if="(busqueda || '').trim() !== '' && !coincidenciaExacta">
                        <div class="p-1.5 bg-gray-50">
                            <button type="button"
                                @click="seleccionar(normalizarTexto(busqueda))"
                                class="w-full text-left px-2 py-1.5 rounded text-xs font-semibold text-brand-navy hover:bg-amber-100 flex items-center gap-1.5">
                                <i class="fas fa-plus-circle text-amber-600"></i>
                                <span>Crear categoría "<strong x-text="normalizarTexto(busqueda)"></strong>"</span>
                            </button>
                        </div>
                    </template>
                </div>
                @error('tipo')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sede / Ubicación -->
            <div>
                <label for="ubicacion" class="block text-sm font-medium text-brand-navy">Sede / Ubicación</label>
                <select name="ubicacion" id="ubicacion" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('ubicacion') border-red-500 @enderror">
                    <option value="">-- Seleccionar sede --</option>
                    @foreach (\App\Models\Mobiliario::UBICACIONES as $ubicacion)
                        <option value="{{ $ubicacion }}" @selected(old('ubicacion') === $ubicacion)>
                            {{ $ubicacion }}
                        </option>
                    @endforeach
                </select>
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
                    @foreach (\App\Models\Mobiliario::ESTADOS_MANUALES as $estado)
                        <option value="{{ $estado }}" @selected(old('estado') === $estado)>{{ $estado }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    La disponibilidad se calculará automáticamente: los muebles en buen estado quedarán como <strong>Sin Asignar</strong>. Si seleccionás <strong>Dañado / Defectuoso</strong>, el mueble se registrará y pasará automáticamente al <strong>Archivo de Bajas</strong>.
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
