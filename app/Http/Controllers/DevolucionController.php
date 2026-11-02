<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Devolucion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $devoluciones = Devolucion::with(['asignacion.empleado', 'asignacion.mobiliario', 'asignacion.dispositivo'])
            ->whereHas('asignacion', function ($query) use ($buscar) {
                $query->when($buscar, function ($q) use ($buscar) {
                    $q->whereHas('empleado', function ($sub) use ($buscar) {
                        $sub->where('nombre_completo', 'like', "%$buscar%");
                    })
                        ->orWhereHas('mobiliario', function ($sub) use ($buscar) {
                            $sub->where('etiqueta', 'like', "%$buscar%");
                        })
                        ->orWhereHas('dispositivo', function ($sub) use ($buscar) {
                            $sub->where('etiqueta', 'like', "%$buscar%");
                        });
                });
            })
            ->latest()
            ->paginate(10);

        return view('devoluciones.index', compact('devoluciones'));
    }

    public function create()
    {
        // Solo asignaciones que no tienen devolución registrada
        $asignaciones = \App\Models\Asignacion::with(['mobiliario', 'dispositivo'])
            ->whereDoesntHave('devolucion')
            ->latest()
            ->get();

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
            return redirect()->back()->with('error', 'Este bien ya fue devuelto.');
        }

        $asignacion = Asignacion::findOrFail($request->asignacion_id);

        // Cambiar disponibilidad respetando el estado físico del bien
        $item = $asignacion->tipo === 'mobiliario'
            ? \App\Models\Mobiliario::find($asignacion->id_referencia)
            : \App\Models\Dispositivo::find($asignacion->id_referencia);

        if ($item) {
            $item->disponibilidad = $item::calcularDisponibilidad($item->estado, false, false);
            $item->save();
        }

        // Registrar la devolución
        \App\Models\Devolucion::create([
            'asignacion_id' => $request->asignacion_id,
            'fecha_devolucion' => $request->fecha_devolucion,
            'recibido_por' => $request->recibido_por,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('devoluciones.index')->with('success', 'Devolución registrada correctamente.');
    }

    public function show($id)
    {
        $devolucion = Devolucion::with('asignacion')->findOrFail($id);

        return view('devoluciones.show', compact('devolucion'));
    }

    public function pdf(Devolucion $devolucion)
    {
        $asignacion = $devolucion->asignacion;
        $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
            ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
            : Dispositivo::withTrashed()->find($asignacion->id_referencia);

        $pdf = Pdf::loadView('devoluciones.pdf', compact('devolucion', 'asignacion', 'item'));

        return $pdf->stream('Acta_Devolucion_'.$devolucion->id.'.pdf');
    }

    public function destroy(Devolucion $devolucion)
    {
        DB::transaction(function () use ($devolucion): void {
            $asignacion = $devolucion->asignacion;
            $devolucion->delete();

            if ($asignacion) {
                $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
                    ? Mobiliario::find($asignacion->id_referencia)
                    : Dispositivo::find($asignacion->id_referencia);

                if ($item) {
                    // Al borrar la devolución, el bien vuelve a estar asignado a esa persona
                    $item->disponibilidad = $item::DISPONIBILIDAD_ASIGNADO;
                    $item->save();
                }
            }
        });

        return redirect()->route('devoluciones.index')->with('success', 'Devolución eliminada.');
    }

    public function verPublica($uuid)
    {
        $asignacion = Asignacion::where('uuid', $uuid)->firstOrFail();
        $devolucion = \App\Models\Devolucion::where('asignacion_id', $asignacion->id)->firstOrFail();

        $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
            ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
            : Dispositivo::withTrashed()->find($asignacion->id_referencia);

        return view('devoluciones.publica', compact('devolucion', 'asignacion', 'item'));
    }

    public function buscarAsignaciones(Request $request)
    {
        $q = $request->get('q');

        $asignaciones = Asignacion::whereDoesntHave('devolucion') // solo asignaciones sin devolución
            ->where(function ($query) use ($q) {
                $query->whereHas('empleado', function ($sub) use ($q) {
                    $sub->where('nombre_completo', 'like', "%$q%");
                })
                    ->orWhereHas('mobiliario', function ($sub) use ($q) {
                        $sub->where('etiqueta', 'like', "%$q%")
                            ->orWhere('nombre', 'like', "%$q%");
                    })
                    ->orWhereHas('dispositivo', function ($sub) use ($q) {
                        $sub->where('etiqueta', 'like', "%$q%")
                            ->orWhere('nombre', 'like', "%$q%");
                    });
            })
            ->with(['empleado', 'mobiliario', 'dispositivo'])
            ->latest()
            ->get();

        return response()->json($asignaciones);
    }
}
