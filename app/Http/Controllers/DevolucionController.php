<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\Asignacion;
use App\Models\Mobiliario;
use App\Models\Dispositivo;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DevolucionController extends Controller
{
    public function index()
    {
        $devoluciones = Devolucion::with('asignacion')->latest()->get();
        return view('devoluciones.index', compact('devoluciones'));
    }

    public function create()
    {
        $asignaciones = Asignacion::all();
        return view('devoluciones.create', compact('asignaciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asignacion_id' => 'required|exists:asignacions,id',
            'fecha_devolucion' => 'required|date',
            'recibido_por' => 'required|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        // Verificar si ya fue devuelto
        $yaDevuelto = \App\Models\Devolucion::where('asignacion_id', $request->asignacion_id)->exists();

        if ($yaDevuelto) {
            return redirect()->back()->with('error', 'Este bien ya fue devuelto y no puede devolverse nuevamente.');
        }

        $asignacion = \App\Models\Asignacion::findOrFail($request->asignacion_id);

        // Cambiar estado del bien a disponible
        $item = $asignacion->tipo === 'mobiliario'
            ? \App\Models\Mobiliario::find($asignacion->id_referencia)
            : \App\Models\Dispositivo::find($asignacion->id_referencia);

        if ($item) {
            $item->estado = 'disponible';
            $item->save();
        }

        // Registrar devolución
        \App\Models\Devolucion::create([
            'asignacion_id' => $request->asignacion_id,
            'fecha_devolucion' => $request->fecha_devolucion,
            'recibido_por' => $request->recibido_por,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('devoluciones.index')->with('success', 'Devolución registrada correctamente.');
    }



    public function show(Devolucion $devolucion)
    {
        return view('devoluciones.show', compact('devolucion'));
    }

    public function pdf(Devolucion $devolucion)
    {
        $asignacion = $devolucion->asignacion;
        $item = $asignacion->tipo === 'mobiliario'
            ? Mobiliario::find($asignacion->id_referencia)
            : Dispositivo::find($asignacion->id_referencia);

        $pdf = Pdf::loadView('devoluciones.pdf', compact('devolucion', 'asignacion', 'item'));
        return $pdf->stream('Acta_Devolucion_' . $devolucion->id . '.pdf');
    }

    public function destroy(Devolucion $devolucion)
    {
        $devolucion->delete();
        return redirect()->route('devoluciones.index')->with('success', 'Devolución eliminada.');
    }

    public function verPublica($uuid)
    {
        $asignacion = Asignacion::where('uuid', $uuid)->firstOrFail();
        $devolucion = \App\Models\Devolucion::where('asignacion_id', $asignacion->id)->firstOrFail();

        $item = $asignacion->tipo === 'mobiliario'
            ? \App\Models\Mobiliario::find($asignacion->id_referencia)
            : \App\Models\Dispositivo::find($asignacion->id_referencia);

        return view('devoluciones.publica', compact('devolucion', 'asignacion', 'item'));
    }
}
