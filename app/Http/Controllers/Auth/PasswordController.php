<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
            'current_password.current_password' => 'La contraseña actual ingresada es incorrecta.',
            'password.required' => 'Debes ingresar una nueva contraseña.',
            'password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'password.min' => 'La nueva contraseña debe tener al menos :min caracteres.',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->to(route('profile.edit') . '#seccion-password')
                ->withErrors($validator, 'updatePassword')
                ->withInput();
        }

        $validated = $validator->validated();
        $user = $request->user();

        $user->update([
            'password' => Hash::make($validated['password']),
            'remember_token' => Str::random(60),
        ]);

        // Purgar todas las sesiones persistidas del usuario si se usa driver database
        if (Schema::hasTable('sessions')) {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        // Cerrar la sesión actual e invalidar datos
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('url.intended');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente.',
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login')
            ->with('status', 'Tu contraseña ha sido actualizada exitosamente. Por seguridad, inicia sesión con tu nueva contraseña.');
    }

    /**
     * Establecer la contraseña personal obligatoria en el primer inicio de sesión de un usuario nuevo.
     */
    public function primerCambioPassword(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        // Blindaje: Solo los usuarios con la bandera activa pueden usar este endpoint de un solo uso
        if (! $user->debeCambiarPassword()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'No tienes pendiente un cambio de contraseña obligatorio.',
                ], 403);
            }

            abort(403, 'No tienes pendiente un cambio de contraseña obligatorio.');
        }

        $validator = Validator::make($request->all(), [
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'password.required' => 'Debes ingresar tu nueva contraseña personal.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La nueva contraseña debe contener al menos :min caracteres.',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return back()->withErrors($validator, 'primerPassword')->withInput();
        }

        $validated = $validator->validated();
        $user = $request->user();

        $user->update([
            'password' => Hash::make($validated['password']),
            'debe_cambiar_password' => false,
            'remember_token' => Str::random(60),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Contraseña personal establecida correctamente.',
                'redirect' => route('dashboard'),
            ]);
        }

        return redirect()->route('dashboard')
            ->with('status', 'Tu contraseña personal ha sido establecida exitosamente. Bienvenido a INVEC.');
    }
}
