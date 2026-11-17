<x-app-layout>
    <div class="py-6 max-w-4xl mx-auto space-y-6">
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-navy">Mi Perfil</h1>
                <p class="mt-1 text-sm text-gray-500">
                    @if (auth()->user()->esSuperAdmin())
                        Gestiona la información de tu cuenta institucional y actualiza tus credenciales de acceso
                    @else
                        Actualiza tu contraseña de acceso institucional
                    @endif
                </p>
            </div>
            <div>
                @if (auth()->user()->esSuperAdmin())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-900 border border-purple-200">
                        <i class="fas fa-crown text-[11px] text-amber-500" aria-hidden="true"></i>
                        Superadministrador
                    </span>
                @elseif (auth()->user()->esAdministrador())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-brand-gold-100 text-brand-gold-800 border border-brand-gold-300">
                        <i class="fas fa-shield-alt text-[11px]" aria-hidden="true"></i>
                        Administrador
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="fas fa-eye text-[11px]" aria-hidden="true"></i>
                        Solo Lectura
                    </span>
                @endif
            </div>
        </div>

        <!-- Alertas de estado generales -->
        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 flex items-center gap-3" role="alert">
                <i class="fas fa-exclamation-circle text-red-600 text-base shrink-0" aria-hidden="true"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Tarjeta 1: Información Personal -->
        <div id="seccion-info" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 sm:p-8">
                @if (session('status') === 'profile-updated')
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 flex items-center gap-3" role="alert">
                        <i class="fas fa-check-circle text-emerald-600 text-base shrink-0" aria-hidden="true"></i>
                        <span class="font-medium">Tu información de perfil ha sido actualizada exitosamente.</span>
                    </div>
                @endif

                <div class="mb-5 pb-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-brand-navy-50 text-brand-navy flex items-center justify-center shrink-0">
                        <i class="fas fa-user text-base" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-brand-navy">Información de la Cuenta</h2>
                        <p class="text-xs text-gray-500">
                            @if ($user->esSuperAdmin())
                                Actualiza tu nombre completo y dirección de correo electrónico institucional
                            @else
                                Datos institucionales registrados en INVEC (protegidos por auditoría)
                            @endif
                        </p>
                    </div>
                </div>

                @if ($user->esSuperAdmin())
                    <!-- Formulario de edición para Superadministrador -->
                    <div class="mb-5 rounded-xl border border-purple-200 bg-purple-50/60 p-3.5 text-xs text-purple-900 flex items-start gap-2.5">
                        <i class="fas fa-crown text-amber-500 text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                        <span class="leading-relaxed">
                            Como <strong>Superadministrador</strong>, puedes modificar tu nombre y correo en este perfil. Para cambiar el nombre de otros usuarios, hazlo desde el <a href="{{ route('usuarios.index') }}" class="underline font-semibold hover:text-purple-700">panel de Gestión de Usuarios</a>.
                        </span>
                    </div>

                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Nombre Completo -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-brand-navy">Nombre Completo</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Correo Electrónico -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-brand-navy">Correo Electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Datos informativos del sistema -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3 border-t border-gray-100">
                            <div>
                                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Rol de Permisos</span>
                                <p class="text-sm font-semibold text-brand-navy mt-1">
                                    {{ $user->rol_nombre }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">Control total del sistema institucional.</p>
                            </div>

                            <div>
                                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha de Creación</span>
                                <p class="text-sm font-semibold text-brand-navy mt-1">
                                    {{ $user->created_at?->format('d/m/Y h:i A') ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">Miembro institucional registrado en INVEC.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-brand-gold text-brand-navy text-sm font-semibold shadow-sm hover:bg-brand-gold-400 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                                <i class="fas fa-save text-xs" aria-hidden="true"></i>
                                Guardar Información
                            </button>
                        </div>
                    </form>
                @else
                    <!-- Vista de solo lectura para usuarios regulares (Administrador y Solo Lectura) -->
                    <div class="mb-5 rounded-xl border border-blue-200 bg-blue-50/70 p-4 text-xs text-blue-900 flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
                            <i class="fas fa-shield-alt text-sm" aria-hidden="true"></i>
                        </div>
                        <div class="space-y-1">
                            <p class="font-semibold text-brand-navy text-sm">Información de identidad protegida por auditoría</p>
                            <p class="text-gray-600 leading-relaxed">
                                Por motivos de trazabilidad y orden en los registros de auditoría del sistema (asignaciones, devoluciones y bajas de inventario), los usuarios no pueden editar su nombre ni correo. Únicamente un <span class="font-semibold text-brand-navy">Superadministrador</span> tiene autorización para modificar datos de identidad.
                            </p>
                            <p class="text-gray-500">
                                Desde este perfil únicamente puedes actualizar tu <a href="#seccion-password" class="text-brand-navy font-semibold underline hover:text-brand-gold transition">contraseña de acceso</a>. Si requieres corregir tu nombre, solicita la modificación a un Superadministrador.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <!-- Nombre Completo (Solo Lectura) -->
                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-semibold text-brand-navy">Nombre Completo</label>
                                    <span class="text-xs text-gray-400 font-normal flex items-center gap-1">
                                        <i class="fas fa-lock text-[10px]" aria-hidden="true"></i> Solo lectura
                                    </span>
                                </div>
                                <div class="relative mt-1.5">
                                    <input type="text" value="{{ $user->name }}" readonly disabled
                                        class="block w-full rounded-lg border-gray-200 bg-gray-50 text-gray-700 shadow-sm cursor-not-allowed select-none pl-3.5 pr-10 focus:ring-0 focus:border-gray-200 font-medium">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-lock text-xs" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Este nombre se refleja en todas tus acciones y firmas de auditoría.</p>
                            </div>

                            <!-- Correo Electrónico (Solo Lectura) -->
                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-semibold text-brand-navy">Correo Electrónico</label>
                                    <span class="text-xs text-gray-400 font-normal flex items-center gap-1">
                                        <i class="fas fa-lock text-[10px]" aria-hidden="true"></i> Solo lectura
                                    </span>
                                </div>
                                <div class="relative mt-1.5">
                                    <input type="email" value="{{ $user->email }}" readonly disabled
                                        class="block w-full rounded-lg border-gray-200 bg-gray-50 text-gray-700 shadow-sm cursor-not-allowed select-none pl-3.5 pr-10 focus:ring-0 focus:border-gray-200 font-medium">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-lock text-xs" aria-hidden="true"></i>
                                    </div>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Dirección vinculada para inicio de sesión institucional.</p>
                            </div>
                        </div>

                        <!-- Datos informativos del sistema -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-3 border-t border-gray-100">
                            <div>
                                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Rol de Permisos</span>
                                <p class="text-sm font-semibold text-brand-navy mt-1">
                                    {{ $user->rol_nombre }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">El rol solo puede ser modificado por un Superadministrador.</p>
                            </div>

                            <div>
                                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha de Creación</span>
                                <p class="text-sm font-semibold text-brand-navy mt-1">
                                    {{ $user->created_at?->format('d/m/Y h:i A') ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">Miembro institucional registrado en INVEC.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tarjeta 2: Actualizar Contraseña -->
        <div x-data="passwordUpdateHandler()" id="seccion-password" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
            <div class="p-6 sm:p-8">
                <div class="mb-5 pb-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-brand-gold-50 text-brand-gold-700 flex items-center justify-center shrink-0">
                        <i class="fas fa-lock text-base" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-brand-navy">Seguridad y Contraseña</h2>
                        <p class="text-xs text-gray-500">Mantén protegida tu cuenta actualizando tu contraseña periódicamente</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('password.update') }}" @submit.prevent="submitForm($event)" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Contraseña Actual -->
                    <div>
                        <label for="current_password" class="block text-sm font-semibold text-brand-navy">Contraseña Actual</label>
                        <div class="relative mt-1.5 w-full sm:w-2/3">
                            <input :type="showCurrentPass ? 'text' : 'password'"
                                name="current_password"
                                id="current_password"
                                required
                                autocomplete="current-password"
                                placeholder="Ingresa tu contraseña actual"
                                :class="errors.current_password ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300'"
                                class="block w-full rounded-lg shadow-sm pr-10 focus:border-brand-gold focus:ring-brand-gold @error('current_password', 'updatePassword') border-red-500 @enderror">
                            <button type="button"
                                @click="showCurrentPass = !showCurrentPass"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas text-xs" :class="showCurrentPass ? 'fa-eye' : 'fa-eye-slash'" aria-hidden="true"></i>
                            </button>
                        </div>
                        <template x-if="errors.current_password">
                            <p class="text-red-600 text-xs mt-1 font-medium" x-text="errors.current_password[0]"></p>
                        </template>
                        @error('current_password', 'updatePassword')
                            <p x-show="!errors.current_password" class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Requisitos de Contraseña Institucional -->
                    <div class="rounded-lg bg-gray-50 border border-gray-200 p-3.5 text-xs text-gray-700">
                        <p class="font-semibold text-brand-navy flex items-center gap-1.5 mb-2">
                            <i class="fas fa-shield-alt text-brand-gold text-sm" aria-hidden="true"></i>
                            Requisitos de la nueva contraseña:
                        </p>
                        <ul class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                            <li class="flex items-center gap-1.5 transition-colors duration-150"
                                :class="newPassword.length === 0 ? 'text-gray-500' : (hasMinLength ? 'text-emerald-700 font-medium' : 'text-rose-600 font-medium')">
                                <i class="fas text-xs shrink-0"
                                   :class="newPassword.length === 0 ? 'fa-circle text-gray-300' : (hasMinLength ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500')"></i>
                                <span>Mínimo 8 caracteres (máx. 72)</span>
                            </li>
                            <li class="flex items-center gap-1.5 transition-colors duration-150"
                                :class="newPassword.length === 0 ? 'text-gray-500' : (hasLetter ? 'text-emerald-700 font-medium' : 'text-rose-600 font-medium')">
                                <i class="fas text-xs shrink-0"
                                   :class="newPassword.length === 0 ? 'fa-circle text-gray-300' : (hasLetter ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500')"></i>
                                <span>Al menos una letra (a-z, A-Z)</span>
                            </li>
                            <li class="flex items-center gap-1.5 transition-colors duration-150"
                                :class="newPassword.length === 0 ? 'text-gray-500' : (hasNumber ? 'text-emerald-700 font-medium' : 'text-rose-600 font-medium')">
                                <i class="fas text-xs shrink-0"
                                   :class="newPassword.length === 0 ? 'fa-circle text-gray-300' : (hasNumber ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500')"></i>
                                <span>Al menos un número (0-9)</span>
                            </li>
                        </ul>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2 border-t border-gray-100">
                        <!-- Nueva Contraseña -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-brand-navy">Nueva Contraseña</label>
                            <div class="relative mt-1.5">
                                <input :type="showNewPass ? 'text' : 'password'"
                                    name="password"
                                    id="password"
                                    required
                                    x-model="newPassword"
                                    @copy.prevent
                                    @cut.prevent
                                    autocomplete="new-password"
                                    placeholder="Mínimo 8 caracteres (letras y números)"
                                    :class="errors.password ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-300'"
                                    class="block w-full rounded-lg shadow-sm pr-10 focus:border-brand-gold focus:ring-brand-gold @error('password', 'updatePassword') border-red-500 @enderror">
                                <button type="button"
                                    @click="showNewPass = !showNewPass"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i class="fas text-xs" :class="showNewPass ? 'fa-eye' : 'fa-eye-slash'" aria-hidden="true"></i>
                                </button>
                            </div>
                            <template x-if="errors.password">
                                <p class="text-red-600 text-xs mt-1 font-medium" x-text="errors.password[0]"></p>
                            </template>
                            @error('password', 'updatePassword')
                                <p x-show="!errors.password" class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirmar Nueva Contraseña -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-brand-navy">Confirmar Nueva Contraseña</label>
                            <div class="relative mt-1.5">
                                <input :type="showConfirmPass ? 'text' : 'password'"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    required
                                    x-model="confirmPassword"
                                    @paste.prevent
                                    autocomplete="new-password"
                                    placeholder="Repite la nueva contraseña a mano"
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

                    <div class="rounded-lg bg-blue-50 border border-blue-200 p-3.5 text-xs text-blue-800 flex items-start gap-2.5">
                        <i class="fas fa-info-circle text-blue-600 text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                        <span>Por tu seguridad, al actualizar tu contraseña se cerrará tu sesión automáticamente y deberás volver a iniciar sesión con tu nueva credencial.</span>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-gray-100">
                        <button type="submit"
                            :disabled="loading"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-brand-navy text-white text-sm font-semibold shadow-sm hover:bg-brand-navy-800 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition disabled:opacity-50 cursor-pointer">
                            <i class="fas text-xs" :class="loading ? 'fa-spinner fa-spin' : 'fa-key'" aria-hidden="true"></i>
                            <span x-text="loading ? 'Actualizando...' : 'Actualizar Contraseña'"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Modal de confirmación y redirección automática -->
            <div x-show="showModal"
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                 role="dialog"
                 aria-modal="true">

                <div x-show="showModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-90 translate-y-4"
                     class="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-sm w-full p-6 text-center">

                    <!-- Icono animado -->
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-emerald-100 text-emerald-600 mb-4 ring-8 ring-emerald-50">
                        <i class="fas fa-check text-2xl" aria-hidden="true"></i>
                    </div>

                    <h3 class="text-xl font-bold text-brand-navy">¡Contraseña Actualizada!</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Tu contraseña se ha cambiado exitosamente. Por motivos de seguridad institucional, cerraremos tu sesión.
                    </p>

                    <!-- Contador regresivo -->
                    <div class="my-5 p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center gap-2 text-sm text-slate-700">
                        <i class="fas fa-circle-notch fa-spin text-brand-gold"></i>
                        <span>Redirigiendo al inicio de sesión en</span>
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-brand-navy text-white text-xs font-bold" x-text="countdown">3</span>
                        <span>segundos...</span>
                    </div>

                    <button type="button"
                            @click="goToLogin()"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-brand-navy text-white text-sm font-semibold shadow-sm hover:bg-brand-navy-800 transition">
                        <span>Ir al Login ahora</span>
                        <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function passwordUpdateHandler() {
            return {
                loading: false,
                errors: {},
                showModal: false,
                countdown: 3,
                timer: null,
                redirectUrl: '{{ route('login') }}',
                showCurrentPass: false,
                showNewPass: false,
                showConfirmPass: false,
                newPassword: '',
                confirmPassword: '',

                get hasMinLength() {
                    return this.newPassword.length >= 8 && this.newPassword.length <= 72;
                },
                get hasLetter() {
                    return /[a-zA-Z]/.test(this.newPassword);
                },
                get hasNumber() {
                    return /[0-9]/.test(this.newPassword);
                },
                get passwordsMatch() {
                    return this.confirmPassword.length > 0 && this.confirmPassword === this.newPassword;
                },
                get passwordsMismatch() {
                    return this.confirmPassword.length > 0 && this.confirmPassword !== this.newPassword;
                },

                async submitForm(event) {
                    this.loading = true;
                    this.errors = {};
                    const form = event.target;
                    const formData = new FormData(form);

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]')?.value
                            }
                        });

                        if (response.status === 422) {
                            const data = await response.json();
                            this.errors = data.errors || {};
                            this.loading = false;
                            return;
                        }

                        if (!response.ok) {
                            throw new Error('Error al actualizar la contraseña');
                        }

                        const data = await response.json();
                        this.redirectUrl = data.redirect || '{{ route('login') }}';
                        this.loading = false;
                        this.showModal = true;
                        this.startCountdown();
                    } catch (err) {
                        this.loading = false;
                        alert('Ocurrió un error inesperado al procesar la solicitud. Por favor intenta de nuevo.');
                    }
                },

                startCountdown() {
                    this.countdown = 3;
                    if (this.timer) clearInterval(this.timer);
                    this.timer = setInterval(() => {
                        this.countdown--;
                        if (this.countdown <= 0) {
                            clearInterval(this.timer);
                            this.goToLogin();
                        }
                    }, 1000);
                },

                goToLogin() {
                    if (this.timer) clearInterval(this.timer);
                    window.location.href = this.redirectUrl;
                }
            };
        }
    </script>
</x-app-layout>
