<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    // Reporte de bienes asignados
    public function asignados()
    {
        $asignaciones = Asignacion::latest()->get();

        return view('reportes.asignados', compact('asignaciones'));
    }

    // Reporte de bienes disponibles (no asignados)
    public function disponibles(Request $request)
    {
        $asignadosIdsDispositivos = Asignacion::where('tipo', 'dispositivo')->pluck('id_referencia')->toArray();
        $asignadosIdsMobiliario = Asignacion::where('tipo', 'mobiliario')->pluck('id_referencia')->toArray();

        $filtroTipoDispositivo = $request->input('tipo_dispositivo');
        $filtroTipoMobiliario = $request->input('tipo_mobiliario');

        $dispositivos = Dispositivo::whereNotIn('id', $asignadosIdsDispositivos)
            ->when($filtroTipoDispositivo, fn($q) => $q->where('tipo', 'like', '%' . $filtroTipoDispositivo . '%'))
            ->paginate(10, ['*'], 'dispositivos');

        $mobiliarios = Mobiliario::whereNotIn('id', $asignadosIdsMobiliario)
            ->when($filtroTipoMobiliario, fn($q) => $q->where('tipo', 'like', '%' . $filtroTipoMobiliario . '%'))
            ->paginate(10, ['*'], 'mobiliarios');

        return view('reportes.disponibles', compact('dispositivos', 'mobiliarios'));
    }
}
