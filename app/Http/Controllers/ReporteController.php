<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    // Reporte de bienes asignados
    public function asignados(Request $request)
    {
        $query = Asignacion::with('empleado');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('empleado')) {
            $query->whereHas('empleado', function ($q) use ($request) {
                $q->where('nombre_completo', 'like', '%' . $request->empleado . '%');
            });
        }

        if ($request->filled('elemento')) {
            $query->where(function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('tipo', 'mobiliario')
                        ->whereIn('id_referencia', \App\Models\Mobiliario::where('nombre', 'like', '%' . $request->elemento . '%')->pluck('id'));
                })->orWhere(function ($q2) use ($request) {
                    $q2->where('tipo', 'dispositivo')
                        ->whereIn('id_referencia', \App\Models\Dispositivo::where('nombre', 'like', '%' . $request->elemento . '%')->pluck('id'));
                });
            });
        }

        $asignaciones = $query->latest()->get();

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
