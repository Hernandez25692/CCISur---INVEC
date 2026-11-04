<?php

use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispositivoController;
use App\Http\Controllers\MobiliarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Página de inicio pública
Route::get('/', function () {
    return view('inicio');
});

// Dashboard (requiere login)
Route::get('/dashboard', DashboardController::class)->middleware(['auth'])->name('dashboard');

// Perfil de usuario (editar, actualizar)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Rutas protegidas (solo autenticados)
Route::middleware(['auth'])->group(function () {
    // Establecer contraseña obligatoria de primer inicio de sesión
    Route::post('/primer-cambio-password', [\App\Http\Controllers\Auth\PasswordController::class, 'primerCambioPassword'])->name('primer-cambio-password');

    // Gestión de usuarios del sistema (exclusivo para el Superadministrador)
    Route::middleware(['can:superadmin'])->group(function () {
        Route::patch('/usuarios/{usuario}/toggle-activo', [UserController::class, 'toggleActivo'])->name('usuarios.toggle-activo');
        Route::resource('usuarios', UserController::class)->except(['show', 'destroy']);
    });

    // CRUD de recursos
    Route::get('/mobiliario/bajas/exportar', [\App\Http\Controllers\MobiliarioController::class, 'exportarBajas'])->name('mobiliario.bajas.exportar');
    Route::get('/mobiliario/bajas', [\App\Http\Controllers\MobiliarioController::class, 'bajas'])->name('mobiliario.bajas');
    Route::get('/mobiliario/exportar', [\App\Http\Controllers\MobiliarioController::class, 'exportar'])->name('mobiliario.exportar');
    Route::post('/mobiliario/{id}/reactivar', [\App\Http\Controllers\MobiliarioController::class, 'reactivar'])->name('mobiliario.reactivar');
    Route::get('/mobiliario/{mobiliario}/historial', [\App\Http\Controllers\MobiliarioController::class, 'historial'])->withTrashed()->name('mobiliario.historial');
    Route::resource('mobiliario', MobiliarioController::class);

    Route::get('/dispositivos/bajas/exportar', [\App\Http\Controllers\DispositivoController::class, 'exportarBajas'])->name('dispositivos.bajas.exportar');
    Route::get('/dispositivos/bajas', [\App\Http\Controllers\DispositivoController::class, 'bajas'])->name('dispositivos.bajas');
    Route::get('/dispositivos/exportar', [\App\Http\Controllers\DispositivoController::class, 'exportar'])->name('dispositivos.exportar');
    Route::post('/dispositivos/{id}/reactivar', [\App\Http\Controllers\DispositivoController::class, 'reactivar'])->name('dispositivos.reactivar');
    Route::get('/dispositivos/{dispositivo}/historial', [\App\Http\Controllers\DispositivoController::class, 'historial'])->withTrashed()->name('dispositivos.historial');
    Route::resource('dispositivos', DispositivoController::class);

    Route::get('/empleados/bajas', [\App\Http\Controllers\EmpleadoController::class, 'bajas'])->name('empleados.bajas');
    Route::post('/empleados/{id}/reactivar', [\App\Http\Controllers\EmpleadoController::class, 'reactivar'])->name('empleados.reactivar');
    Route::resource('empleados', \App\Http\Controllers\EmpleadoController::class);

    Route::get('/asignaciones/exportar', [AsignacionController::class, 'exportar'])->name('asignaciones.exportar');
    Route::resource('asignaciones', AsignacionController::class)->except(['edit', 'update'])->parameters([
        'asignaciones' => 'asignacion',
    ]);

    // PDF de acta de asignación
    Route::get('asignaciones/{asignacion}/pdf', [AsignacionController::class, 'pdf'])->name('asignaciones.pdf');

    // Reportes (Redirección para compatibilidad)
    Route::get('/reportes/asignados', fn () => redirect()->route('asignaciones.index', request()->query()))->name('reportes.asignados');
    Route::get('/reportes/disponibles', fn () => redirect()->route('dispositivos.index', request()->query()))->name('reportes.disponibles');
    Route::get('/reportes/asignados/exportar', fn () => redirect()->route('asignaciones.exportar', request()->query()))->name('reportes.asignados.exportar');
    Route::get('/reportes/disponibles/exportar', fn () => redirect()->route('dispositivos.exportar', request()->query()))->name('reportes.disponibles.exportar');

    Route::get('/empleados/{empleado}/historial', [\App\Http\Controllers\EmpleadoController::class, 'historial'])->withTrashed()->name('empleados.historial');
    Route::get('/asignaciones/empleado/{empleado}', [\App\Http\Controllers\EmpleadoController::class, 'historial'])->withTrashed()->name('asignaciones.historial');

    Route::get('/devoluciones/exportar', [\App\Http\Controllers\DevolucionController::class, 'exportar'])->name('devoluciones.exportar');
    Route::resource('devoluciones', \App\Http\Controllers\DevolucionController::class)
        ->except(['edit', 'update'])
        ->parameters(['devoluciones' => 'devolucion']);
    Route::get('devoluciones/{devolucion}/pdf', [\App\Http\Controllers\DevolucionController::class, 'pdf'])->name('devoluciones.pdf');

    // APIs para formularios dinámicos y búsquedas ágiles
    Route::get('/api/buscar-empleados', function (\Illuminate\Http\Request $request) {
        $q = trim((string) $request->input('q'));
        $query = \App\Models\Empleado::query();

        if ($q !== '') {
            $query->where(function ($sub) use ($q): void {
                $sub->where('nombre_completo', 'like', "%{$q}%")
                    ->orWhere('identidad', 'like', "%{$q}%")
                    ->orWhere('codigo', 'like', "%{$q}%");
            });
        }

        return $query->select(['id', 'codigo', 'nombre_completo', 'identidad', 'gerencia', 'cargo'])
            ->orderBy('nombre_completo')
            ->limit(20)
            ->get();
    });

    Route::get('/api/categorias-items/{tipo}', function (string $tipo) {
        $model = $tipo === 'mobiliario'
            ? \App\Models\Mobiliario::query()
            : ($tipo === 'dispositivo' ? \App\Models\Dispositivo::query() : null);

        if (! $model) {
            return response()->json([]);
        }

        $categorias = $model->disponibles()
            ->whereNotNull('tipo')
            ->where('tipo', '!=', '')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        return response()->json($categorias);
    });

    Route::get('/api/buscar-items/{tipo}', function (string $tipo, \Illuminate\Http\Request $request) {
        $categoria = trim((string) $request->input('categoria'));
        $q = trim((string) $request->input('q'));
        $forzarId = $request->input('id');

        $baseQuery = $tipo === 'mobiliario'
            ? \App\Models\Mobiliario::query()
            : ($tipo === 'dispositivo' ? \App\Models\Dispositivo::query() : null);

        if (! $baseQuery) {
            return response()->json([]);
        }

        $query = $baseQuery->where(function ($scope) use ($forzarId): void {
            $scope->disponibles();
            if ($forzarId) {
                $scope->orWhere('id', $forzarId);
            }
        });

        if ($categoria !== '') {
            $query->where('tipo', $categoria);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q, $tipo): void {
                $sub->where('nombre', 'like', "%{$q}%")
                    ->orWhere('etiqueta', 'like', "%{$q}%");
                if ($tipo === 'dispositivo') {
                    $sub->orWhere('n_serie', 'like', "%{$q}%")
                        ->orWhere('marca', 'like', "%{$q}%")
                        ->orWhere('modelo', 'like', "%{$q}%");
                }
            });
        }

        $columns = ['id', 'nombre', 'tipo', 'etiqueta', 'estado', 'disponibilidad'];
        if ($tipo === 'dispositivo') {
            $columns = array_merge($columns, ['marca', 'modelo', 'n_serie']);
        }

        $items = $query->select($columns)->orderBy('nombre')->limit(30)->get();

        return response()->json($items);
    });

    // API para compatibilidad con llamadas existentes
    Route::get('/api/obtener-items/{tipo}', function ($tipo) {
        $forzarId = request('id');

        $baseQuery = $tipo === 'mobiliario'
            ? \App\Models\Mobiliario::query()
            : ($tipo === 'dispositivo' ? \App\Models\Dispositivo::query() : null);

        if (! $baseQuery) {
            return [];
        }

        $query = $baseQuery->where(function ($q) use ($forzarId) {
            $q->disponibles();
            if ($forzarId) {
                $q->orWhere('id', $forzarId);
            }
        });

        return $query->select('id', 'nombre', 'etiqueta')->get();
    });
});

// Rutas de autenticación generadas por Breeze
require __DIR__.'/auth.php';
