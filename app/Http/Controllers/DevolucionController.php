<?php

namespace App\Http\Controllers;

use App\Exports\DevolucionesExport;
use App\Models\Asignacion;
use App\Models\Devolucion;
use App\Models\Dispositivo;
use App\Models\Empleado;
use App\Models\Mobiliario;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $sede = $request->input('sede');
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

        if ($sede && in_array($sede, \App\Models\Empleado::UBICACIONES, true)) {
            $query->whereHas('asignacion', function ($sub) use ($sede): void {
                $sub->where(function ($sub2) use ($sede): void {
                    $sub2->whereHas('empleado', fn ($emp) => $emp->where('ubicacion', $sede))
                        ->orWhereHas('mobiliario', fn ($mob) => $mob->where('ubicacion', $sede))
                        ->orWhereHas('dispositivo', fn ($disp) => $disp->where('ubicacion', $sede));
                });
            });
        }

        $parsedInicio = $fechaInicio ? rescue(fn () => Carbon::parse($fechaInicio)->startOfDay(), null, false) : null;
        $parsedFin = $fechaFin ? rescue(fn () => Carbon::parse($fechaFin)->endOfDay(), null, false) : null;

        if ($parsedInicio && $parsedFin && $parsedInicio->gt($parsedFin)) {
            [$parsedInicio, $parsedFin] = [$parsedFin->copy()->startOfDay(), $parsedInicio->copy()->endOfDay()];
        }

        if ($parsedInicio) {
            $query->whereDate('fecha_devolucion', '>=', $parsedInicio);
        }

        if ($parsedFin) {
            $query->whereDate('fecha_devolucion', '<=', $parsedFin);
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
            'buen_estado' => Devolucion::where('estado_devuelto', 'Usado / Segunda mano')->count(),
            'no_asignables' => Devolucion::whereIn('estado_devuelto', Dispositivo::ESTADOS_NO_ASIGNABLES)->count(),
        ];

        return view('devoluciones.index', compact('devoluciones', 'metricas', 'totalAnuladas', 'estadoActa', 'sede'));
    }

    /**
     * Listar archivo histórico exclusivo de devoluciones anuladas.
     */
    public function bajas(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar'));
        $tipo = $request->input('tipo');
        $sede = $request->input('sede');
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

        if ($sede && in_array($sede, \App\Models\Empleado::UBICACIONES, true)) {
            $query->whereHas('asignacion', function ($sub) use ($sede): void {
                $sub->where(function ($sub2) use ($sede): void {
                    $sub2->whereHas('empleado', fn ($emp) => $emp->where('ubicacion', $sede))
                        ->orWhereHas('mobiliario', fn ($mob) => $mob->where('ubicacion', $sede))
                        ->orWhereHas('dispositivo', fn ($disp) => $disp->where('ubicacion', $sede));
                });
            });
        }

        $parsedInicio = $fechaInicio ? rescue(fn () => Carbon::parse($fechaInicio)->startOfDay(), null, false) : null;
        $parsedFin = $fechaFin ? rescue(fn () => Carbon::parse($fechaFin)->endOfDay(), null, false) : null;

        if ($parsedInicio && $parsedFin && $parsedInicio->gt($parsedFin)) {
            [$parsedInicio, $parsedFin] = [$parsedFin->copy()->startOfDay(), $parsedInicio->copy()->endOfDay()];
        }

        if ($parsedInicio) {
            $query->whereDate('deleted_at', '>=', $parsedInicio);
        }

        if ($parsedFin) {
            $query->whereDate('deleted_at', '<=', $parsedFin);
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

        $asignaciones = Asignacion::with([
            'empleado',
            'mobiliario.mantenimientoActivo',
            'dispositivo.mantenimientoActivo',
        ])
            ->whereDoesntHave('devolucion')
            ->latest()
            ->get();

        $asignacionesData = $asignaciones->map(function ($a) {
            $bien = $a->tipo === Asignacion::TIPO_MOBILIARIO
                ? ($a->mobiliario ?? Mobiliario::withTrashed()->find($a->id_referencia))
                : ($a->dispositivo ?? Dispositivo::withTrashed()->find($a->id_referencia));

            $enMantenimiento = $bien?->relationLoaded('mantenimientoActivo')
                ? ($bien->mantenimientoActivo !== null)
                : ($bien?->mantenimientoActivo()->exists() ?? false);

            $identidadRaw = $a->empleado?->identidad ? preg_replace('/[\s\-]+/', '', $a->empleado->identidad) : '';

            $estadosDevolucionTipo = $a->tipo === Asignacion::TIPO_DISPOSITIVO
                ? Devolucion::ESTADOS_DEVOLUCION_DISPOSITIVO
                : Devolucion::ESTADOS_DEVOLUCION_MOBILIARIO;

            $bienEstadoSugerido = in_array($bien?->estado, $estadosDevolucionTipo, true)
                ? $bien->estado
                : 'Usado / Segunda mano';

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
                'en_mantenimiento' => $enMantenimiento,
                'fecha_asignacion' => $a->fecha_entrega ? Carbon::parse($a->fecha_entrega)->format('d/m/Y') : 'N/A',
                'fecha_asignacion_raw' => $a->fecha_entrega ? Carbon::parse($a->fecha_entrega)->format('Y-m-d') : null,
            ];
        })
            ->filter(fn ($item) => ! $item['en_mantenimiento'])
            ->values();

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

        $asignacionPrevia = Asignacion::find($request->input('asignacion_id'));
        $fechaEntregaRaw = $asignacionPrevia?->fecha_entrega?->format('Y-m-d');
        $fechaEntregaFormateada = $asignacionPrevia?->fecha_entrega?->format('d/m/Y');

        $reglasFecha = ['required', 'date', 'before_or_equal:today'];
        if ($fechaEntregaRaw) {
            $reglasFecha[] = 'after_or_equal:'.$fechaEntregaRaw;
        }

        $estadosPermitidos = $asignacionPrevia?->tipo === Asignacion::TIPO_DISPOSITIVO
            ? Devolucion::ESTADOS_DEVOLUCION_DISPOSITIVO
            : Devolucion::ESTADOS_DEVOLUCION_MOBILIARIO;

        $validated = $request->validate([
            'asignacion_id' => [
                'required',
                'exists:asignacions,id',
                Rule::unique('devoluciones', 'asignacion_id')->withoutTrashed(),
            ],
            'fecha_devolucion' => $reglasFecha,
            'estado_devuelto' => ['required', 'string', Rule::in($estadosPermitidos)],
            'recibido_por' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ], [
            'asignacion_id.required' => 'Debe seleccionar una asignación para registrar la devolución.',
            'asignacion_id.exists' => 'La asignación seleccionada no es válida.',
            'asignacion_id.unique' => 'Esta asignación ya cuenta con un acta de devolución registrada.',
            'fecha_devolucion.required' => 'La fecha de devolución es obligatoria.',
            'fecha_devolucion.date' => 'La fecha de devolución debe ser una fecha válida.',
            'fecha_devolucion.before_or_equal' => 'La fecha de devolución no puede ser una fecha futura.',
            'fecha_devolucion.after_or_equal' => $fechaEntregaFormateada
                ? "La fecha de devolución no puede ser anterior a la fecha en que se entregó el bien ({$fechaEntregaFormateada})."
                : 'La fecha de devolución no puede ser anterior a la fecha de entrega del bien.',
            'estado_devuelto.required' => 'Debe evaluar el estado físico en que se recibe el bien.',
            'estado_devuelto.in' => 'El estado físico seleccionado no es válido para devolución (un bien devuelto ya no puede recibirse como nuevo).',
            'recibido_por.required' => 'El nombre de quien recibe es obligatorio.',
        ]);

        $validated['recibido_por'] = Auth::user()->name;

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
                    ? Mobiliario::withTrashed()->lockForUpdate()->find($asignacion->id_referencia)
                    : Dispositivo::withTrashed()->lockForUpdate()->find($asignacion->id_referencia);

                if ($item && $item->mantenimientoActivo()->exists()) {
                    $etiquetaBien = $item::ETIQUETA_BIEN ?? 'bien';
                    throw new \RuntimeException("Este {$etiquetaBien} ({$item->etiqueta}) se encuentra actualmente en taller / mantenimiento técnico. Para registrar su devolución por daño irreparable, debe finalizar el mantenimiento correspondiente desde su Hoja de Vida.");
                }

                // Capturar el estado físico previo antes de registrar la devolución
                $estadoAnterior = $item?->estado ?? null;

                // Crear primero el acta formal de devolución para obtener su ID (🟡-1)
                $devolucion = Devolucion::create([
                    'asignacion_id' => $asignacion->id,
                    'fecha_devolucion' => $validated['fecha_devolucion'],
                    'estado_devuelto' => $validated['estado_devuelto'],
                    'estado_anterior' => $estadoAnterior,
                    'recibido_por' => $validated['recibido_por'],
                    'observaciones' => $validated['observaciones'] ?? null,
                ]);

                if ($item) {
                    $item->estado = $validated['estado_devuelto'];

                    // Preservar condición de área común de la asignación original (🟡-1 & 🟡-2)
                    $esAreaComun = (bool) $asignacion->es_area_comun;
                    $item->disponibilidad = $item::calcularDisponibilidad($item->estado, $esAreaComun, false);
                    $item->save();

                    // Si el bien se recibe en estado Dañado / Defectuoso, pasa directamente al archivo de bajas
                    if ($validated['estado_devuelto'] === 'Dañado / Defectuoso') {
                        $motivoDevolucion = ! empty($validated['observaciones'])
                            ? 'Devolución por daño: '.$validated['observaciones']
                            : 'Dado de baja automáticamente por recepción en estado Dañado / Defectuoso';

                        $item->update([
                            'motivo_baja' => \Illuminate\Support\Str::limit($motivoDevolucion, 480),
                            'baja_por_user_id' => Auth::id(),
                            'disponibilidad' => $item::DISPONIBILIDAD_NO_APLICA,
                        ]);

                        if (! $item->trashed()) {
                            $item->delete();
                        }
                    } elseif ($validated['estado_devuelto'] === 'En reparación / En revisión') {
                        // Crear registro formal de mantenimiento vinculado al acta de devolución (🟡-1)
                        $this->registrarMantenimientoPorDevolucion(
                            $item,
                            $asignacion,
                            $devolucion,
                            $estadoAnterior,
                            $esAreaComun,
                            $validated['observaciones'] ?? null
                        );
                    }
                }

                return $devolucion;
            });
        } catch (\Throwable $e) {
            report($e);

            $mensajeError = $e instanceof \RuntimeException
                ? $e->getMessage()
                : ($e instanceof \Illuminate\Database\QueryException
                    ? 'Ocurrió un error en la base de datos al registrar la devolución. Verifique los datos ingresados.'
                    : 'No se pudo registrar la devolución. Intente nuevamente.');

            return redirect()->back()
                ->withInput()
                ->with('error', $mensajeError);
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
            'asignacion.cambiosHardwareVigentes',
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
            'asignacion.cambiosHardwareVigentes',
        ]);

        $asignacion = $devolucion->asignacion;
        $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
            ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
            : Dispositivo::withTrashed()->find($asignacion->id_referencia);

        $correlativo = 'DEV-INV-'.($devolucion->fecha_devolucion ? $devolucion->fecha_devolucion->format('Y') : date('Y')).'-'.str_pad($devolucion->id, 4, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('devoluciones.pdf', compact('devolucion', 'asignacion', 'item', 'correlativo'));

        return $pdf->stream("{$correlativo}.pdf");
    }

    public function destroy(Request $request, Devolucion $devolucion): RedirectResponse
    {
        Gate::authorize('admin');

        if (! $devolucion->puedeSerAnuladaPor($request->user())) {
            return back()->with('error', $devolucion->motivoNoAnulable($request->user()) ?? 'Acción denegada: El plazo de gracia para anulación ha expirado.');
        }

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

        try {
            $error = DB::transaction(function () use ($devolucion, $request, $validated): ?string {
                // Orden canónico de locks: Asignación -> Item -> Devolución (🟡-1)
                $asignacion = Asignacion::where('id', $devolucion->asignacion_id)->lockForUpdate()->first();

                if (! $asignacion) {
                    return 'No se puede anular la devolución: la asignación asociada no existe.';
                }

                $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
                    ? Mobiliario::withTrashed()->where('id', $asignacion->id_referencia)->lockForUpdate()->first()
                    : Dispositivo::withTrashed()->where('id', $asignacion->id_referencia)->lockForUpdate()->first();

                if (! $item) {
                    return 'No se puede anular la devolución: el bien asociado ya no existe en el inventario.';
                }

                $devolucionBloqueada = Devolucion::where('id', $devolucion->id)->lockForUpdate()->firstOrFail();

                if (! $devolucionBloqueada->puedeSerAnuladaPor($request->user())) {
                    return $devolucionBloqueada->motivoNoAnulable($request->user()) ?? 'Acción denegada: El plazo de gracia para anulación ha expirado.';
                }

                if ($item->estaAsignado()) {
                    return 'No se puede anular esta devolución: el bien ya se encuentra actualmente asignado en otra asignación activa. Debe resolver esa asignación antes de poder reactivar la anterior.';
                }

                // Validar si el bien está en taller o mantenimiento vinculado (🟡-1 & 🟡-3)
                $mantenimientoVinculado = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
                    ? \App\Models\MantenimientoMobiliario::where(function ($q) use ($devolucionBloqueada, $asignacion) {
                        $q->where('devolucion_id', $devolucionBloqueada->id)
                            ->orWhere('asignacion_id', $asignacion->id);
                    })->whereIn('estado', [
                        \App\Models\MantenimientoMobiliario::ESTADO_EN_PROCESO,
                        \App\Models\MantenimientoMobiliario::ESTADO_IRREPARABLE,
                    ])->first()
                    : \App\Models\MantenimientoDispositivo::where(function ($q) use ($devolucionBloqueada, $asignacion) {
                        $q->where('devolucion_id', $devolucionBloqueada->id)
                            ->orWhere('asignacion_id', $asignacion->id);
                    })->whereIn('estado', [
                        \App\Models\MantenimientoDispositivo::ESTADO_EN_PROCESO,
                        \App\Models\MantenimientoDispositivo::ESTADO_IRREPARABLE,
                    ])->first();

                if ($mantenimientoVinculado) {
                    $mantenimientoVinculado->update([
                        'estado' => 'cancelado',
                        'fecha_fin' => now(),
                        'solucion_aplicada' => 'Mantenimiento cancelado automáticamente por anulación del acta de devolución.',
                    ]);
                } elseif ($item && $item->estaEnMantenimiento()) {
                    return 'No se puede anular la devolución: el bien se encuentra actualmente en taller o mantenimiento activo.';
                }

                // Validar coherencia de sede y colaborador
                if (! $asignacion->es_area_comun && $asignacion->empleado_id) {
                    $empleado = Empleado::withTrashed()->where('id', $asignacion->empleado_id)->lockForUpdate()->first();

                    if (! $empleado || $empleado->trashed()) {
                        return 'No se puede anular esta devolución: el colaborador asociado a esta asignación fue dado de baja o no existe.';
                    }

                    if ($item->ubicacion !== $empleado->ubicacion) {
                        return "No se puede anular esta devolución: el bien pertenece a la Sede {$item->ubicacion} y el colaborador a la Sede {$empleado->ubicacion}.";
                    }
                }

                // Si el bien había sido enviado al archivo de bajas por devolución dañada, restaurarlo (🟡-3)
                if ($item && $item->trashed()) {
                    $item->restore();
                    $item->update([
                        'motivo_baja' => null,
                        'baja_por_user_id' => null,
                    ]);
                }

                $devolucionBloqueada->motivo_anulacion = $validated['motivo_anulacion'];
                $devolucionBloqueada->anulado_por_user_id = $request->user()->id;
                $devolucionBloqueada->save();
                $devolucionBloqueada->delete();

                // Restaurar el estado físico original que tenía el bien antes de registrar la devolución
                if (! empty($devolucionBloqueada->estado_anterior)) {
                    $item->estado = $devolucionBloqueada->estado_anterior;
                }

                // Al anular la devolución, el bien regresa a estar Asignado a su custodio anterior
                $item->disponibilidad = $item::DISPONIBILIDAD_ASIGNADO;
                $item->save();

                return null;
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Ocurrió un error inesperado al anular la devolución. Por favor intente nuevamente.');
        }

        if ($error) {
            return back()->with('error', $error);
        }

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

        try {
            $error = DB::transaction(function () use ($id): ?string {
                // Orden canónico de locks: Asignación -> Item -> Devolución (🟡-1)
                $devolucionPrevia = Devolucion::onlyTrashed()->findOrFail($id);
                $asignacion = Asignacion::withTrashed()->where('id', $devolucionPrevia->asignacion_id)->lockForUpdate()->firstOrFail();

                if ($asignacion->trashed()) {
                    return 'No se puede reactivar: la asignación asociada se encuentra anulada. Primero debe reactivar la asignación.';
                }

                $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
                    ? Mobiliario::withTrashed()->where('id', $asignacion->id_referencia)->lockForUpdate()->first()
                    : Dispositivo::withTrashed()->where('id', $asignacion->id_referencia)->lockForUpdate()->first();

                if ($item && $item->estaEnMantenimiento()) {
                    return 'No se puede reactivar la devolución: el bien se encuentra actualmente en taller o mantenimiento activo.';
                }

                if ($item && $item->trashed()) {
                    return 'No se puede reactivar la devolución: el bien se encuentra en el archivo de bajas. Debe reactivarlo formalmente antes.';
                }

                $devolucion = Devolucion::onlyTrashed()->where('id', $id)->lockForUpdate()->firstOrFail();

                if ($asignacion->devolucion()->exists()) {
                    return 'No se puede reactivar: esta asignación ya cuenta con otra acta de devolución activa.';
                }

                $adminName = Auth::user()?->name ?? 'Administración';
                $anuladoPorName = $devolucion->anuladoPor()->withTrashed()->first()?->name ?? ($devolucion->anuladoPor?->name ?? 'Administración');
                $fechaReactivacion = now()->format('d/m/Y h:i A');
                $notaReactivacion = "[Reactivación {$fechaReactivacion} por {$adminName}]: Acta reactivada tras haber sido anulada previamente por {$anuladoPorName}.";
                $devolucion->observaciones = trim(($devolucion->observaciones ?? '')."\n".$notaReactivacion);
                $devolucion->motivo_anulacion = null;
                $devolucion->anulado_por_user_id = null;
                $devolucion->save();
                $devolucion->restore();

                if ($item) {
                    $estadoPrevio = $item->estado;
                    $item->estado = $devolucion->estado_devuelto;
                    $item->disponibilidad = $item::calcularDisponibilidad($item->estado, (bool) $asignacion->es_area_comun, false);
                    $item->save();

                    // Si el acta de devolución lo remitió a reparación, recrear el ticket de taller correspondiente (🟡-1)
                    if ($devolucion->estado_devuelto === 'En reparación / En revisión') {
                        $this->registrarMantenimientoPorDevolucion(
                            $item,
                            $asignacion,
                            $devolucion,
                            $estadoPrevio,
                            (bool) $asignacion->es_area_comun,
                            'Reactivación de acta de devolución'
                        );
                    }
                }

                return null;
            });
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Ocurrió un error inesperado al reactivar la devolución. Por favor intente nuevamente.');
        }

        if ($error) {
            return back()->with('error', $error);
        }

        return redirect()->route('devoluciones.show', $id)
            ->with('success', 'Acta de devolución reactivada exitosamente. El inventario ha sido actualizado.');
    }

    /**
     * Registra un ticket de mantenimiento vinculado a un acta de devolución cuando el bien ingresa a taller.
     */
    private function registrarMantenimientoPorDevolucion(
        Dispositivo|Mobiliario $item,
        Asignacion $asignacion,
        Devolucion $devolucion,
        string $estadoAnterior,
        bool $esAreaComun,
        ?string $motivo = null
    ): void {
        $motivoFalla = ! empty($motivo)
            ? 'Ingresado a taller tras recepción de devolución: '.$motivo
            : (! empty($devolucion->observaciones)
                ? 'Ingresado a taller tras recepción de devolución: '.$devolucion->observaciones
                : 'Revisión técnica requerida tras recepción de devolución');

        if ($asignacion->tipo === Asignacion::TIPO_MOBILIARIO) {
            \App\Models\MantenimientoMobiliario::create([
                'mobiliario_id' => $item->id,
                'user_id' => Auth::id(),
                'asignacion_id' => $asignacion->id,
                'empleado_id' => $asignacion->empleado_id,
                'tipo' => \App\Models\MantenimientoMobiliario::TIPO_MECANICA,
                'motivo_falla' => $motivoFalla,
                'estado' => \App\Models\MantenimientoMobiliario::ESTADO_EN_PROCESO,
                'fecha_inicio' => now(),
                'estado_fisico_anterior' => $estadoAnterior,
                'es_area_comun' => $esAreaComun,
                'devolucion_id' => $devolucion->id,
            ]);
        } else {
            \App\Models\MantenimientoDispositivo::create([
                'dispositivo_id' => $item->id,
                'user_id' => Auth::id(),
                'asignacion_id' => $asignacion->id,
                'empleado_id' => $asignacion->empleado_id,
                'tipo' => \App\Models\MantenimientoDispositivo::TIPO_DIAGNOSTICO,
                'motivo_falla' => $motivoFalla,
                'estado' => \App\Models\MantenimientoDispositivo::ESTADO_EN_PROCESO,
                'fecha_inicio' => now(),
                'estado_fisico_anterior' => $estadoAnterior,
                'es_area_comun' => $esAreaComun,
                'devolucion_id' => $devolucion->id,
            ]);
        }
    }
}
