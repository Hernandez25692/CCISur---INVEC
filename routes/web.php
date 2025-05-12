<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MobiliarioController;
use App\Http\Controllers\DispositivoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\ReporteController;

// Página de inicio pública
Route::get('/', function () {
    return view('inicio');
});

Route::get('/verificacion-asignacion/{uuid}', [App\Http\Controllers\AsignacionController::class, 'verPublica'])
    ->name('verificar.asignacion');


// Dashboard (requiere login)
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil de usuario (editar, actualizar, eliminar)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas protegidas (solo autenticados)
Route::middleware(['auth'])->group(function () {
    // CRUD de recursos
    Route::resource('mobiliario', MobiliarioController::class);
    Route::resource('dispositivos', DispositivoController::class);
    Route::resource('asignaciones', AsignacionController::class)->parameters([
        'asignaciones' => 'asignacion'
    ]);


    // PDF de acta de asignación
    Route::get('asignaciones/{asignacion}/pdf', [AsignacionController::class, 'pdf'])->name('asignaciones.pdf');

    // Reportes
    Route::get('/reportes/asignados', [ReporteController::class, 'asignados'])->name('reportes.asignados');
    Route::get('/reportes/disponibles', [ReporteController::class, 'disponibles'])->name('reportes.disponibles');
    Route::get('/asignaciones/colaborador/{colaborador}', [App\Http\Controllers\AsignacionController::class, 'historial'])->name('asignaciones.historial');
    Route::resource('devoluciones', \App\Http\Controllers\DevolucionController::class);
    Route::get('devoluciones/{devolucion}/pdf', [\App\Http\Controllers\DevolucionController::class, 'pdf'])->name('devoluciones.pdf');
    Route::get('/devoluciones/buscar', [\App\Http\Controllers\DevolucionController::class, 'buscarAsignaciones'])->name('devoluciones.buscar');
});

// API para obtener ítems por tipo (uso en formularios dinámicos)
Route::get('/api/obtener-items/{tipo}', function ($tipo) {
    if ($tipo === 'mobiliario') {
        return \App\Models\Mobiliario::where('disponibilidad', 'Sin Asignar')
            ->select('id', 'nombre', 'etiqueta')->get();
    } elseif ($tipo === 'dispositivo') {
        return \App\Models\Dispositivo::where('disponibilidad', 'Sin Asignar')
            ->select('id', 'nombre', 'etiqueta')->get();
    }
    return response()->json([]);
});

Route::get('/devoluciones/buscar', [\App\Http\Controllers\DevolucionController::class, 'buscarAsignaciones'])->name('devoluciones.buscar');
Route::resource('empleados', \App\Http\Controllers\EmpleadoController::class);
Route::get('/asignaciones/empleado/{empleado}', [AsignacionController::class, 'historial'])->name('asignaciones.historial');



// Rutas de autenticación generadas por Breeze
require __DIR__ . '/auth.php';
