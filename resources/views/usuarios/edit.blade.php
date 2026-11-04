<x-app-layout>
    @php
        $esUltimoSuperadmin = $usuario->esUltimoSuperadminActivo();
    @endphp

    <div class="py-6 max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('usuarios.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-navy hover:text-brand-gold transition mb-2">
                <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i> Volver a Usuarios
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-brand-navy">Editar Usuario</h1>
                    <p class="mt-1 text-sm text-gray-500">Modifica los datos generales y el rol asignado a este usuario</p>
                </div>
                <div>
                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $usuario->esSuperAdmin() ? 'bg-purple-100 text-purple-900 border border-purple-200' : ($usuario->esAdministrador() ? 'bg-brand-gold-100 text-brand-gold-800 border border-brand-gold-300' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                        @if ($usuario->esSuperAdmin())
                            <i class="fas fa-crown text-[10px] text-amber-500 mr-1" aria-hidden="true"></i>
                        @endif
                        {{ $usuario->rol_nombre }}
                    </span>
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-600 text-base" aria-hidden="true"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if ($esUltimoSuperadmin)
            <div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900 flex items-start gap-2.5">
                <i class="fas fa-shield-alt text-amber-600 text-sm mt-0.5 shrink-0" aria-hidden="true"></i>
                <div>
                    <p class="font-bold">Último Superadministrador Activo</p>
                    <p class="mt-0.5">Este usuario es el único superadministrador activo del sistema. Por seguridad, no es posible degradar su rol ni deshabilitar su cuenta para prevenir que el sistema quede sin administradores.</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('usuarios.update', $usuario) }}"
            x-data="{ 
                rol: '{{ old('rol', $usuario->rol) }}',
                esSuperadminOriginal: {{ $usuario->esSuperAdmin() ? 'true' : 'false' }}
            }"
            class="bg-white p-6 sm:p-8 rounded-xl shadow-sm border border-gray-100 space-y-6">
            @csrf
            @method('PUT')

            <!-- Nombre Completo -->
            <div>
                <label for="name" class="block text-sm font-semibold text-brand-navy">Nombre Completo</label>
                <input type="text" name="name" id="name" value="{{ old('name', $usuario->name) }}" required
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Correo Electrónico -->
            <div>
                <label for="email" class="block text-sm font-semibold text-brand-navy">Correo Electrónico</label>
                <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}" required
                    class="mt-1.5 block w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold @error('email') border-red-500 @enderror">
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
                        @foreach ($rolesDisponibles as $valor => $etiqueta)
                            <option value="{{ $valor }}" 
                                @selected(old('rol', $usuario->rol) === $valor)
                                @disabled($esUltimoSuperadmin && $valor !== \App\Models\User::ROL_SUPERADMIN)>
                                {{ $etiqueta }}
                                @if ($valor === \App\Models\User::ROL_SUPERADMIN)
                                    — Control total del sistema, seguridad y gestión de usuarios
                                @elseif ($valor === \App\Models\User::ROL_ADMINISTRADOR)
                                    — Control total de inventario (dispositivos, muebles, actas y bajas)
                                @elseif ($valor === \App\Models\User::ROL_LECTURA)
                                    — Consultas de inventario, visualización y exportación de reportes / actas
                                @endif
                                @if ($esUltimoSuperadmin && $valor !== \App\Models\User::ROL_SUPERADMIN)
                                    (Bloqueado: debe existir al menos un superadmin activo)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('rol')
                        <p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmación de Autorización Superadministrador (Modo Sudo) -->
                <div x-show="rol === '{{ \App\Models\User::ROL_SUPERADMIN }}' || esSuperadminOriginal" 
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
                                @if ($usuario->esSuperAdmin())
                                    Estás gestionando la cuenta de otro <strong>Superadministrador</strong>. Por seguridad institucional, confirma tu contraseña actual para autorizar cualquier modificación.
                                @else
                                    Estás a punto de ascender este usuario a <strong>Superadministrador</strong>. Por seguridad institucional, confirma tu contraseña actual de Superadministrador para autorizar esta promoción.
                                @endif
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

            <!-- Estado de la cuenta -->
            <div class="pt-4 border-t border-gray-100">
                <label class="inline-flex items-center gap-2 {{ $esUltimoSuperadmin ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer' }}">
                    @if ($esUltimoSuperadmin)
                        <input type="hidden" name="activo" value="1">
                        <input type="checkbox" checked disabled class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                    @else
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" value="1"
                            @checked(old('activo', $usuario->activo ? '1' : '0') === '1')
                            class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                    @endif
                    <span class="text-sm font-semibold text-brand-navy">Cuenta Habilitada (Activa)</span>
                </label>
                @if ($esUltimoSuperadmin)
                    <p class="text-xs text-amber-700 pl-6 mt-1 font-medium">No se puede deshabilitar al único superadministrador activo.</p>
                @else
                    <p class="text-xs text-gray-500 pl-6">Si se desmarca, el usuario no podrá iniciar sesión en la plataforma y sus sesiones actuales serán revocadas.</p>
                @endif
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
                    Guardar Cambios
                </button>
            </div>
        </form>

        <!-- Información de Auditoría y Trazabilidad -->
        <div class="mt-6 bg-white border border-gray-100 rounded-xl p-5 shadow-sm">
            <h3 class="text-xs font-bold text-brand-navy uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fas fa-history text-brand-gold text-sm" aria-hidden="true"></i>
                Auditoría y Trazabilidad de la Cuenta
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50/70 border border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                        <i class="fas fa-user-plus text-xs" aria-hidden="true"></i>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-700">Registrado por:</span>
                        <span class="text-sm font-medium text-brand-navy">
                            {{ $usuario->creador?->name ?? 'Sistema / Registro Inicial' }}
                        </span>
                        <span class="block text-[11px] text-gray-400 mt-0.5">
                            {{ $usuario->created_at?->format('d/m/Y h:i A') ?? 'Fecha desconocida' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50/70 border border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                        <i class="fas fa-user-edit text-xs" aria-hidden="true"></i>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-gray-700">Última edición por:</span>
                        <span class="text-sm font-medium text-brand-navy">
                            @if ($usuario->editor)
                                {{ $usuario->editor->name }}
                                @if ($usuario->editor->id === $usuario->id)
                                    <span class="text-[10px] text-gray-500 font-normal">(El mismo usuario)</span>
                                @endif
                            @else
                                Sin modificaciones
                            @endif
                        </span>
                        <span class="block text-[11px] text-gray-400 mt-0.5">
                            {{ $usuario->updated_at?->format('d/m/Y h:i A') ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>