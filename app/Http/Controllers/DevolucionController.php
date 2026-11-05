<?php

namespace App\Http\Controllers;

use App\Exports\DevolucionesExport;
use App\Models\Asignacion;
use App\Models\Devolucion;
use App\Models\Dispositivo;
use App\Models\Mobiliario;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class DevolucionController extends Controller
{
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar'));
        $tipo = $request->input('tipo');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $estadoFiltro = $request->input('estado');
        $estadoActa = $request->input('estado_acta', 'activas');

        $totalAnuladas = Devolucion::onlyTrashed()->count();

        // Determinar alcance según el filtro de actas
        if ($estadoActa === 'anuladas') {
            $query = Devolucion::onlyTrashed()->with([
                'anuladoPor',
                'asignacion.empleado',
                'asignacion.mobiliario',
                'asignacion.dispositivo',
            ]);
        } elseif ($estadoActa === 'todas') {
            $query = Devolucion::withTrashed()->with([
                'anuladoPor',
                'asignacion.empleado',
                'asignacion.mobiliario',
                'asignacion.dispositivo',
            ]);
        } else {
            $query = Devolucion::with([
                'anuladoPor',
                'asignacion.empleado',
                'asignacion.mobiliario',
                'asignacion.dispositivo',
            ]);
        }

        if ($buscar !== '') {
            $busquedaEscapada = addcslashes($buscar, '%_\\');
            $busquedaIdentidad = addcslashes(\App\Models\Empleado::normalizarIdentidad($buscar), '%_\\');

            // Extraer ID numérico si la búsqueda corresponde a correlativos (DEV-INV-YYYY-XXXX, ACT-INV-YYYY-XXXX, INV-DIS-YYYY-XXXX, etc.)
            $idDevolucion = null;
            if (preg_match('/(?:DEV[-_]?INV[-_]?(?:\d{4})?[-_]?0*|^0*)(\d+)/i', $buscar, $m)) {
                $idDevolucion = (int) $m[1];
            } elseif (ctype_digit($buscar)) {
                $idDevolucion = (int) $buscar;
            }

            $idAsignacion = null;
            if (preg_match('/(?:ACT[-_]?INV[-_]?(?:\d{4})?[-_]?0*)(\d+)/i', $buscar, $m)) {
                $idAsignacion = (int) $m[1];
            }

            $idBien = null;
            if (preg_match('/(?:INV[-_]?(?:DIS|MOB)[-_]?(?:\d{4})?[-_]?0*)(\d+)/i', $buscar, $m)) {
                $idBien = (int) $m[1];
            } elseif (ctype_digit($buscar)) {
                $idBien = (int) $buscar;
            }

            $query->where(function (Builder $q) use ($busquedaEscapada, $busquedaIdentidad, $idDevolucion, $idAsignacion, $idBien): void {
                $q->where('recibido_por', 'like', "%{$busquedaEscapada}%")
                    ->orWhere('observaciones', 'like', "%{$busquedaEscapada}%");

                if ($idDevolucion !== null) {
                    $q->orWhere('id', $idDevolucion);
                }

                $q->orWhereHas('asignacion', function (Builder $sub) use ($busquedaEscapada, $busquedaIdentidad, $idAsignacion, $idBien): void {
                    $sub->where('area', 'like', "%{$busquedaEscapada}%");

                    if ($idAsignacion !== null) {
                        $sub->orWhere('id', $idAsignacion);
                    }

                    if ($idBien !== null) {
                        $sub->orWhere('id_referencia', $idBien);
                    }

                    $sub->orWhereHas('empleado', function (Builder $emp) use ($busquedaEscapada, $busquedaIdentidad): void {
                        $emp->where('nombre_completo', 'like', "%{$busquedaEscapada}%")
                            ->orWhere('codigo', 'like', "%{$busquedaEscapada}%")
                            ->orWhere('identidad', 'like', "%{$busquedaEscapada}%");

                        if ($busquedaIdentidad !== '' && $busquedaIdentidad !== $busquedaEscapada) {
                            $emp->orWhere('identidad', 'like', "%{$busquedaIdentidad}%");
                        }
                    })
                        ->orWhereHas('mobiliario', function (Builder $mob) use ($busquedaEscapada): void {
                            $mob->where('etiqueta', 'like', "%{$busquedaEscapada}%")
                                ->orWhere('nombre', 'like', "%{$busquedaEscapada}%")
                                ->orWhere('tipo', 'like', "%{$busquedaEscapada}%");
                        })
                        ->orWhereHas('dispositivo', function (Builder $disp) use ($busquedaEscapada): void {
                            $disp->where('etiqueta', 'like', "%{$busquedaEscapada}%")
                                ->orWhere('nombre', 'like', "%{$busquedaEscapada}%")
                                ->orWhere('tipo', 'like', "%{$busquedaEscapada}%")
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

        return view('devoluciones.index', compact('devoluciones', 'metricas', 'totalAnuladas', 'estadoActa'));
    }

    /**
     * Listar archivo histórico exclusivo de devoluciones anuladas.
     */
    public function bajas(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar'));
        $tipo = $request->input('tipo');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $totalAnuladas = Devolucion::onlyTrashed()->count();

        $query = Devolucion::onlyTrashed()->with([
            'anuladoPor',
            'asignacion.empleado',
            'asignacion.mobiliario',
            'asignacion.dispositivo',
        ]);

        if ($buscar !== '') {
            $busquedaEscapada = addcslashes($buscar, '%_\\');
            $busquedaIdentidad = addcslashes(\App\Models\Empleado::normalizarIdentidad($buscar), '%_\\');

            $query->where(function (Builder $q) use ($busquedaEscapada, $busquedaIdentidad): void {
                $q->where('recibido_por', 'like', "%{$busquedaEscapada}%")
                    ->orWhere('observaciones', 'like', "%{$busquedaEscapada}%")
                    ->orWhere('motivo_anulacion', 'like', "%{$busquedaEscapada}%")
                    ->orWhereHas('anuladoPor', function (Builder $user) use ($busquedaEscapada): void {
                        $user->where('name', 'like', "%{$busquedaEscapada}%");
                    })
                    ->orWhereHas('asignacion', function (Builder $sub) use ($busquedaEscapada, $busquedaIdentidad): void {
                        $sub->where('area', 'like', "%{$busquedaEscapada}%")
                            ->orWhereHas('empleado', function (Builder $emp) use ($busquedaEscapada, $busquedaIdentidad): void {
                                $emp->where('nombre_completo', 'like', "%{$busquedaEscapada}%")
                                    ->orWhere('codigo', 'like', "%{$busquedaEscapada}%")
                                    ->orWhere('identidad', 'like', "%{$busquedaEscapada}%");

                                if ($busquedaIdentidad !== '' && $busquedaIdentidad !== $busquedaEscapada) {
                                    $emp->orWhere('identidad', 'like', "%{$busquedaIdentidad}%");
                                }
                            })
                            ->orWhereHas('mobiliario', function (Builder $mob) use ($busquedaEscapada): void {
                                $mob->where('etiqueta', 'like', "%{$busquedaEscapada}%")
                                    ->orWhere('nombre', 'like', "%{$busquedaEscapada}%");
                            })
                            ->orWhereHas('dispositivo', function (Builder $disp) use ($busquedaEscapada): void {
                                $disp->where('etiqueta', 'like', "%{$busquedaEscapada}%")
                                    ->orWhere('nombre', 'like', "%{$busquedaEscapada}%")
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
                $query->whereDate('deleted_at', '>=', $parsedInicio);
            }
        }

        if ($fechaFin) {
            $parsedFin = rescue(fn () => Carbon::parse($fechaFin)->endOfDay(), null, false);
            if ($parsedFin) {
                $query->whereDate('deleted_at', '<=', $parsedFin);
            }
        }

        $devoluciones = $query->latest('deleted_at')->paginate(10)->withQueryString();

        return view('devoluciones.bajas', compact('devoluciones', 'totalAnuladas', 'buscar', 'tipo'));
    }

    /**
     * Exportar devoluciones a Excel con los filtros aplicados.
     */
    public function exportar(Request $request)
    {
        return Excel::download(new DevolucionesExport($request), 'reporte_devoluciones.xlsx');
    }

    /**
     * Exportar archivo exclusivo de devoluciones anuladas a Excel.
     */
    public function exportarBajas(Request $request)
    {
        $request->merge(['estado_acta' => 'anuladas']);

        return Excel::download(new DevolucionesExport($request), 'devoluciones_anuladas.xlsx');
    }

    public function create(Request $request)
    {
        Gate::authorize('admin');

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
        Gate::authorize('admin');

        $validated = $request->validate([
            'asignacion_id' => [
                'required',
                'exists:asignacions,id',
                Rule::unique('devoluciones', 'asignacion_id')->withoutTrashed(),
            ],
            'fecha_devolucion' => ['required', 'date'],
            'estado_devuelto' => ['required', 'string', Rule::in(Devolucion::ESTADOS_DEVOLUCION)],
            'recibido_por' => ['required', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ], [
            'asignacion_id.required' => 'Debe seleccionar una asignación para registrar la devolución.',
            'asignacion_id.exists' => 'La asignación seleccionada no es válida.',
            'asignacion_id.unique' => 'Esta asignación ya cuenta con un acta de devolución registrada.',
            'fecha_devolucion.required' => 'La fecha de devolución es obligatoria.',
            'estado_devuelto.required' => 'Debe evaluar el estado físico en que se recibe el bien.',
            'estado_devuelto.in' => 'El estado físico seleccionado no es válido para devolución (un bien devuelto ya no puede recibirse como nuevo).',
            'recibido_por.required' => 'El nombre de quien recibe es obligatorio.',
        ]);

        try {
            $devolucion = DB::transaction(function () use ($validated) {
                $asignacion = Asignacion::where('id', $validated['asignacion_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // Verificación atómica dentro del bloqueo por si hubo concurrencia
                if ($asignacion->devolucion()->exists()) {
                    throw new \RuntimeException('Esta asignación ya cuenta con un acta de devolución registrada.');
                }

                $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
                    ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
                    : Dispositivo::withTrashed()->find($asignacion->id_referencia);

                // Capturar el estado físico previo antes de registrar la devolución
                $estadoAnterior = $item?->estado ?? $asignacion->estado_al_asignar ?? null;

                if ($item) {
                    // Actualizar estado físico del bien recibido
                    $item->estado = $validated['estado_devuelto'];
                    // Recalcular disponibilidad automáticamente (si viene dañado/en reparación queda No Aplica; si era de área común conserva su condición)
                    $item->disponibilidad = $item::calcularDisponibilidad($item->estado, (bool) $asignacion->es_area_comun, false);
                    $item->save();
                }

                return Devolucion::create([
                    'asignacion_id' => $asignacion->id,
                    'fecha_devolucion' => $validated['fecha_devolucion'],
                    'estado_devuelto' => $validated['estado_devuelto'],
                    'estado_anterior' => $estadoAnterior,
                    'recibido_por' => $validated['recibido_por'],
                    'observaciones' => $validated['observaciones'] ?? null,
                ]);
            });
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e instanceof \RuntimeException ? $e->getMessage() : 'No se pudo registrar la devolución debido a un conflicto de concurrencia.');
        }

        return redirect()->route('devoluciones.show', $devolucion)
            ->with('success', 'Devolución registrada exitosamente. El inventario ha sido actualizado.');
    }

    public function show(Devolucion $devolucion)
    {
        $devolucion->load([
            'anuladoPor',
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
        Gate::authorize('admin');

        $validated = $request->validate([
            'password' => ['required', 'string'],
            'motivo_anulacion' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'password.required' => 'Debe ingresar su contraseña de administrador para anular esta devolución.',
            'motivo_anulacion.required' => 'Debe ingresar el motivo de anulación de la devolución.',
            'motivo_anulacion.min' => 'El motivo de anulación debe contener al menos 5 caracteres.',
        ]);

        if (! Hash::check($validated['password'], $request->user()->password)) {
            return back()->with('error', 'Contraseña incorrecta. No se pudo anular la devolución.');
        }

        DB::transaction(function () use ($devolucion, $request, $validated): void {
            $asignacion = $devolucion->asignacion;

            $devolucion->motivo_anulacion = $validated['motivo_anulacion'];
            $devolucion->anulado_por_user_id = $request->user()->id;
            $devolucion->save();
            $devolucion->delete();

            if ($asignacion) {
                $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
                    ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
                    : Dispositivo::withTrashed()->find($asignacion->id_referencia);

                if ($item) {
                    // Restaurar el estado físico original que tenía el bien antes de registrar la devolución
                    if (! empty($devolucion->estado_anterior)) {
                        $item->estado = $devolucion->estado_anterior;
                    } elseif (! empty($asignacion->estado_al_asignar)) {
                        $item->estado = $asignacion->estado_al_asignar;
                    }

                    // Al anular la devolución, el bien regresa a estar Asignado a su custodio anterior
                    $item->disponibilidad = $item::DISPONIBILIDAD_ASIGNADO;
                    $item->save();
                }
            }
        });

        return redirect()->route('devoluciones.index')
            ->with('success', 'Acta de devolución anulada. El bien ha retornado a su estado asignado anterior.');
    }

    /**
     * Reactivar una devolución anulada por error previo.
     */
    public function restore(Request $request, int $id): RedirectResponse
    {
        Gate::authorize('admin');

        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Debe ingresar su contraseña de administrador para reactivar esta devolución.',
        ]);

        if (! Hash::check($request->password, $request->user()->password)) {
            return back()->with('error', 'Contraseña incorrecta. No se pudo reactivar la devolución.');
        }

        $devolucion = Devolucion::onlyTrashed()->findOrFail($id);
        $asignacion = Asignacion::withTrashed()->findOrFail($devolucion->asignacion_id);

        if ($asignacion->trashed()) {
            return back()->with('error', 'No se puede reactivar: la asignación asociada se encuentra anulada. Primero debe reactivar la asignación.');
        }

        if ($asignacion->devolucion()->exists()) {
            return back()->with('error', 'No se puede reactivar: esta asignación ya cuenta con otra acta de devolución activa.');
        }

        DB::transaction(function () use ($devolucion, $asignacion): void {
            $devolucion->motivo_anulacion = null;
            $devolucion->anulado_por_user_id = null;
            $devolucion->save();
            $devolucion->restore();

            $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
                ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
                : Dispositivo::withTrashed()->find($asignacion->id_referencia);

            if ($item) {
                $item->estado = $devolucion->estado_devuelto;
                $item->disponibilidad = $item::calcularDisponibilidad($item->estado, (bool) $asignacion->es_area_comun, false);
                $item->save();
            }
        });

        return redirect()->route('devoluciones.show', $devolucion->id)
            ->with('success', 'Acta de devolución reactivada exitosamente. El inventario ha sido actualizado.');
    }
}
