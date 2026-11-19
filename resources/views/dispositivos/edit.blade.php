<x-app-layout>
    <div class="py-6 max-w-3xl mx-auto">
        @php
            $volverAHistorial = request('from') === 'historial' || str_contains(url()->previous(), "/dispositivos/{$dispositivo->id}/historial");
            $cancelarUrl = $volverAHistorial ? route('dispositivos.historial', $dispositivo->id) : route('dispositivos.index');
        @endphp

        <div class="mb-6">
            <a href="{{ $cancelarUrl }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-navy hover:text-brand-gold-700 transition mb-2">
                <i class="fas fa-arrow-left text-[10px]" aria-hidden="true"></i>
                {{ $volverAHistorial ? 'Volver a Hoja de Vida' : 'Volver a Inventario de Dispositivos' }}
            </a>
            <h1 class="text-2xl font-bold text-brand-navy">Editar Dispositivo</h1>
            <p class="mt-1 text-sm text-gray-500">Actualiza la información técnica o el estado físico del equipo seleccionado</p>
        </div>

        <form action="{{ route('dispositivos.update', $dispositivo->id) }}" method="POST"
            x-data="{
                tipoSeleccionado: @js(old('tipo', $dispositivo->tipo)),
                tiposOpcionales: @js(\App\Models\Dispositivo::TIPOS_SERIE_OPCIONAL),
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
                    return this.tipoSeleccionado !== '';
                }
            }"
            class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 space-y-5">
            @csrf
            @method('PUT')

            @if ($volverAHistorial)
                <input type="hidden" name="from" value="historial">
            @endif

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-brand-navy">Nombre del Dispositivo</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $dispositivo->nombre) }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('nombre') border-red-500 @enderror">
                @error('nombre')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Etiqueta y Disponibilidad actual (solo lectura) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="etiqueta" class="block text-sm font-medium text-brand-navy">Etiqueta de Inventario</label>
                    <input type="text" id="etiqueta" value="{{ $dispositivo->etiqueta }}" readonly
                        class="w-full mt-1 border-gray-200 rounded-lg shadow-sm bg-gray-100 text-gray-600 font-mono text-sm cursor-not-allowed">
                </div>

                <div>
                    <span class="block text-sm font-medium text-brand-navy">Disponibilidad Actual</span>
                    <div class="mt-1 flex items-center h-[38px] px-3 rounded-lg bg-gray-50 border border-gray-200">
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset {{ $dispositivo->clase_disponibilidad }}">
                            {{ $dispositivo->disponibilidad_real }}
                        </span>
                        <span class="ml-2 text-xs text-gray-500">(Automática)</span>
                    </div>
                </div>
            </div>

            <!-- Tipo y Número de Serie -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-brand-navy">Tipo de Dispositivo</label>
                    <div class="mt-1 flex items-center justify-between h-[38px] px-3 rounded-lg bg-gray-100 border border-gray-200 text-gray-700 text-sm cursor-not-allowed">
                        <span class="font-medium text-brand-navy">{{ $dispositivo->tipo }}</span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-gray-500 bg-white/80 border border-gray-200 px-2 py-0.5 rounded">
                            <i class="fas fa-lock text-[10px]" aria-hidden="true"></i> Fijo
                        </span>
                    </div>
                    <input type="hidden" name="tipo" value="{{ $dispositivo->tipo }}">
                    <p class="text-[11px] text-gray-400 mt-1">El tipo de equipo no se puede modificar tras su registro.</p>
                </div>

                <div>
                    <label for="n_serie" class="block text-sm font-medium text-brand-navy">
                        Número de Serie
                        <span x-show="!serieObligatoria" x-cloak class="text-xs font-normal text-gray-500">(Opcional)</span>
                    </label>
                    <input type="text" name="n_serie" id="n_serie" value="{{ old('n_serie', $dispositivo->n_serie) }}"
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
                <div>
                    <label for="marca" class="block text-sm font-medium text-brand-navy">Marca</label>
                    <input type="text" name="marca" id="marca" value="{{ old('marca', $dispositivo->marca) }}" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('marca') border-red-500 @enderror">
                    @error('marca')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="modelo" class="block text-sm font-medium text-brand-navy">Modelo</label>
                    <input type="text" name="modelo" id="modelo" value="{{ old('modelo', $dispositivo->modelo) }}" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('modelo') border-red-500 @enderror">
                    @error('modelo')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Sede / Ubicación -->
            @if ($dispositivo->estaAsignado())
                <div class="rounded-lg bg-amber-50/70 border border-amber-200 p-4 space-y-2">
                    <div class="flex items-start gap-2.5">
                        <i class="fas fa-lock text-amber-600 mt-0.5" aria-hidden="true"></i>
                        <div class="text-xs text-amber-900">
                            <p class="font-semibold">Sede protegida mientras el dispositivo está asignado</p>
                            <p class="mt-0.5 text-amber-800">
                                Este equipo se encuentra actualmente en custodia activa de un colaborador. Para transferirlo a otra sede, registre primero el acta de devolución correspondiente.
                            </p>
                        </div>
                    </div>
                    <div>
                        <label for="ubicacion_bloqueada" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Sede Actual
                        </label>
                        <input type="hidden" name="ubicacion" value="{{ $dispositivo->ubicacion }}">
                        <input type="text" id="ubicacion_bloqueada" value="Sede {{ $dispositivo->ubicacion }}" readonly
                            class="w-full border-amber-200 rounded-lg bg-white/80 text-gray-700 text-sm cursor-not-allowed">
                    </div>
                </div>
            @else
                <div>
                    <label for="ubicacion" class="block text-sm font-medium text-brand-navy">Sede / Ubicación</label>
                    <select name="ubicacion" id="ubicacion" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('ubicacion') border-red-500 @enderror">
                        <option value="">-- Seleccionar sede --</option>
                        @foreach (\App\Models\Dispositivo::UBICACIONES as $ubicacion)
                            <option value="{{ $ubicacion }}" @selected(old('ubicacion', $dispositivo->ubicacion) === $ubicacion)>
                                {{ $ubicacion }}
                            </option>
                        @endforeach
                    </select>
                    @error('ubicacion')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <!-- Estado Físico -->
            @if ($dispositivo->mantenimientoActivo()->exists())
                <div class="rounded-lg bg-purple-50/80 border border-purple-200 p-4 space-y-2">
                    <div class="flex items-start gap-2.5">
                        <i class="fas fa-tools text-purple-600 mt-0.5" aria-hidden="true"></i>
                        <div class="text-xs text-purple-900">
                            <p class="font-semibold">Estado físico protegido mientras el equipo está en mantenimiento activo</p>
                            <p class="mt-0.5 text-purple-800">
                                Este dispositivo se encuentra actualmente en taller / revisión técnica. Para actualizar su estado físico resultante o liberarlo, finalice o cancele el mantenimiento desde su hoja de vida.
                            </p>
                        </div>
                    </div>
                    <div>
                        <label for="estado_bloqueado_mant" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Estado Físico Actual
                        </label>
                        <input type="hidden" name="estado" value="{{ $dispositivo->estado }}">
                        <input type="text" id="estado_bloqueado_mant" value="{{ $dispositivo->estado }}" readonly
                            class="w-full border-purple-200 rounded-lg bg-white/80 text-gray-700 text-sm cursor-not-allowed">
                    </div>
                </div>
            @elseif ($dispositivo->estaAsignado())
                <div class="rounded-lg bg-amber-50/70 border border-amber-200 p-4 space-y-2">
                    <div class="flex items-start gap-2.5">
                        <i class="fas fa-lock text-amber-600 mt-0.5" aria-hidden="true"></i>
                        <div class="text-xs text-amber-900">
                            <p class="font-semibold">Estado físico protegido mientras el dispositivo está asignado</p>
                            <p class="mt-0.5 text-amber-800">
                                Este equipo se encuentra asignado a un colaborador. Para cambiar su estado físico (por daño, revisión o desgaste) o liberarlo, registrá primero su devolución.
                            </p>
                        </div>
                    </div>
                    <div>
                        <label for="estado_bloqueado" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Estado Físico Actual
                        </label>
                        <input type="hidden" name="estado" value="{{ $dispositivo->estado }}">
                        <input type="text" id="estado_bloqueado" value="{{ $dispositivo->estado }}" readonly
                            class="w-full border-amber-200 rounded-lg bg-white/80 text-gray-700 text-sm cursor-not-allowed">
                    </div>
                </div>
            @else
                <div>
                    @php
                        $puedeConservarNuevo = $dispositivo->estado === \App\Models\Dispositivo::ESTADO_NUEVO
                            && ! $dispositivo->asignaciones()->exists()
                            && ! $dispositivo->mantenimientos()->exists();
                    @endphp
                    <label for="estado" class="block text-sm font-medium text-brand-navy">Estado Físico</label>
                    <select name="estado" id="estado" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('estado') border-red-500 @enderror">
                        @foreach (\App\Models\Dispositivo::ESTADOS_MANUALES as $est)
                            @if ($est === \App\Models\Dispositivo::ESTADO_NUEVO && ! $puedeConservarNuevo)
                                @continue
                            @endif
                            <option value="{{ $est }}" @selected(old('estado', $dispositivo->estado) === $est)>
                                {{ $est }}
                            </option>
                        @endforeach
                    </select>
                    @if (! $puedeConservarNuevo)
                        <p class="mt-1 text-xs text-amber-700">
                            <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>El estado <strong>Nuevo</strong> solo aplica a equipos de primer ingreso que no han tenido custodios ni reparaciones en taller.
                        </p>
                    @endif
                    <p class="mt-1 text-xs text-gray-500">
                        Si seleccionás <strong>Dañado / Defectuoso</strong>, el dispositivo pasará directamente al <strong>Archivo de Bajas</strong>.
                    </p>
                    @error('estado')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Uso común / Infraestructura -->
                <div class="rounded-lg bg-gray-50 p-3.5 border border-gray-200">
                    <label for="es_area_comun" class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="es_area_comun" id="es_area_comun" value="1"
                            @checked(old('_token') !== null ? old('es_area_comun') : ($dispositivo->disponibilidad === \App\Models\Dispositivo::DISPONIBILIDAD_NO_APLICA && \App\Models\Dispositivo::esEstadoAsignable((string) $dispositivo->estado)))
                            class="mt-0.5 rounded border-gray-300 text-brand-navy focus:ring-brand-gold">
                        <span class="text-sm">
                            <span class="font-medium text-brand-navy block">Equipo de área común o infraestructura (No aplica para asignación individual)</span>
                            <span class="text-xs text-gray-500">Marcá esta opción si es un equipo compartido o de red (ej. router, switch, servidor, impresora compartida o proyector de salón).</span>
                        </span>
                    </label>
                </div>
            @endif

            <!-- Bloque de Especificaciones Técnicas (Reactivo con Alpine) -->
            @php
                $espActual = $dispositivo->especificaciones ?? [];
                $resRam = \App\Models\Dispositivo::resolverOpcionSelect($espActual['ram'] ?? null, \App\Models\Dispositivo::OPCIONES_RAM);
                $resAlm = \App\Models\Dispositivo::resolverOpcionSelect($espActual['almacenamiento'] ?? null, \App\Models\Dispositivo::OPCIONES_ALMACENAMIENTO);
                $resSo = \App\Models\Dispositivo::resolverOpcionSelect($espActual['sistema_operativo'] ?? null, \App\Models\Dispositivo::OPCIONES_SO);
                $resPuertos = \App\Models\Dispositivo::resolverOpcionSelect($espActual['puertos_red'] ?? null, \App\Models\Dispositivo::OPCIONES_PUERTOS_RED);
                $resVel = \App\Models\Dispositivo::resolverOpcionSelect($espActual['velocidad_red'] ?? null, \App\Models\Dispositivo::OPCIONES_VELOCIDAD_RED);
                $resPoe = \App\Models\Dispositivo::resolverOpcionSelect($espActual['poe'] ?? null, \App\Models\Dispositivo::OPCIONES_POE);
                $resPantalla = \App\Models\Dispositivo::resolverOpcionSelect($espActual['tamano_pantalla'] ?? null, \App\Models\Dispositivo::OPCIONES_TAMANO_PANTALLA);
                $resTecImp = \App\Models\Dispositivo::resolverOpcionSelect($espActual['tecnologia_impresion'] ?? null, \App\Models\Dispositivo::OPCIONES_TECNOLOGIA_IMPRESION);
                $resConImp = \App\Models\Dispositivo::resolverOpcionSelect($espActual['conectividad_impresion'] ?? null, \App\Models\Dispositivo::OPCIONES_CONECTIVIDAD_IMPRESION);
                $resUps = \App\Models\Dispositivo::resolverOpcionSelect($espActual['capacidad_ups'] ?? null, \App\Models\Dispositivo::OPCIONES_CAPACIDAD_UPS);
            @endphp
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
                                value="{{ old('especificaciones.procesador', $espActual['procesador'] ?? '') }}"
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
                        <div x-data="{ valorRam: @js(old('especificaciones.ram', $resRam['select'])) }">
                            <label for="esp_ram" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Memoria RAM
                            </label>
                            <select name="especificaciones[ram]" id="esp_ram" x-model="valorRam"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar RAM --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_RAM as $ram)
                                    <option value="{{ $ram }}" @selected(old('especificaciones.ram', $resRam['select']) === $ram)>
                                        {{ $ram === 'Otro' ? 'Otro / Personalizado...' : $ram }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorRam === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[ram_otro]"
                                    value="{{ old('especificaciones.ram_otro', $resRam['otro']) }}"
                                    placeholder="Escribe la memoria RAM (ej. 48 GB DDR5)"
                                    class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Almacenamiento -->
                        <div x-data="{ valorAlm: @js(old('especificaciones.almacenamiento', $resAlm['select'])) }">
                            <label for="esp_almacenamiento" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Almacenamiento Principal
                            </label>
                            <select name="especificaciones[almacenamiento]" id="esp_almacenamiento" x-model="valorAlm"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar almacenamiento --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_ALMACENAMIENTO as $alm)
                                    <option value="{{ $alm }}" @selected(old('especificaciones.almacenamiento', $resAlm['select']) === $alm)>
                                        {{ $alm === 'Otro' ? 'Otro / Personalizado...' : $alm }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorAlm === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[almacenamiento_otro]"
                                    value="{{ old('especificaciones.almacenamiento_otro', $resAlm['otro']) }}"
                                    placeholder="Escribe el almacenamiento (ej. 2 TB NVMe + 1 TB HDD)"
                                    class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                        </div>

                        <!-- Sistema Operativo -->
                        <div x-data="{ valorSo: @js(old('especificaciones.sistema_operativo', $resSo['select'])) }">
                            <label for="esp_so" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Sistema Operativo
                            </label>
                            <select name="especificaciones[sistema_operativo]" id="esp_so" x-model="valorSo"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar S.O. --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_SO as $so)
                                    <option value="{{ $so }}" @selected(old('especificaciones.sistema_operativo', $resSo['select']) === $so)>
                                        {{ $so === 'Otro' ? 'Otro / Personalizado...' : $so }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorSo === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[sistema_operativo_otro]"
                                    value="{{ old('especificaciones.sistema_operativo_otro', $resSo['otro']) }}"
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
                        <div x-data="{ valorPuertos: @js(old('especificaciones.puertos_red', $resPuertos['select'])) }">
                            <label for="esp_puertos_red" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Cantidad de Puertos
                            </label>
                            <select name="especificaciones[puertos_red]" id="esp_puertos_red" x-model="valorPuertos"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar puertos --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_PUERTOS_RED as $ptos)
                                    <option value="{{ $ptos }}" @selected(old('especificaciones.puertos_red', $resPuertos['select']) === $ptos)>
                                        {{ $ptos === 'Otro' ? 'Otro / Personalizado...' : $ptos }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorPuertos === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[puertos_red_otro]"
                                    value="{{ old('especificaciones.puertos_red_otro', $resPuertos['otro']) }}"
                                    placeholder="Escribe la cantidad (ej. 52 Puertos + 4 SFP+)"
                                    class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                        </div>

                        <!-- Velocidad de Puertos -->
                        <div x-data="{ valorVelocidad: @js(old('especificaciones.velocidad_red', $resVel['select'])) }">
                            <label for="esp_velocidad_red" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Velocidad de Enlace / Puertos
                            </label>
                            <select name="especificaciones[velocidad_red]" id="esp_velocidad_red" x-model="valorVelocidad"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar velocidad --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_VELOCIDAD_RED as $vel)
                                    <option value="{{ $vel }}" @selected(old('especificaciones.velocidad_red', $resVel['select']) === $vel)>
                                        {{ $vel === 'Otro' ? 'Otro / Personalizado...' : $vel }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorVelocidad === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[velocidad_red_otro]"
                                    value="{{ old('especificaciones.velocidad_red_otro', $resVel['otro']) }}"
                                    placeholder="Escribe la velocidad (ej. 2.5 Gbps Multi-Gigabit)"
                                    class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- PoE -->
                        <div x-data="{ valorPoe: @js(old('especificaciones.poe', $resPoe['select'])) }">
                            <label for="esp_poe" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                                Soporte PoE (Power over Ethernet)
                            </label>
                            <select name="especificaciones[poe]" id="esp_poe" x-model="valorPoe"
                                class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                                <option value="">-- Seleccionar PoE --</option>
                                @foreach (\App\Models\Dispositivo::OPCIONES_POE as $poe)
                                    <option value="{{ $poe }}" @selected(old('especificaciones.poe', $resPoe['select']) === $poe)>
                                        {{ $poe === 'Otro' ? 'Otro / Personalizado...' : $poe }}
                                    </option>
                                @endforeach
                            </select>
                            <div x-show="valorPoe === 'Otro'" x-cloak class="mt-2">
                                <input type="text" name="especificaciones[poe_otro]"
                                    value="{{ old('especificaciones.poe_otro', $resPoe['otro']) }}"
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
                                    <option value="{{ $gst }}" @selected(old('especificaciones.gestion_red', $espActual['gestion_red'] ?? '') === $gst)>{{ $gst }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Grupo Pantallas (Monitor, Pantalla interactiva) -->
                <div x-show="esPantalla" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div x-data="{ valorPantalla: @js(old('especificaciones.tamano_pantalla', $resPantalla['select'])) }">
                        <label for="esp_tamano_pantalla" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Tamaño de Pantalla
                        </label>
                        <select name="especificaciones[tamano_pantalla]" id="esp_tamano_pantalla" x-model="valorPantalla"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">-- Seleccionar tamaño --</option>
                            @foreach (\App\Models\Dispositivo::OPCIONES_TAMANO_PANTALLA as $tam)
                                <option value="{{ $tam }}" @selected(old('especificaciones.tamano_pantalla', $resPantalla['select']) === $tam)>
                                    {{ $tam === 'Otro' ? 'Otro / Personalizado...' : $tam }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="valorPantalla === 'Otro'" x-cloak class="mt-2">
                            <input type="text" name="especificaciones[tamano_pantalla_otro]"
                                value="{{ old('especificaciones.tamano_pantalla_otro', $resPantalla['otro']) }}"
                                placeholder="Escribe el tamaño (ej. 75 pulgadas)"
                                class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>

                    <div>
                        <label for="esp_conectores_pantalla" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Conectores / Puertos de Video
                        </label>
                        <input type="text" name="especificaciones[conectores_pantalla]" id="esp_conectores_pantalla"
                            value="{{ old('especificaciones.conectores_pantalla', $espActual['conectores_pantalla'] ?? '') }}"
                            placeholder="Ej. HDMI, VGA, DisplayPort"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                    </div>
                </div>

                <!-- Grupo Impresión (Impresora, Escáner) -->
                <div x-show="esImpresion" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div x-data="{ valorTecImp: @js(old('especificaciones.tecnologia_impresion', $resTecImp['select'])) }">
                        <label for="esp_tecnologia_impresion" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Tecnología de Impresión
                        </label>
                        <select name="especificaciones[tecnologia_impresion]" id="esp_tecnologia_impresion" x-model="valorTecImp"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">-- Seleccionar tecnología --</option>
                            @foreach (\App\Models\Dispositivo::OPCIONES_TECNOLOGIA_IMPRESION as $tec)
                                <option value="{{ $tec }}" @selected(old('especificaciones.tecnologia_impresion', $resTecImp['select']) === $tec)>
                                    {{ $tec === 'Otro' ? 'Otro / Personalizado...' : $tec }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="valorTecImp === 'Otro'" x-cloak class="mt-2">
                            <input type="text" name="especificaciones[tecnologia_impresion_otro]"
                                value="{{ old('especificaciones.tecnologia_impresion_otro', $resTecImp['otro']) }}"
                                placeholder="Escribe la tecnología (ej. Matriz de punto)"
                                class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>

                    <div x-data="{ valorConImp: @js(old('especificaciones.conectividad_impresion', $resConImp['select'])) }">
                        <label for="esp_conectividad_impresion" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Conectividad
                        </label>
                        <select name="especificaciones[conectividad_impresion]" id="esp_conectividad_impresion" x-model="valorConImp"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">-- Seleccionar conectividad --</option>
                            @foreach (\App\Models\Dispositivo::OPCIONES_CONECTIVIDAD_IMPRESION as $con)
                                <option value="{{ $con }}" @selected(old('especificaciones.conectividad_impresion', $resConImp['select']) === $con)>
                                    {{ $con === 'Otro' ? 'Otro / Personalizado...' : $con }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="valorConImp === 'Otro'" x-cloak class="mt-2">
                            <input type="text" name="especificaciones[conectividad_impresion_otro]"
                                value="{{ old('especificaciones.conectividad_impresion_otro', $resConImp['otro']) }}"
                                placeholder="Escribe la conectividad (ej. Bluetooth + USB)"
                                class="block w-full rounded-lg border-amber-300 bg-amber-50/40 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>
                </div>

                <!-- Grupo UPS -->
                <div x-show="esUps" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div x-data="{ valorUps: @js(old('especificaciones.capacidad_ups', $resUps['select'])) }">
                        <label for="esp_capacidad_ups" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Capacidad de Carga
                        </label>
                        <select name="especificaciones[capacidad_ups]" id="esp_capacidad_ups" x-model="valorUps"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">-- Seleccionar capacidad --</option>
                            @foreach (\App\Models\Dispositivo::OPCIONES_CAPACIDAD_UPS as $cap)
                                <option value="{{ $cap }}" @selected(old('especificaciones.capacidad_ups', $resUps['select']) === $cap)>
                                    {{ $cap === 'Otro' ? 'Otro / Personalizado...' : $cap }}
                                </option>
                            @endforeach
                        </select>
                        <div x-show="valorUps === 'Otro'" x-cloak class="mt-2">
                            <input type="text" name="especificaciones[capacidad_ups_otro]"
                                value="{{ old('especificaciones.capacidad_ups_otro', $resUps['otro']) }}"
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
                        value="{{ old('especificaciones.notas_tecnicas', $espActual['notas_tecnicas'] ?? '') }}"
                        placeholder="Ej. Teclado retroiluminado, incluye adaptador USB-C o dirección MAC"
                        class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                </div>
            </div>

            <!-- Fecha de Registro -->
            <div>
                <label for="fecha_registro" class="block text-sm font-medium text-brand-navy">Fecha de Registro</label>
                <input type="date" name="fecha_registro" id="fecha_registro"
                    value="{{ old('fecha_registro', $dispositivo->fecha_registro?->format('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('fecha_registro') border-red-500 @enderror">
                @error('fecha_registro')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-gray-100 sticky bottom-0 bg-white/95 backdrop-blur-sm py-3 -mx-6 px-6 z-10 shadow-xs">
                <a href="{{ $cancelarUrl }}"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-600 hover:text-brand-navy transition w-full sm:w-auto text-center">
                    ← Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 bg-brand-gold text-brand-navy px-5 py-2.5 rounded-lg hover:bg-brand-gold-400 font-semibold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition w-full sm:w-auto">
                    Actualizar Dispositivo
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
