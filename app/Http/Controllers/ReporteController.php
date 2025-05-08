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
        $mobiliarios = \App\Models\Mobiliario::where('estado', 'disponible')->get();
        $dispositivos = \App\Models\Dispositivo::where('estado', 'disponible')->get();

        return view('reportes.disponibles', compact('mobiliarios', 'dispositivos'));
    }
}
