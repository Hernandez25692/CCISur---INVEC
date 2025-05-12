<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;

class ReporteController extends Controller
{
    // Reporte de bienes asignados
    public function asignados()
    {
        $asignaciones = Asignacion::latest()->get();

        return view('reportes.asignados', compact('asignaciones'));
    }

    // Reporte de bienes disponibles (no asignados)
    public function disponibles()
    {
        $asignadosIdsDispositivos = Asignacion::where('tipo', 'dispositivo')->pluck('id_referencia')->toArray();
        $asignadosIdsMobiliario = Asignacion::where('tipo', 'mobiliario')->pluck('id_referencia')->toArray();

        $dispositivos = Dispositivo::whereNotIn('id', $asignadosIdsDispositivos)->get();
        $mobiliarios = Mobiliario::whereNotIn('id', $asignadosIdsMobiliario)->get();

        return view('reportes.disponibles', compact('dispositivos', 'mobiliarios'));
    }
}
