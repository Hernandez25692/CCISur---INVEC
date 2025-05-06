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
});

// API para obtener ítems por tipo (uso en formularios dinámicos)
Route::get('/api/obtener-items/{tipo}', function ($tipo) {
    return $tipo === 'mobiliario'
        ? \App\Models\Mobiliario::select('id', 'nombre')->get()
        : ($tipo === 'dispositivo'
            ? \App\Models\Dispositivo::select('id', 'nombre')->get()
            : response()->json([]));
});

// Rutas de autenticación generadas por Breeze
require __DIR__ . '/auth.php';
