<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;



class AsignacionController extends Controller
{
    // Listar asignaciones
    public function index()
    {
        $asignaciones = Asignacion::latest()->get();
        return view('asignaciones.index', compact('asignaciones'));
    }

    // Formulario para nueva asignación
    public function create()
    {
        return view('asignaciones.create');
    }

    // Guardar nueva asignación
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:mobiliario,dispositivo',
            'id_referencia' => 'required|integer',
            'colaborador' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'fecha_entrega' => 'required|date',
            'entregado_por' => 'required|string|max:255',
        ]);

        // Verificar si el bien ya está asignado
        $existeAsignacion = Asignacion::where('id_referencia', $request->id_referencia)
            ->where('tipo', $request->tipo)
            ->doesntHave('devolucion') // sin devolución
            ->exists();

        if ($existeAsignacion) {
            return redirect()->back()->with('error', 'Este bien ya ha sido asignado y no ha sido devuelto.');
        }

        // Crear la asignación
        $asignacion = Asignacion::create([
            ...$request->all(),
            'uuid' => \Illuminate\Support\Str::uuid()
        ]);

        // Cambiar disponibilidad
        if ($request->tipo === 'mobiliario') {
            $item = \App\Models\Mobiliario::find($request->id_referencia);
        } else {
            $item = \App\Models\Dispositivo::find($request->id_referencia);
        }

        if ($item) {
            $item->disponibilidad = 'asignado';
            $item->save();
        }

        return redirect()->route('asignaciones.index')->with('success', 'Asignación registrada correctamente.');
    }


    // Mostrar detalles (opcional para PDF)
    public function show(Asignacion $asignacion)
    {
        if ($asignacion->tipo === 'mobiliario') {
            $item = Mobiliario::find($asignacion->id_referencia);
        } else {
            $item = Dispositivo::find($asignacion->id_referencia);
        }

        return view('asignaciones.show', compact('asignacion', 'item'));
    }

    // Eliminar asignación
    public function destroy(Asignacion $asignacion)
    {
        $asignacion->delete();
        return redirect()->route('asignaciones.index')->with('success', 'Asignación eliminada.');
    }

    public function pdf(Asignacion $asignacion)
    {
        $item = $asignacion->tipo === 'mobiliario'
            ? \App\Models\Mobiliario::find($asignacion->id_referencia)
            : \App\Models\Dispositivo::find($asignacion->id_referencia);

        // Generar el QR en formato SVG
        $qrSvg = QrCode::format('svg')->size(150)->generate(route('verificar.asignacion', $asignacion->uuid));

        $pdf = Pdf::loadView('asignaciones.acta_pdf', compact('asignacion', 'item', 'qrSvg'));
        return $pdf->stream('Acta_Entrega_' . $asignacion->id . '.pdf');
    }

    public function verPublica($uuid)
    {
        $asignacion = Asignacion::where('uuid', $uuid)->firstOrFail();

        $item = $asignacion->tipo === 'mobiliario'
            ? Mobiliario::find($asignacion->id_referencia)
            : Dispositivo::find($asignacion->id_referencia);

        return view('asignaciones.publica', compact('asignacion', 'item'));
    }

    public function historial($colaborador)
    {
        $asignaciones = Asignacion::where('colaborador', $colaborador)->latest()->get();

        foreach ($asignaciones as $asignacion) {
            $asignacion->item = $asignacion->tipo === 'mobiliario'
                ? \App\Models\Mobiliario::find($asignacion->id_referencia)
                : \App\Models\Dispositivo::find($asignacion->id_referencia);
        }

        return view('asignaciones.historial', compact('asignaciones', 'colaborador'));
    }
}
