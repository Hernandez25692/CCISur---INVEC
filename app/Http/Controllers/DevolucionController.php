<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Devolucion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DevolucionController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar'));
        $tipo = $request->input('tipo');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $estadoFiltro = $request->input('estado');

        $query = Devolucion::with([
            'asignacion.empleado',
            'asignacion.mobiliario',
            'asignacion.dispositivo',
        ]);

        if ($buscar !== '') {
            $busquedaEscapada = addcslashes($buscar, '%_\\');
            $query->where(function ($q) use ($busquedaEscapada): void {
                $q->where('recibido_por', 'like', "%{$busquedaEscapada}%")
                    ->orWhere('observaciones', 'like', "%{$busquedaEscapada}%")
                    ->orWhere('id', $busquedaEscapada)
                    ->orWhereHas('asignacion', function ($sub) use ($busquedaEscapada): void {
                        $sub->where('area', 'like', "%{$busquedaEscapada}%")
                            ->orWhereHas('empleado', function ($emp) use ($busquedaEscapada): void {
                                $emp->where('nombre_completo', 'like', "%{$busquedaEscapada}%")
                                    ->orWhere('codigo', 'like', "%{$busquedaEscapada}%")
                                    ->orWhere('identidad', 'like', "%{$busquedaEscapada}%");
                            })
                            ->orWhereHas('mobiliario', function ($mob) use ($busquedaEscapada): void {
                                $mob->where('etiqueta', 'like', "%{$busquedaEscapada}%")
                                    ->orWhere('nombre', 'like', "%{$busquedaEscapada}%");
                            })
                            ->orWhereHas('dispositivo', function ($disp) use ($busquedaEscapada): void {
                                $disp->where('etiqueta', 'like', "%{$busquedaEscapada}%")
                                    ->orWhere('nombre', 'like', "%{$busquedaEscapada}%")
                                    ->orWhere('marca', 'like', "%{$busquedaEscapada}%")
                                    ->orWhere('modelo', 'like', "%{$busquedaEscapada}%")
                                    ->orWhere('n_serie', 'like', "%{$busquedaEscapada}%");
                            });
                    });
            });
        }

        if ($tipo && in_array($tipo, [Asignacion::TIPO_DISPOSITIVO, Asignacion::TIPO_MOBILIARIO], true)) {
            $query->whereHas('asignacion', function ($sub) use ($tipo): void {
                $sub->where('tipo', $tipo);
            });
        }

        if ($fechaInicio) {
            $parsedInicio = rescue(fn () => Carbon::parse($fechaInicio)->startOfDay(), null, false);
            if ($parsedInicio) {
                $query->whereDate('fecha_devolucion', '>=', $parsedInicio);
            }
        }

        if ($fechaFin) {
            $parsedFin = rescue(fn () => Carbon::parse($fechaFin)->endOfDay(), null, false);
            if ($parsedFin) {
                $query->whereDate('fecha_devolucion', '<=', $parsedFin);
            }
        }

        if ($estadoFiltro) {
            $query->where('estado_devuelto', $estadoFiltro);
        }

        $devoluciones = $query->latest('fecha_devolucion')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $metricas = [
            'total' => Devolucion::count(),
            'mes' => Devolucion::whereMonth('fecha_devolucion', now()->month)
                ->whereYear('fecha_devolucion', now()->year)
                ->count(),
            'buen_estado' => Devolucion::whereIn('estado_devuelto', [
                'Nuevo / En perfectas condiciones',
                'Con pequeños detalles / Imperfecciones leves',
                'Usado / Segunda mano',
            ])->count(),
            'no_asignables' => Devolucion::whereIn('estado_devuelto', Dispositivo::ESTADOS_NO_ASIGNABLES)->count(),
        ];

        return view('devoluciones.index', compact('devoluciones', 'metricas'));
    }

    public function create(Request $request)
    {
        $asignaciones = Asignacion::with(['empleado', 'mobiliario', 'dispositivo'])
            ->whereDoesntHave('devolucion')
            ->latest()
            ->get();

        $asignacionesData = $asignaciones->map(function ($a) {
            $bien = $a->tipo === Asignacion::TIPO_MOBILIARIO
                ? ($a->mobiliario ?? Mobiliario::withTrashed()->find($a->id_referencia))
                : ($a->dispositivo ?? Dispositivo::withTrashed()->find($a->id_referencia));

            $identidadRaw = $a->empleado?->identidad ? preg_replace('/[\s\-]+/', '', $a->empleado->identidad) : '';

            $bienEstadoSugerido = ($bien?->estado === 'Nuevo / En perfectas condiciones' || empty($bien?->estado))
                ? 'Usado / Segunda mano'
                : $bien->estado;

            return [
                'id' => $a->id,
                'tipo' => $a->tipo,
                'tipo_label' => ucfirst($a->tipo),
                'destinatario' => $a->destinatario_texto,
                'colaborador_codigo' => $a->empleado?->codigo ?? null,
                'colaborador_identidad' => $a->empleado?->identidad_formateada ?? $a->empleado?->identidad ?? null,
                'colaborador_identidad_raw' => $identidadRaw,
                'es_area_comun' => (bool) $a->es_area_comun,
                'area' => $a->area,
                'bien_nombre' => $bien?->nombre ?? 'N/A',
                'bien_etiqueta' => $bien?->etiqueta ?? 'Sin etiqueta',
                'bien_categoria' => $bien?->tipo ?? 'General',
                'bien_marca' => $bien?->marca ?? null,
                'bien_modelo' => $bien?->modelo ?? null,
                'bien_serie' => $bien?->n_serie ?? null,
                'bien_estado' => $bienEstadoSugerido,
                'fecha_asignacion' => $a->fecha_entrega ? Carbon::parse($a->fecha_entrega)->format('d/m/Y') : 'N/A',
            ];
        });

        $preseleccionadoId = $request->input('asignacion_id');
        $preseleccionado = null;
        if ($preseleccionadoId) {
            $preseleccionado = $asignacionesData->firstWhere('id', (int) $preseleccionadoId);
        }

        $estados = Devolucion::ESTADOS_DEVOLUCION;
        $recibidoPor = auth()->user()?->name ?? 'Administración CCISUR';

        return view('devoluciones.create', compact('asignacionesData', 'estados', 'preseleccionado', 'recibidoPor'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asignacion_id' => ['required', 'exists:asignacions,id'],
            'fecha_devolucion' => ['required', 'date'],
            'estado_devuelto' => ['required', 'string', Rule::in(Devolucion::ESTADOS_DEVOLUCION)],
            'recibido_por' => ['required', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ], [
            'asignacion_id.required' => 'Debe seleccionar una asignación para registrar la devolución.',
            'asignacion_id.exists' => 'La asignación seleccionada no es válida.',
            'fecha_devolucion.required' => 'La fecha de devolución es obligatoria.',
            'estado_devuelto.required' => 'Debe evaluar el estado físico en que se recibe el bien.',
            'estado_devuelto.in' => 'El estado físico seleccionado no es válido para devolución (un bien devuelto ya no puede recibirse como nuevo).',
            'recibido_por.required' => 'El nombre de quien recibe es obligatorio.',
        ]);

        // Verificar si ya fue devuelto previamente
        $yaDevuelto = Devolucion::where('asignacion_id', $validated['asignacion_id'])->exists();
        if ($yaDevuelto) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Esta asignación ya cuenta con un acta de devolución registrada.');
        }

        $devolucion = DB::transaction(function () use ($validated) {
            $asignacion = Asignacion::findOrFail($validated['asignacion_id']);

            $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
                ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
                : Dispositivo::withTrashed()->find($asignacion->id_referencia);

            if ($item) {
                // Actualizar estado físico del bien recibido
                $item->estado = $validated['estado_devuelto'];
                // Recalcular disponibilidad automáticamente (si viene dañado/en reparación queda No Aplica)
                $item->disponibilidad = $item::calcularDisponibilidad($item->estado, false, false);
                $item->save();
            }

            return Devolucion::create([
                'asignacion_id' => $asignacion->id,
                'fecha_devolucion' => $validated['fecha_devolucion'],
                'estado_devuelto' => $validated['estado_devuelto'],
                'recibido_por' => $validated['recibido_por'],
                'observaciones' => $validated['observaciones'] ?? null,
            ]);
        });

        return redirect()->route('devoluciones.show', $devolucion)
            ->with('success', 'Devolución registrada exitosamente. El inventario ha sido actualizado.');
    }

    public function show(Devolucion $devolucion)
    {
        $devolucion->load([
            'asignacion.empleado',
            'asignacion.mobiliario',
            'asignacion.dispositivo',
        ]);

        $asignacion = $devolucion->asignacion;
        $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
            ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
            : Dispositivo::withTrashed()->find($asignacion->id_referencia);

        $correlativo = 'DEV-INV-'.($devolucion->fecha_devolucion ? $devolucion->fecha_devolucion->format('Y') : date('Y')).'-'.str_pad($devolucion->id, 4, '0', STR_PAD_LEFT);

        return view('devoluciones.show', compact('devolucion', 'asignacion', 'item', 'correlativo'));
    }

    public function pdf(Devolucion $devolucion)
    {
        $devolucion->load([
            'asignacion.empleado',
            'asignacion.mobiliario',
            'asignacion.dispositivo',
        ]);

        $asignacion = $devolucion->asignacion;
        $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
            ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
            : Dispositivo::withTrashed()->find($asignacion->id_referencia);

        $correlativo = 'DEV-INV-'.($devolucion->fecha_devolucion ? $devolucion->fecha_devolucion->format('Y') : date('Y')).'-'.str_pad($devolucion->id, 4, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('devoluciones.pdf', compact('devolucion', 'asignacion', 'item', 'correlativo'));

        return $pdf->stream("{$correlativo}.pdf");
    }

    public function destroy(Request $request, Devolucion $devolucion)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Debe ingresar su contraseña de administrador para anular esta devolución.',
        ]);

        if (! Hash::check($request->password, $request->user()->password)) {
            return back()->with('error', 'Contraseña incorrecta. No se pudo anular la devolución.');
        }

        DB::transaction(function () use ($devolucion): void {
            $asignacion = $devolucion->asignacion;
            $devolucion->delete();

            if ($asignacion) {
                $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
                    ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
                    : Dispositivo::withTrashed()->find($asignacion->id_referencia);

                if ($item) {
                    // Al anular la devolución, el bien regresa a estar Asignado a su custodio anterior
                    $item->disponibilidad = $item::DISPONIBILIDAD_ASIGNADO;
                    $item->save();
                }
            }
        });

        return redirect()->route('devoluciones.index')
            ->with('success', 'Acta de devolución anulada. El bien ha retornado a su estado asignado anterior.');
    }
}
