<x-app-layout>
    <div class="py-6 max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('usuarios.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-navy hover:text-brand-gold transition mb-2">
                <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i> Volver a Usuarios
            </a>
            <h1 class="text-2xl font-bold text-brand-navy">Crear Nuevo Usuario</h1>
            <p class="mt-1 text-sm text-gray-500">Registra un colaborador o personal autorizado para acceder a INVEC</p>
        </div>

        <form method="POST" action="{{ route('usuarios.store') }}"
            x-data="{ rol: '{{ old('rol', '') }}' }"
            class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-gray-100 space-y-6">
            @csrf

            <!-- Nombre Completo -->
            <div>
                <label for="name" class="block text-sm font-semibold text-brand-navy">Nombre Completo</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                    placeholder="Ej. Ing. Carlos Mendoza"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Correo Electrónico -->
            <div>
                <label for="email" class="block text-sm font-semibold text-brand-navy">Correo Electrónico</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    placeholder="Ej. carlos.mendoza@ccisur.hn"
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('email') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">Este correo será su identificador único para iniciar sesión.</p>
                @error('email')
                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rol de Acceso -->
            <div class="space-y-3">
                <div>
                    <label for="rol" class="block text-sm font-semibold text-brand-navy">Rol en el Sistema</label>
                    <select name="rol" id="rol" x-model="rol" required
                        class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('rol') border-red-500 @enderror">
                        @if (count($rolesDisponibles) > 1)
                            <option value="" disabled @selected(!old('rol'))>-- Seleccionar rol de acceso --</option>
                        @endif
                        @foreach ($rolesDisponibles as $valor => $etiqueta)
                            <option value="{{ $valor }}" @selected(old('rol', count($rolesDisponibles) === 1 ? $valor : '') === $valor)>
                                {{ $etiqueta }}
                                @if ($valor === \App\Models\User::ROL_SUPERADMIN)
                                    — Control total del sistema, seguridad y gestión de usuarios
                                @elseif ($valor === \App\Models\User::ROL_ADMINISTRADOR)
                                    — Control total de inventario (dispositivos, muebles, actas y bajas)
                                @elseif ($valor === \App\Models\User::ROL_LECTURA)
                                    — Consultas de inventario, visualización y exportación de reportes / actas
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('rol')
                        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmación de Autorización Superadministrador (Modo Sudo) -->
                <div x-show="rol === '{{ \App\Models\User::ROL_SUPERADMIN }}'" 
                     x-cloak
                     x-transition
                     class="p-4 rounded-xl bg-purple-50 border border-purple-200 space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                            <i class="fas fa-crown text-sm text-amber-500" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-purple-900">Autorización Requerida (Rol Superadministrador)</h3>
                            <p class="text-xs text-purple-700 mt-0.5 leading-relaxed">
                                Estás a punto de otorgar privilegios de <strong>Superadministrador</strong>. Por seguridad institucional, confirma tu contraseña actual de Superadministrador para autorizar este nombramiento.
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="superadmin_password" class="block text-xs font-semibold text-purple-900 uppercase tracking-wider">
                            Tu Contraseña Actual de Superadministrador <span class="text-red-500">*</span>
                        </label>
                        <input type="password" 
                               name="superadmin_password" 
                               id="superadmin_password" 
                               autocomplete="current-password"
                               placeholder="Ingresa tu contraseña para autorizar"
                               class="mt-1 block w-full rounded-lg text-sm border-purple-300 shadow-sm focus:border-purple-600 focus:ring-purple-600 @error('superadmin_password') border-red-500 ring-1 ring-red-500 @enderror">
                        @error('superadmin_password')
                            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contraseña Inicial Provisional -->
            <div x-data="{
                showPass: false,
                showConfirmPass: false,
                pass: '',
                confirmPass: '',
                get hasMin() { return this.pass.length >= 8 && this.pass.length <= 72; },
                get hasLetter() { return /[a-zA-Z]/.test(this.pass); },
                get hasNum() { return /[0-9]/.test(this.pass); },
                get passwordsMatch() { return this.confirmPass.length > 0 && this.confirmPass === this.pass; },
                get passwordsMismatch() { return this.confirmPass.length > 0 && this.confirmPass !== this.pass; }
            }" class="space-y-4 pt-2 border-t border-gray-100">
                <div class="rounded-lg bg-brand-gold-50 border border-brand-gold-200 p-3.5 text-xs text-brand-gold-900 flex items-start gap-2.5">
                    <i class="fas fa-info-circle text-brand-gold-700 text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span><strong>Contraseña Inicial Provisional:</strong> Al iniciar sesión por primera vez con esta clave, el sistema le exigirá automáticamente establecer su contraseña personal definitiva antes de acceder.</span>
                </div>

                <!-- Requisitos de Contraseña Institucional -->
                <div class="rounded-lg bg-gray-50 border border-gray-200 p-3 text-xs text-gray-700">
                    <p class="font-semibold text-brand-navy flex items-center gap-1.5 mb-1.5">
                        <i class="fas fa-shield-alt text-brand-gold text-xs" aria-hidden="true"></i>
                        Requisitos de la contraseña:
                    </p>
                    <ul class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                        <li class="flex items-center gap-1.5 transition-colors duration-150"
                            :class="pass.length === 0 ? 'text-gray-500' : (hasMin ? 'text-emerald-700 font-medium' : 'text-rose-600 font-medium')">
                            <i class="fas text-xs shrink-0"
                               :class="pass.length === 0 ? 'fa-circle text-gray-300' : (hasMin ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500')"></i>
                            <span>Mínimo 8 caracteres (máx. 72)</span>
                        </li>
                        <li class="flex items-center gap-1.5 transition-colors duration-150"
                            :class="pass.length === 0 ? 'text-gray-500' : (hasLetter ? 'text-emerald-700 font-medium' : 'text-rose-600 font-medium')">
                            <i class="fas text-xs shrink-0"
                               :class="pass.length === 0 ? 'fa-circle text-gray-300' : (hasLetter ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500')"></i>
                            <span>Al menos una letra (a-z)</span>
                        </li>
                        <li class="flex items-center gap-1.5 transition-colors duration-150"
                            :class="pass.length === 0 ? 'text-gray-500' : (hasNum ? 'text-emerald-700 font-medium' : 'text-rose-600 font-medium')">
                            <i class="fas text-xs shrink-0"
                               :class="pass.length === 0 ? 'fa-circle text-gray-300' : (hasNum ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500')"></i>
                            <span>Al menos un número (0-9)</span>
                        </li>
                    </ul>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-brand-navy">Contraseña Provisional</label>
                        <div class="relative mt-1.5">
                            <input :type="showPass ? 'text' : 'password'"
                                name="password"
                                id="password"
                                required
                                x-model="pass"
                                @copy.prevent
                                @cut.prevent
                                autocomplete="new-password"
                                placeholder="Mínimo 8 caracteres (letras y números)"
                                class="block w-full rounded-lg border-gray-300 shadow-sm pr-10 focus:border-brand-gold focus:ring-brand-gold @error('password') border-red-500 @enderror">
                            <button type="button"
                                @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas text-xs" :class="showPass ? 'fa-eye' : 'fa-eye-slash'" aria-hidden="true"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-brand-navy">Confirmar Contraseña</label>
                        <div class="relative mt-1.5">
                            <input :type="showConfirmPass ? 'text' : 'password'"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                x-model="confirmPass"
                                @paste.prevent
                                autocomplete="new-password"
                                placeholder="Repita la contraseña a mano"
                                class="block w-full rounded-lg border-gray-300 shadow-sm pr-10 focus:border-brand-gold focus:ring-brand-gold">
                            <button type="button"
                                @click="showConfirmPass = !showConfirmPass"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas text-xs" :class="showConfirmPass ? 'fa-eye' : 'fa-eye-slash'" aria-hidden="true"></i>
                            </button>
                        </div>
                        <template x-if="passwordsMatch">
                            <p class="text-emerald-600 text-xs mt-1 font-medium flex items-center gap-1">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                                <span>Las contraseñas coinciden</span>
                            </p>
                        </template>
                        <template x-if="passwordsMismatch">
                            <p class="text-rose-600 text-xs mt-1 font-medium flex items-center gap-1">
                                <i class="fas fa-times-circle text-rose-500"></i>
                                <span>Las contraseñas no coinciden</span>
                            </p>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Estado de la cuenta -->
            <div class="pt-2 border-t border-gray-100">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" value="1" @checked(old('activo', '1') === '1')
                        class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                    <span class="text-sm font-semibold text-brand-navy">Cuenta Habilitada (Activa)</span>
                </label>
                <p class="text-xs text-gray-500 pl-6">Si se desmarca, el usuario no podrá iniciar sesión en la plataforma.</p>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('usuarios.index') }}"
                    class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-brand-gold text-brand-navy text-sm font-semibold shadow-sm hover:bg-brand-gold-400 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                    <i class="fas fa-save text-xs" aria-hidden="true"></i>
                    Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
