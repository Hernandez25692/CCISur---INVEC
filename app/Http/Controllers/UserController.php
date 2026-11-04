<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Listar todos los usuarios del sistema con filtros de búsqueda y rol.
     */
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar'));
        $rol = $request->input('rol');
        $estado = $request->input('estado');

        $query = User::query();

        if ($buscar !== '') {
            $buscarSafe = addcslashes($buscar, '%_\\');
            $query->where(function ($q) use ($buscarSafe): void {
                $q->where('name', 'like', "%{$buscarSafe}%")
                    ->orWhere('email', 'like', "%{$buscarSafe}%");
            });
        }

        if ($rol && in_array($rol, [User::ROL_SUPERADMIN, User::ROL_ADMINISTRADOR, User::ROL_LECTURA], true)) {
            $query->where('rol', $rol);
        }

        if ($estado !== null && in_array($estado, ['1', '0'], true)) {
            $query->where('activo', (bool) (int) $estado);
        }

        $hayFiltros = filled($buscar) || filled($rol) || ($estado !== null && $estado !== '');

        $usuarios = $query->orderByRaw("CASE WHEN id = ? THEN 0 ELSE 1 END", [auth()->id()])
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $totalUsuarios = User::count();
        $totalActivos = User::where('activo', true)->count();
        $totalInactivos = User::where('activo', false)->count();
        $totalSuperadmins = User::where('rol', User::ROL_SUPERADMIN)->count();
        $totalAdmins = User::where('rol', User::ROL_ADMINISTRADOR)->count();
        $totalLectura = User::where('rol', User::ROL_LECTURA)->count();

        return view('usuarios.index', compact(
            'usuarios',
            'hayFiltros',
            'totalUsuarios',
            'totalActivos',
            'totalInactivos',
            'totalSuperadmins',
            'totalAdmins',
            'totalLectura',
        ));
    }

    /**
     * Formulario de creación de usuario.
     */
    public function create(): View
    {
        $rolesDisponibles = $this->rolesDisponibles();

        return view('usuarios.create', compact('rolesDisponibles'));
    }

    /**
     * Guardar un nuevo usuario en la base de datos con contraseña inicial provisional.
     */
    public function store(Request $request): RedirectResponse
    {
        $rolesValidos = array_keys($this->rolesDisponibles());

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'rol' => ['required', Rule::in($rolesValidos)],
            'activo' => ['sometimes', 'boolean'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        // Sudo Mode: Si se asigna el rol Superadministrador, exigir confirmación de la contraseña del operador
        if ($request->input('rol') === User::ROL_SUPERADMIN) {
            $rules['superadmin_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese una dirección de correo electrónico válida.',
            'email.unique' => 'Este correo electrónico ya está registrado en el sistema.',
            'rol.required' => 'Debe seleccionar un rol para el usuario.',
            'rol.in' => 'El rol seleccionado no es válido.',
            'password.required' => 'Debes definir una contraseña inicial provisional.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'superadmin_password.required' => 'Debes ingresar tu contraseña actual de Superadministrador para autorizar este nombramiento.',
            'superadmin_password.current_password' => 'La contraseña de confirmación de Superadministrador es incorrecta.',
        ]);

        $usuario = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'rol' => $validated['rol'],
            'activo' => $request->boolean('activo', true),
            'password' => Hash::make($validated['password']),
            'debe_cambiar_password' => true, // Exige cambio de contraseña en su primer login
            'creado_por' => auth()->id(),
            'actualizado_por' => null,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario «{$usuario->name}» creado exitosamente. En su primer inicio de sesión se le solicitará establecer su contraseña personal.");
    }

    /**
     * Formulario de edición de usuario (sin campos de contraseña).
     */
    public function edit(User $usuario): View|RedirectResponse
    {
        if (! $usuario->puedeSerGestionadoPor(auth()->user())) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes modificar tu propia cuenta desde este panel.');
        }

        $usuario->load(['creador', 'editor']);
        $rolesDisponibles = $this->rolesDisponibles();

        return view('usuarios.edit', compact('usuario', 'rolesDisponibles'));
    }

    /**
     * Actualizar los datos del usuario (Nombre, Correo, Rol y Estado).
     */
    public function update(Request $request, User $usuario): RedirectResponse
    {
        if (! $usuario->puedeSerGestionadoPor(auth()->user())) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes modificar tu propia cuenta desde este panel.');
        }

        $rolesValidos = array_keys($this->rolesDisponibles());

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'rol' => ['required', Rule::in($rolesValidos)],
            'activo' => ['sometimes', 'boolean'],
        ];

        // Sudo Mode: Si se promueve a Superadministrador O se modifica a un Superadministrador existente
        if ($request->input('rol') === User::ROL_SUPERADMIN || $usuario->esSuperAdmin()) {
            $rules['superadmin_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese una dirección de correo válida.',
            'email.unique' => 'Este correo ya pertenece a otro usuario.',
            'rol.required' => 'Debe seleccionar un rol válido.',
            'rol.in' => 'El rol seleccionado no es válido.',
            'superadmin_password.required' => 'Debes ingresar tu contraseña actual de Superadministrador para autorizar cambios en esta cuenta.',
            'superadmin_password.current_password' => 'La contraseña de confirmación de Superadministrador es incorrecta.',
        ]);

        // Invariante de seguridad: No se puede degradar ni deshabilitar al último Superadministrador activo
        if ($usuario->esUltimoSuperadminActivo()) {
            if ($validated['rol'] !== User::ROL_SUPERADMIN) {
                return back()
                    ->withInput()
                    ->with('error', 'No puedes cambiar el rol porque este usuario es el único Superadministrador activo del sistema.');
            }

            if ($request->has('activo') && ! $request->boolean('activo')) {
                return back()
                    ->withInput()
                    ->with('error', 'No puedes deshabilitar al único Superadministrador activo del sistema.');
            }
        }

        $usuario->name = $validated['name'];
        $usuario->email = $validated['email'];
        $usuario->rol = $validated['rol'];
        $usuario->activo = $request->has('activo') ? $request->boolean('activo') : (bool) $usuario->activo;
        $usuario->actualizado_por = auth()->id();

        // Si el usuario fue desactivado, revocar sus sesiones de inmediato
        if (! $usuario->activo) {
            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $usuario->id)->delete();
            }
            $usuario->forceFill(['remember_token' => Str::random(60)]);
        }

        $usuario->save();

        return redirect()->route('usuarios.index')
            ->with('success', "Usuario «{$usuario->name}» actualizado exitosamente.");
    }

    /**
     * Alternar el estado activo / inactivo de un usuario (Dar de alta / Dar de baja).
     */
    public function toggleActivo(Request $request, User $usuario): RedirectResponse
    {
        if (! $usuario->puedeSerGestionadoPor(auth()->user())) {
            return back()->with('error', 'No puedes modificar tu propia cuenta desde este panel.');
        }

        // Prohibir deshabilitar al único Superadministrador activo
        if ($usuario->esUltimoSuperadminActivo()) {
            return back()->with('error', 'No puedes deshabilitar al único Superadministrador activo del sistema.');
        }

        // Sudo Mode: Si se altera el estado de un Superadministrador, exigir confirmación de contraseña
        if ($usuario->esSuperAdmin()) {
            $validator = Validator::make($request->all(), [
                'superadmin_password' => ['required', 'current_password'],
            ], [
                'superadmin_password.required' => 'Debes ingresar tu contraseña actual de Superadministrador para autorizar esta acción.',
                'superadmin_password.current_password' => 'La contraseña de confirmación de Superadministrador es incorrecta.',
            ]);

            if ($validator->fails()) {
                return back()->with('error', $validator->errors()->first('superadmin_password'));
            }
        }

        $usuario->activo = ! $usuario->activo;
        $usuario->actualizado_por = auth()->id();

        // Si se deshabilita, expulsar sesiones activas de inmediato
        if (! $usuario->activo) {
            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->where('user_id', $usuario->id)->delete();
            }
            $usuario->forceFill(['remember_token' => Str::random(60)]);
        }

        $usuario->save();

        $estadoTexto = $usuario->activo ? 'habilitado' : 'deshabilitado';

        return back()->with('success', "El usuario «{$usuario->name}» ha sido {$estadoTexto} exitosamente.");
    }

    /**
     * Obtener los roles que el Superadministrador puede asignar a otros usuarios.
     *
     * @return array<string, string>
     */
    private function rolesDisponibles(): array
    {
        return [
            User::ROL_SUPERADMIN => 'Superadministrador',
            User::ROL_ADMINISTRADOR => 'Administrador',
            User::ROL_LECTURA => 'Solo Lectura',
        ];
    }
}
