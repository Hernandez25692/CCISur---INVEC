<x-app-layout>
    <div class="py-4 space-y-6">
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-brand-navy">Gestión de Usuarios</h1>
                <p class="mt-1 text-sm text-gray-500">Control de accesos, roles institucionales y estado de cuentas en INVEC</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('usuarios.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-brand-gold text-brand-navy text-sm font-semibold shadow-sm hover:bg-brand-gold-400 focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2 transition">
                    <i class="fas fa-user-plus text-xs" aria-hidden="true"></i>
                    Nuevo Usuario
                </a>
            </div>
        </div>

        <!-- Tarjetas de métricas -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-brand-navy-50 text-brand-navy flex items-center justify-center shrink-0">
                    <i class="fas fa-users text-lg" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500">Total Usuarios</p>
                    <p class="text-xl font-bold text-brand-navy">{{ $totalUsuarios }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-brand-gold-50 text-brand-gold-700 flex items-center justify-center shrink-0">
                    <i class="fas fa-user-shield text-lg" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500">Administradores</p>
                    <p class="text-xl font-bold text-brand-gold-700">{{ $totalSuperadmins + $totalAdmins }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                    <i class="fas fa-eye text-lg" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500">Solo Lectura</p>
                    <p class="text-xl font-bold text-blue-700">{{ $totalLectura }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                    <i class="fas fa-user-check text-lg" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500">Cuentas Activas</p>
                    <p class="text-xl font-bold text-emerald-700">{{ $totalActivos }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-center gap-3 col-span-2 sm:col-span-1">
                <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center shrink-0">
                    <i class="fas fa-user-slash text-lg" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500">Deshabilitados</p>
                    <p class="text-xl font-bold text-rose-700">{{ $totalInactivos }}</p>
                </div>
            </div>
        </div>

        <!-- Filtros de búsqueda -->
        <div x-data="{ mostrarFiltros: @js((bool) ($hayFiltros ?? false)) }"
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <button type="button" @click="mostrarFiltros = !mostrarFiltros"
                :aria-expanded="mostrarFiltros.toString()"
                class="sm:hidden w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-brand-navy bg-gray-50/70 hover:bg-gray-100/70 transition">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-sliders-h text-brand-gold text-xs" aria-hidden="true"></i>
                    Filtros de usuarios
                    @if ($hayFiltros ?? false)
                        <span class="px-2 py-0.5 text-[11px] rounded-full bg-brand-gold text-brand-navy font-bold">Filtros aplicados</span>
                    @endif
                </span>
                <i class="fas fa-chevron-down text-xs text-gray-500 transition-transform duration-200"
                    :class="{ 'rotate-180': mostrarFiltros }" aria-hidden="true"></i>
            </button>

            <form method="GET" action="{{ route('usuarios.index') }}"
                :class="{ 'block': mostrarFiltros, 'hidden': !mostrarFiltros }"
                class="{{ ($hayFiltros ?? false) ? 'block' : 'hidden' }} sm:block p-5">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="filtro_buscar" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Nombre o Correo
                        </label>
                        <input type="text" id="filtro_buscar" name="buscar" value="{{ request('buscar') }}"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold"
                            placeholder="Ej. Nicolle, admin@ccisur.hn...">
                    </div>

                    <div>
                        <label for="filtro_rol" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Rol de Acceso
                        </label>
                        <select id="filtro_rol" name="rol"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">-- Todos los roles --</option>
                            <option value="{{ \App\Models\User::ROL_SUPERADMIN }}" @selected(request('rol') === \App\Models\User::ROL_SUPERADMIN)>
                                Superadministrador (Seguridad y Usuarios)
                            </option>
                            <option value="{{ \App\Models\User::ROL_ADMINISTRADOR }}" @selected(request('rol') === \App\Models\User::ROL_ADMINISTRADOR)>
                                Administrador (Control inventario)
                            </option>
                            <option value="{{ \App\Models\User::ROL_LECTURA }}" @selected(request('rol') === \App\Models\User::ROL_LECTURA)>
                                Solo Lectura (Consultas y reportes)
                            </option>
                        </select>
                    </div>

                    <div>
                        <label for="filtro_estado" class="block text-xs font-semibold text-brand-navy uppercase tracking-wider mb-1">
                            Estado de Cuenta
                        </label>
                        <select id="filtro_estado" name="estado"
                            class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            <option value="">-- Todos los estados --</option>
                            <option value="1" @selected(request('estado') === '1')>Activo (Habilitado)</option>
                            <option value="0" @selected(request('estado') === '0')>Inactivo (Deshabilitado)</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    @if ($hayFiltros ?? false)
                        <a href="{{ route('usuarios.index') }}"
                            class="px-3 py-2 text-xs font-medium text-gray-600 hover:text-brand-navy hover:underline transition">
                            Limpiar filtros
                        </a>
                    @endif
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-navy text-white text-xs font-semibold shadow-sm hover:bg-brand-navy-700 transition">
                        <i class="fas fa-search text-xs" aria-hidden="true"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50/80 text-xs uppercase font-semibold text-brand-navy border-b border-gray-100">
                        <tr>
                            <th scope="col" class="px-5 py-3.5">Usuario</th>
                            <th scope="col" class="px-5 py-3.5">Correo Electrónico</th>
                            <th scope="col" class="px-5 py-3.5">Rol</th>
                            <th scope="col" class="px-5 py-3.5">Estado</th>
                            <th scope="col" class="px-5 py-3.5">Fecha de Registro</th>
                            <th scope="col" class="px-5 py-3.5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($usuarios as $usuario)
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-brand-navy text-brand-gold flex items-center justify-center font-bold text-sm shrink-0" aria-hidden="true">
                                            {{ mb_strtoupper(mb_substr($usuario->name, 0, 1, 'UTF-8'), 'UTF-8') }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-brand-navy flex items-center gap-2">
                                                {{ $usuario->name }}
                                                @if ($usuario->id === auth()->id())
                                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-brand-gold-100 text-brand-navy">
                                                        Tú
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 font-mono text-xs text-gray-600">
                                    {{ $usuario->email }}
                                </td>
                                <td class="px-5 py-4">
                                    @if ($usuario->esSuperAdmin())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-900 border border-purple-200">
                                            <i class="fas fa-crown text-[10px] text-amber-500" aria-hidden="true"></i>
                                            Superadmin
                                        </span>
                                    @elseif ($usuario->esAdministrador())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-gold-100 text-brand-gold-800 border border-brand-gold-300">
                                            <i class="fas fa-shield-alt text-[10px]" aria-hidden="true"></i>
                                            Administrador
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            <i class="fas fa-eye text-[10px]" aria-hidden="true"></i>
                                            Solo Lectura
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if ($usuario->estaActivo())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500" aria-hidden="true"></span>
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500">
                                    {{ $usuario->created_at?->format('d/m/Y') ?? 'N/A' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if ($usuario->id === auth()->id())
                                        <a href="{{ route('profile.edit') }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-navy-50 text-brand-navy text-xs font-semibold hover:bg-brand-navy-100 transition"
                                            title="Gestionar mi información personal">
                                            <i class="fas fa-user-circle text-xs" aria-hidden="true"></i>
                                            Mi Perfil
                                        </a>
                                    @elseif ($usuario->puedeSerGestionadoPor(auth()->user()))
                                        <div class="inline-flex items-center justify-end gap-2">
                                            <!-- Editar -->
                                            <a href="{{ route('usuarios.edit', $usuario) }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-brand-navy hover:bg-gray-100 transition"
                                                title="Editar usuario">
                                                <i class="fas fa-edit text-xs" aria-hidden="true"></i>
                                                Editar
                                            </a>

                                            <!-- Alternar Activo / Inactivo -->
                                            @if ($usuario->esUltimoSuperadminActivo())
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-gray-400 bg-gray-50 cursor-not-allowed"
                                                    title="No se puede desactivar al único superadministrador activo">
                                                    <i class="fas fa-lock text-xs" aria-hidden="true"></i>
                                                    Activo
                                                </span>
                                            @else
                                                <form method="POST" action="{{ route('usuarios.toggle-activo', $usuario) }}"
                                                    data-swal-toggle-usuario
                                                    data-swal-nombre="{{ $usuario->name }}"
                                                    data-swal-accion="{{ $usuario->estaActivo() ? 'deshabilitar' : 'habilitar' }}"
                                                    data-swal-es-superadmin="{{ $usuario->esSuperAdmin() ? 'true' : 'false' }}"
                                                    class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border text-xs font-medium transition cursor-pointer {{ $usuario->estaActivo() ? 'border-red-200 text-red-700 hover:bg-red-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}"
                                                        title="{{ $usuario->estaActivo() ? 'Deshabilitar cuenta' : 'Habilitar cuenta' }}">
                                                        <i class="fas {{ $usuario->estaActivo() ? 'fa-user-slash' : 'fa-user-check' }} text-xs" aria-hidden="true"></i>
                                                        {{ $usuario->estaActivo() ? 'Desactivar' : 'Activar' }}
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-500 border border-gray-200" title="Esta cuenta tiene nivel igual o superior al tuyo y no puede ser modificada">
                                            <i class="fas fa-lock text-[10px]" aria-hidden="true"></i>
                                            Protegido
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500">
                                    No se encontraron usuarios que coincidan con los criterios de búsqueda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($usuarios->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $usuarios->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
