<?php

use App\Exports\AsignacionesExport;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispositivoController;
use App\Http\Controllers\MobiliarioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

// Página de inicio pública
Route::get('/', function () {
    return view('inicio');
});

// Dashboard (requiere login)
Route::get('/dashboard', DashboardController::class)->middleware(['auth'])->name('dashboard');

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
    Route::resource('empleados', \App\Http\Controllers\EmpleadoController::class);
    Route::resource('asignaciones', AsignacionController::class)->parameters([
        'asignaciones' => 'asignacion',
    ]);

    // PDF de acta de asignación
    Route::get('asignaciones/{asignacion}/pdf', [AsignacionController::class, 'pdf'])->name('asignaciones.pdf');

    // Reportes
    Route::get('/reportes/asignados', [ReporteController::class, 'asignados'])->name('reportes.asignados');
    Route::get('/reportes/disponibles', [ReporteController::class, 'disponibles'])->name('reportes.disponibles');

    // Exportar reportes a Excel
    Route::get('/reportes/asignados/exportar', function () {
        $filtros = [
            'tipo' => request('tipo'),
            'empleado' => request('empleado'),
            'elemento' => request('elemento'),
        ];

        return Excel::download(new AsignacionesExport($filtros), 'reporte_asignaciones.xlsx');
    })->name('reportes.asignados.exportar');
    Route::get('/reportes/disponibles/exportar', [ReporteController::class, 'exportarDisponibles'])->name('reportes.disponibles.exportar');

    Route::get('/asignaciones/colaborador/{colaborador}', [App\Http\Controllers\AsignacionController::class, 'historial'])->name('asignaciones.historial');
    Route::get('/asignaciones/empleado/{empleado}', [AsignacionController::class, 'historial'])->name('asignaciones.historial');
    Route::resource('devoluciones', \App\Http\Controllers\DevolucionController::class);
    Route::get('devoluciones/{devolucion}/pdf', [\App\Http\Controllers\DevolucionController::class, 'pdf'])->name('devoluciones.pdf');
    Route::get('/devoluciones/buscar', [\App\Http\Controllers\DevolucionController::class, 'buscarAsignaciones'])->name('devoluciones.buscar');

    // API para obtener ítems por tipo (uso en formularios dinámicos)
    Route::get('/api/obtener-items/{tipo}', function ($tipo) {
        $forzarId = request('id');

        $baseQuery = $tipo === 'mobiliario'
            ? \App\Models\Mobiliario::query()
            : ($tipo === 'dispositivo' ? \App\Models\Dispositivo::query() : null);

        if (! $baseQuery) {
            return [];
        }

        $query = $baseQuery->where(function ($q) use ($forzarId) {
            $q->where('disponibilidad', 'Sin Asignar');
            if ($forzarId) {
                $q->orWhere('id', $forzarId); // Incluir el solicitado aunque esté asignado
            }
        });

        return $query->select('id', 'nombre', 'etiqueta')->get();
    });
});

// Rutas de autenticación generadas por Breeze
require __DIR__.'/auth.php';
