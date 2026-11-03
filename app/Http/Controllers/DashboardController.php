<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $countMobiliario = Mobiliario::count();
        $countDispositivos = Dispositivo::count();

        // Disponibilidad real derivada de una sola fuente de verdad (bienes asignables vs asignaciones activas)
        $asignadoMobiliario = Mobiliario::asignados()->count();
        $disponibleMobiliario = Mobiliario::disponibles()->count();

        $asignadoDispositivos = Dispositivo::asignados()->count();
        $disponibleDispositivos = Dispositivo::disponibles()->count();

        $totalAsignado = $asignadoMobiliario + $asignadoDispositivos;
        $totalDisponible = $disponibleMobiliario + $disponibleDispositivos;

        // Solo asignaciones activas (bajo custodia vigente, sin devolución registrada)
        $ultimasAsignaciones = Asignacion::activas()
            ->with(['empleado', 'mobiliario', 'dispositivo'])
            ->latest('id')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'countMobiliario',
            'countDispositivos',
            'asignadoMobiliario',
            'disponibleMobiliario',
            'asignadoDispositivos',
            'disponibleDispositivos',
            'totalAsignado',
            'totalDisponible',
            'ultimasAsignaciones',
        ));
    }
}
