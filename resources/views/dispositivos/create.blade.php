<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-brand-navy">Registrar Nuevo Dispositivo</h1>
            <p class="mt-1 text-sm text-gray-500">Completa los datos técnicos para incorporar un nuevo equipo al inventario</p>
        </div>

        <form action="{{ route('dispositivos.store') }}" method="POST"
            x-data="{
                tipoSeleccionado: @js(old('tipo', '')),
                tiposOpcionales: @js(\App\Models\Dispositivo::TIPOS_SERIE_OPCIONAL),
                todosTipos: @js($tipos),
                tipoAbierto: false,
                get tiposFiltrados() {
                    const q = (this.tipoSeleccionado || '').trim().toLowerCase();
                    if (!q) {
                        return this.todosTipos;
                    }
                    return this.todosTipos.filter(t => t.toLowerCase().includes(q));
                },
                get tipoCoincidenciaExacta() {
                    const q = (this.tipoSeleccionado || '').trim().toLowerCase();
                    return this.todosTipos.find(t => t.toLowerCase() === q);
                },
                seleccionarTipo(t) {
                    this.tipoSeleccionado = t;
                    this.tipoAbierto = false;
                },
                normalizarTipoTexto(txt) {
                    if (!txt) return '';
                    const trim = txt.trim();
                    const match = this.todosTipos.find(t => t.toLowerCase() === trim.toLowerCase());
                    if (match) return match;
                    const acronimos = ['UPS', 'AP', 'HDMI', 'PC', 'USB', 'DVR', 'NVR', 'POS'];
                    const menores = ['de', 'del', 'la', 'el', 'en', 'para', 'y', 'o', 'a'];
                    return trim.split(/\s+/).map((p, idx) => {
                        const up = p.toUpperCase();
                        if (acronimos.includes(up)) return up;
                        const low = p.toLowerCase();
                        if (idx > 0 && menores.includes(low)) return low;
                        return p.charAt(0).toUpperCase() + p.slice(1).toLowerCase();
                    }).join(' ');
                },
                onBlurTipo() {
                    setTimeout(() => {
                        this.tipoAbierto = false;
                        if (this.tipoSeleccionado) {
                            this.tipoSeleccionado = this.normalizarTipoTexto(this.tipoSeleccionado);
                        }
                    }, 200);
                },
                get serieObligatoria() {
                    return !this.tipoSeleccionado || !this.tiposOpcionales.includes(this.tipoSeleccionado);
                },
                get esComputadora() {
                    return ['Laptop', 'Computadora de escritorio', 'Servidor', 'Tablet'].includes(this.tipoSeleccionado);
                },
                get esRed() {
                    return ['Switch de red', 'Router', 'Access Point (AP)'].includes(this.tipoSeleccionado);
                },
                get esPantalla() {
                    return ['Monitor', 'Pantalla interactiva'].includes(this.tipoSeleccionado);
                },
                get esImpresion() {
                    return ['Impresora', 'Escáner'].includes(this.tipoSeleccionado);
                },
                get esUps() {
                    return this.tipoSeleccionado === 'No-break / UPS';
                },
                get muestraEspecificaciones() {
                    return (this.tipoSeleccionado || '').trim() !== '';
                }
            }"
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-5">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-brand-navy">Nombre del Dispositivo</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                    placeholder="Ej. Laptop HP ProBook 450 G9"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('nombre') border-red-500 @enderror">
                @error('nombre')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo y Número de Serie -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="relative">
                    <label for="tipo" class="block text-sm font-medium text-brand-navy">Tipo de Dispositivo</label>
                    <div class="relative mt-1">
                        <input type="text" name="tipo" id="tipo"
                            x-model="tipoSeleccionado"
                            @focus="tipoAbierto = true"
                            @input="tipoAbierto = true"
                            @blur="onBlurTipo()"
                            @keydown.escape="tipoAbierto = false"
                            autocomplete="off"
                            required
                            placeholder="Buscar o escribir tipo (ej. Laptop, Router...)"
                            class="block w-full rounded-lg border-gray-300 pr-10 shadow-sm focus:border-brand-gold focus:ring-brand-gold text-sm @error('tipo') border-red-500 @enderror">

                        <button type="button" @mousedown.prevent @click="tipoAbierto = !tipoAbierto" tabindex="-1" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i class="fas fa-chevron-down text-xs transition-transform duration-150" :class="tipoAbierto ? 'rotate-180' : ''" aria-hidden="true"></i>
                        </button>
                    </div>

                    <!-- Dropdown de tipos -->
                    <div x-show="tipoAbierto" x-cloak
                        @mousedown.outside="tipoAbierto = false"
                        class="absolute z-30 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto text-sm divide-y divide-gray-100">

                        <div class="py-1">
                            <template x-for="t in tiposFiltrados" :key="t">
                                <button type="button"
                                    @click="seleccionarTipo(t)"
                                    class="w-full text-left px-3 py-1.5 hover:bg-amber-50 hover:text-brand-navy flex items-center justify-between text-gray-700 transition-colors">
                                    <span x-text="t"></span>
                                    <span x-show="(tipoSeleccionado || '').toLowerCase() === t.toLowerCase()" class="text-amber-600 font-bold text-xs">✓</span>
                                </button>
                            </template>
                            <template x-if="tiposFiltrados.length === 0">
                                <div class="px-3 py-2 text-xs text-gray-500 italic">
                                    No se encontraron categorías con "<span x-text="tipoSeleccionado"></span>".
                                </div>
                            </template>
                        </div>

                        <!-- Opción de agregar / crear tipo nuevo -->
                        <template x-if="(tipoSeleccionado || '').trim() !== '' && !tipoCoincidenciaExacta">
                            <div class="p-1.5 bg-gray-50">
                                <button type="button"
                                    @click="seleccionarTipo(normalizarTipoTexto(tipoSeleccionado))"
                                    class="w-full text-left px-2 py-1.5 rounded text-xs font-semibold text-brand-navy hover:bg-amber-100 flex items-center gap-1.5">
                                    <i class="fas fa-plus-circle text-amber-600"></i>
                                    <span>Crear categoría "<strong x-text="normalizarTipoTexto(tipoSeleccionado)"></strong>"</span>
                                </button>
                            </div>
                        </template>
                    </div>
                    @error('tipo')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="n_serie" class="block text-sm font-medium text-brand-navy">
                        Número de Serie
                        <span x-show="!serieObligatoria" x-cloak class="text-xs font-normal text-gray-500">(Opcional)</span>
                    </label>
                    <input type="text" name="n_serie" id="n_serie" value="{{ old('n_serie') }}"
                        :required="serieObligatoria"
                        :placeholder="serieObligatoria ? 'Ej. 5CD1234XYZ' : 'Opcional para este accesorio'"
                        class="mt-1 block w-full rounded-lg border-gray-300 font-mono text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('n_serie') border-red-500 @enderror">
                    @error('n_serie')
                        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Marca y Modelo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div x-data="{
                    todasMarcas: @js($marcas),
                    marcasPorTipo: @js($marcasPorTipo),
                    busqueda: @js(old('marca', '')),
                    abierto: false,
                    get sugeridas() {
                        if (!tipoSeleccionado || !this.marcasPorTipo[tipoSeleccionado]) {
                            return [];
                        }
                        return this.marcasPorTipo[tipoSeleccionado];
                    },
                    get marcasFiltradas() {
                        const q = (this.busqueda || '').trim().toLowerCase();
                        if (!q) {
                            return this.todasMarcas;
                        }
                        return this.todasMarcas.filter(m => m.toLowerCase().includes(q));
                    },
                    get coincidenciaExacta() {
                        const q = (this.busqueda || '').trim().toLowerCase();
                        return this.todasMarcas.find(m => m.toLowerCase() === q);
                    },
                    seleccionar(marca) {
                        this.busqueda = marca;
                        this.abierto = false;
                    },
                    normalizarTexto(txt) {
                        if (!txt) return '';
                        const trim = txt.trim();
                        const match = this.todasMarcas.find(m => m.toLowerCase() === trim.toLowerCase());
                        if (match) return match;
                        if (trim.length <= 3 && /^[a-zA-Z0-9]+$/.test(trim)) return trim.toUpperCase();
                        return trim.charAt(0).toUpperCase() + trim.slice(1).toLowerCase();
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
                    <label for="marca" class="block text-sm font-medium text-brand-navy">Marca</label>
                    <div class="relative mt-1">
                        <input type="text" name="marca" id="marca"
                            x-model="busqueda"
                            @focus="abierto = true"
                            @input="abierto = true"
                            @blur="onBlur()"
                            @keydown.escape="abierto = false"
                            autocomplete="off"
                            required
                            placeholder="Buscar o escribir marca (ej. HP, Dell...)"
                            class="block w-full rounded-lg border-gray-300 pr-10 shadow-sm focus:border-brand-gold focus:ring-brand-gold text-sm @error('marca') border-red-500 @enderror">

                        <button type="button" @mousedown.prevent @click="abierto = !abierto" tabindex="-1" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <i class="fas fa-chevron-down text-xs transition-transform duration-150" :class="abierto ? 'rotate-180' : ''" aria-hidden="true"></i>
                        </button>
                    </div>

                    <!-- Dropdown de marcas -->
                    <div x-show="abierto" x-cloak
                        @mousedown.outside="abierto = false"
                        class="absolute z-30 mt-1 w-full bg-white rounded-lg shadow-lg border border-gray-200 max-h-60 overflow-y-auto text-sm divide-y divide-gray-100">

                        <div class="py-1">
                            <template x-for="marca in marcasFiltradas" :key="marca">
                                <button type="button"
                                    @click="seleccionar(marca)"
                                    class="w-full text-left px-3 py-1.5 hover:bg-amber-50 hover:text-brand-navy flex items-center justify-between text-gray-700 transition-colors">
                                    <span x-text="marca"></span>
                                    <span x-show="(busqueda || '').toLowerCase() === marca.toLowerCase()" class="text-amber-600 font-bold text-xs">✓</span>
                                </button>
                            </template>
                            <template x-if="marcasFiltradas.length === 0">
                                <div class="px-3 py-2 text-xs text-gray-500 italic">
                                    No se encontraron marcas registradas con "<span x-text="busqueda"></span>".
                                </div>
                            </template>
                        </div>

                        <!-- Opción de agregar / usar marca nueva -->
                        <template x-if="(busqueda || '').trim() !== '' && !coincidenciaExacta">
                            <div class="p-1.5 bg-gray-50">
                                <button type="button"
                                    @click="seleccionar(normalizarTexto(busqueda))"
                                    class="w-full text-left px-2 py-1.5 rounded text-xs font-semibold text-brand-navy hover:bg-amber-100 flex items-center gap-1.5">
                                    <i class="fas fa-plus-circle text-amber-600"></i>
                                    <span>Usar "<strong x-text="normalizarTexto(busqueda)"></strong>" como marca nueva</span>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Sugerencias rápidas por tipo de dispositivo seleccionado -->
                    <div x-show="sugeridas.length > 0" class="mt-1.5 flex flex-wrap items-center gap-1">
                        <span class="text-[11px] text-gray-400">Sugeridas:</span>
                        <template x-for="sug in sugeridas" :key="sug">
                            <button type="button"
                                @click="seleccionar(sug)"
                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-medium border transition-colors"
                                :class="(busqueda || '').toLowerCase() === sug.toLowerCase()
                                    ? 'bg-amber-500 text-white border-amber-600'
                                    : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-300'"
                                x-text="sug">
                            </button>
                        </template>
                    </div>

                    @error('marca')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="modelo" class="block text-sm font-medium text-brand-navy">Modelo</label>
                    <input type="text" name="modelo" id="modelo" value="{{ old('modelo') }}" required
                        placeholder="Ej. 14-dq2055wm, EcoTank L3250"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('modelo') border-red-500 @enderror">
                    @error('modelo')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Sede / Ubicación -->
            <div>
                <label for="ubicacion" class="block text-sm font-medium text-brand-navy">Sede / Ubicación</label>
                <select name="ubicacion" id="ubicacion" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('ubicacion') border-red-500 @enderror">
                    <option value="">-- Seleccionar sede --</option>
                    @foreach (\App\Models\Dispositivo::UBICACIONES as $ubicacion)
                        <option value="{{ $ubicacion }}" @selected(old('ubicacion') === $ubicacion)>
                            {{ $ubicacion }}
                        </option>
                    @endforeach
                </select>
                @error('ubicacion')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado Físico -->
            <div>
                <label for="estado" class="block text-sm font-medium text-brand-navy">Estado Físico</label>
                <select name="estado" id="estado" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('estado') border-red-500 @enderror">
                    <option value="">-- Seleccionar estado --</option>
                    @foreach (\App\Models\Dispositivo::ESTADOS_MANUALES as $est)
                        <option value="{{ $est }}" @selected(old('estado') === $est)>
                            {{ $est }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    La disponibilidad se calculará automáticamente: los equipos en buen estado quedarán como <strong>Sin Asignar</strong>. Si seleccionás <strong>Dañado / Defectuoso</strong>, el dispositivo se registrará y pasará automáticamente al <strong>Archivo de Bajas</strong>.
                </p>
                @error('estado')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Uso común / Infraestructura -->
            <div class="rounded-lg bg-gray-50 p-3.5 border border-gray-200">
                <label for="es_area_comun" class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="es_area_comun" id="es_area_comun" value="1"
                        @checked(old('es_area_comun'))
                        class="mt-0.5 rounded border-gray-300 text-brand-navy focus:ring-brand-gold">
                    <span class="text-sm">
                        <span class="font-medium text-brand-navy block">Equipo de área común o infraestructura (No aplica para asignación individual)</span>
                        <span class="text-xs text-gray-500">Marcá esta opción si es un equipo compartido o de red (ej. router, switch, servidor, impresora compartida o proyector de salón).</span>
                    </span>
                </label>
            </div>

            <!-- Bloque de Especificaciones Técnicas (Reactivo con Alpine) -->
            <div x-show="muestraEspecificaciones" x-cloak
                class="rounded-xl border border-brand-navy/15 bg-brand-navy-50/40 p-4 sm:p-5 space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-brand-navy/10">
                    <span class="w-7 h-7 rounded-lg bg-brand-navy text-brand-gold flex items-center justify-center text-xs">
                        <i class="fas fa-microchip" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-brand-navy">Especificaciones Técnicas del Hardware</h3>
                        <p class="text-xs text-gray-500">Configuración interna y características técnicas para control y actas oficiales</p>
                    </div>
                </div>

                <!-- Grupo Computadoras (Laptop, PC, Servidor, Tablet) -->
                <div x-show="esComputadora" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Procesador -->
                        <div>
                            <label for="esp_procesador" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Procesador (CPU)
                            </label>
                            <input type="text" name="especificaciones[procesador]" id="esp_procesador"
                                list="lista_procesadores"
                                value="{{ old('especificaciones.procesador') }}"
                                placeholder="Ej. Intel Core i5-1135G7 / Ryzen 5"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <datalist id="lista_procesadores">
                                <option value="Intel Core i3">
                                <option value="Intel Core i5">
                                <option value="Intel Core i7">
                                <option value="Intel Core i9">
                                <option value="AMD Ryzen 3">
                                <option value="AMD Ryzen 5">
                                <option value="AMD Ryzen 7">
                                <option value="Apple M1">
                                <option value="Apple M2">
                                <option value="Apple M3">
                                <option value="Intel Celeron">
                                <option value="Intel Xeon">
                            </datalist>
                        </div>

                        <!-- Memoria RAM -->
                        <div x-data="{ valorRam: @js(old('especificaciones.ram', '')) }">
                            <label for="esp_ram" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Memoria RAM
                            </label>
                            <select name="especificaciones[ram]" id="esp_ram" x-model="valorRam"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar RAM --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_RAM as $ram)
                                    <option value="{{ $ram }}" @selected(old('especificaciones.ram') === $ram)>
                                        {{ $ram === 'Otro' ? 'Otro / Personalizado...' : $ram }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorRam === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[ram_otro]"
                                    value="{{ old('especificaciones.ram_otro') }}"
                                    placeholder="Escribe la memoria RAM (ej. 48 GB DDR5)"
                                    class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Almacenamiento -->
                        <div x-data="{ valorAlm: @js(old('especificaciones.almacenamiento', '')) }">
                            <label for="esp_almacenamiento" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Almacenamiento Principal
                            </label>
                            <select name="especificaciones[almacenamiento]" id="esp_almacenamiento" x-model="valorAlm"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar almacenamiento --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_ALMACENAMIENTO as $alm)
                                    <option value="{{ $alm }}" @selected(old('especificaciones.almacenamiento') === $alm)>
                                        {{ $alm === 'Otro' ? 'Otro / Personalizado...' : $alm }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorAlm === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[almacenamiento_otro]"
                                    value="{{ old('especificaciones.almacenamiento_otro') }}"
                                    placeholder="Escribe el almacenamiento (ej. 2 TB NVMe + 1 TB HDD)"
                                    class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                        </div>

                        <!-- Sistema Operativo -->
                        <div x-data="{ valorSo: @js(old('especificaciones.sistema_operativo', '')) }">
                            <label for="esp_so" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Sistema Operativo
                            </label>
                            <select name="especificaciones[sistema_operativo]" id="esp_so" x-model="valorSo"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar S.O. --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_SO as $so)
                                    <option value="{{ $so }}" @selected(old('especificaciones.sistema_operativo') === $so)>
                                        {{ $so === 'Otro' ? 'Otro / Personalizado...' : $so }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorSo === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[sistema_operativo_otro]"
                                    value="{{ old('especificaciones.sistema_operativo_otro') }}"
                                    placeholder="Escribe el sistema operativo (ej. Windows Server 2022)"
                                    class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grupo Red (Switch, Router, AP) -->
                <div x-show="esRed" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Cantidad de Puertos -->
                        <div x-data="{ valorPuertos: @js(old('especificaciones.puertos_red', '')) }">
                            <label for="esp_puertos_red" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Cantidad de Puertos
                            </label>
                            <select name="especificaciones[puertos_red]" id="esp_puertos_red" x-model="valorPuertos"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar puertos --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_PUERTOS_RED as $ptos)
                                    <option value="{{ $ptos }}" @selected(old('especificaciones.puertos_red') === $ptos)>
                                        {{ $ptos === 'Otro' ? 'Otro / Personalizado...' : $ptos }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorPuertos === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[puertos_red_otro]"
                                    value="{{ old('especificaciones.puertos_red_otro') }}"
                                    placeholder="Escribe la cantidad (ej. 52 Puertos + 4 SFP+)"
                                    class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                        </div>

                        <!-- Velocidad de Puertos -->
                        <div x-data="{ valorVelocidad: @js(old('especificaciones.velocidad_red', '')) }">
                            <label for="esp_velocidad_red" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Velocidad de Enlace / Puertos
                            </label>
                            <select name="especificaciones[velocidad_red]" id="esp_velocidad_red" x-model="valorVelocidad"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar velocidad --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_VELOCIDAD_RED as $vel)
                                    <option value="{{ $vel }}" @selected(old('especificaciones.velocidad_red') === $vel)>
                                        {{ $vel === 'Otro' ? 'Otro / Personalizado...' : $vel }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorVelocidad === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[velocidad_red_otro]"
                                    value="{{ old('especificaciones.velocidad_red_otro') }}"
                                    placeholder="Escribe la velocidad (ej. 2.5 Gbps Multi-Gigabit)"
                                    class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- PoE -->
                        <div x-data="{ valorPoe: @js(old('especificaciones.poe', '')) }">
                            <label for="esp_poe" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Soporte PoE (Power over Ethernet)
                            </label>
                            <select name="especificaciones[poe]" id="esp_poe" x-model="valorPoe"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar PoE --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_POE as $poe)
                                    <option value="{{ $poe }}" @selected(old('especificaciones.poe') === $poe)>
                                        {{ $poe === 'Otro' ? 'Otro / Personalizado...' : $poe }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorPoe === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[poe_otro]"
                                    value="{{ old('especificaciones.poe_otro') }}"
                                    placeholder="Escribe el estándar PoE (ej. PoE++ 60W)"
                                    class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                        </div>

                        <!-- Gestión -->
                        <div>
                            <label for="esp_gestion_red" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Gestión / Administración
                            </label>
                            <select name="especificaciones[gestion_red]" id="esp_gestion_red"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar gestión --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_GESTION_RED as $gst)
                                    <option value="{{ $gst }}" @selected(old('especificaciones.gestion_red') === $gst)>{{ $gst }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Grupo Pantallas (Monitor, Pantalla interactiva) -->
                <div x-show="esPantalla" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div x-data="{ valorPantalla: @js(old('especificaciones.tamano_pantalla', '')) }">
                        <label for="esp_tamano_pantalla" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Tamaño de Pantalla
                        </label>
                        <select name="especificaciones[tamano_pantalla]" id="esp_tamano_pantalla" x-model="valorPantalla"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">-- Seleccionar tamaño --</option>
                            @foreach (\App\Models\Dispositivo::OPCIONES_TAMANO_PANTALLA as $tam)
                                <option value="{{ $tam }}" @selected(old('especificaciones.tamano_pantalla') === $tam)>
                                    {{ $tam === 'Otro' ? 'Otro / Personalizado...' : $tam }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="valorPantalla === 'Otro'" x-cloak class="mt-2">
                            <input type="text" name="especificaciones[tamano_pantalla_otro]"
                                value="{{ old('especificaciones.tamano_pantalla_otro') }}"
                                placeholder="Escribe el tamaño (ej. 75 pulgadas)"
                                class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>

                    <div>
                        <label for="esp_conectores_pantalla" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Conectores / Puertos de Video
                        </label>
                        <input type="text" name="especificaciones[conectores_pantalla]" id="esp_conectores_pantalla"
                            value="{{ old('especificaciones.conectores_pantalla') }}"
                            placeholder="Ej. HDMI, VGA, DisplayPort"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                </div>

                <!-- Grupo Impresión (Impresora, Escáner) -->
                <div x-show="esImpresion" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div x-data="{ valorTecImp: @js(old('especificaciones.tecnologia_impresion', '')) }">
                        <label for="esp_tecnologia_impresion" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Tecnología de Impresión
                        </label>
                        <select name="especificaciones[tecnologia_impresion]" id="esp_tecnologia_impresion" x-model="valorTecImp"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">-- Seleccionar tecnología --</option>
                            @foreach (\App\Models\Dispositivo::OPCIONES_TECNOLOGIA_IMPRESION as $tec)
                                <option value="{{ $tec }}" @selected(old('especificaciones.tecnologia_impresion') === $tec)>
                                    {{ $tec === 'Otro' ? 'Otro / Personalizado...' : $tec }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="valorTecImp === 'Otro'" x-cloak class="mt-2">
                            <input type="text" name="especificaciones[tecnologia_impresion_otro]"
                                value="{{ old('especificaciones.tecnologia_impresion_otro') }}"
                                placeholder="Escribe la tecnología (ej. Matriz de punto)"
                                class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>

                    <div x-data="{ valorConImp: @js(old('especificaciones.conectividad_impresion', '')) }">
                        <label for="esp_conectividad_impresion" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Conectividad
                        </label>
                        <select name="especificaciones[conectividad_impresion]" id="esp_conectividad_impresion" x-model="valorConImp"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">-- Seleccionar conectividad --</option>
                            @foreach (\App\Models\Dispositivo::OPCIONES_CONECTIVIDAD_IMPRESION as $con)
                                <option value="{{ $con }}" @selected(old('especificaciones.conectividad_impresion') === $con)>
                                    {{ $con === 'Otro' ? 'Otro / Personalizado...' : $con }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="valorConImp === 'Otro'" x-cloak class="mt-2">
                            <input type="text" name="especificaciones[conectividad_impresion_otro]"
                                value="{{ old('especificaciones.conectividad_impresion_otro') }}"
                                placeholder="Escribe la conectividad (ej. Bluetooth + USB)"
                                class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                </div>

                <!-- Grupo UPS -->
                <div x-show="esUps" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div x-data="{ valorUps: @js(old('especificaciones.capacidad_ups', '')) }">
                        <label for="esp_capacidad_ups" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Capacidad de Carga
                        </label>
                        <select name="especificaciones[capacidad_ups]" id="esp_capacidad_ups" x-model="valorUps"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">-- Seleccionar capacidad --</option>
                            @foreach (\App\Models\Dispositivo::OPCIONES_CAPACIDAD_UPS as $cap)
                                <option value="{{ $cap }}" @selected(old('especificaciones.capacidad_ups') === $cap)>
                                    {{ $cap === 'Otro' ? 'Otro / Personalizado...' : $cap }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="valorUps === 'Otro'" x-cloak class="mt-2">
                            <input type="text" name="especificaciones[capacidad_ups_otro]"
                                value="{{ old('especificaciones.capacidad_ups_otro') }}"
                                placeholder="Escribe la capacidad (ej. 3000 VA Online)"
                                class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                </div>

                <!-- Campo de Notas Técnicas Adicionales (Común para todos) -->
                <div>
                    <label for="esp_notas_tecnicas" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                        Detalles o Notas Técnicas Adicionales <span class="text-xs font-normal text-gray-500 lowercase">(opcional)</span>
                    </label>
                    <input type="text" name="especificaciones[notas_tecnicas]" id="esp_notas_tecnicas"
                        value="{{ old('especificaciones.notas_tecnicas') }}"
                        placeholder="Ej. Teclado retroiluminado, incluye adaptador USB-C o dirección MAC"
                        class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                </div>
            </div>

            <!-- Fecha de Registro -->
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
                <a href="{{ route('dispositivos.index') }}"
                    class="text-sm font-medium text-gray-600 hover:text-brand-navy transition">
                    ← Volver al listado
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-brand-gold text-brand-navy px-5 py-2.5 rounded-lg hover:bg-brand-gold-400 font-semibold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                    Guardar Dispositivo
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
