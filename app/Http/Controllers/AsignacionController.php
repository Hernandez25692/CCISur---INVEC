<?php

namespace App\Http\Controllers;

use App\Exports\AsignacionesExport;
use App\Models\Asignacion;
use App\Models\Dispositivo;
use App\Models\Empleado;
use App\Models\Mobiliario;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;

class AsignacionController extends Controller
{
    /**
     * Listar asignaciones según el filtro de estado (activas por defecto, o anuladas/todas).
     */
    public function index(Request $request): View
    {
        $tipo = $request->input('tipo');
        $buscar = trim((string) $request->input('buscar'));
        $estadoActa = $request->input('estado_acta', 'activas');

        // Métricas de inventario activo y archivo
        $totalActivas = Asignacion::activas()->count();
        $totalAnuladas = Asignacion::onlyTrashed()->count();
        $totalDispositivos = Asignacion::activas()->where('tipo', Asignacion::TIPO_DISPOSITIVO)->count();
        $totalMobiliario = Asignacion::activas()->where('tipo', Asignacion::TIPO_MOBILIARIO)->count();
        $totalCustodios = Asignacion::activas()->whereNotNull('empleado_id')->distinct('empleado_id')->count('empleado_id');

        // Determinar alcance de la consulta
        if ($estadoActa === 'anuladas') {
            $query = Asignacion::onlyTrashed()->with(['anuladoPor', 'empleado', 'mobiliario', 'dispositivo']);
        } elseif ($estadoActa === 'todas') {
            $query = Asignacion::withTrashed()->with(['anuladoPor', 'empleado', 'mobiliario', 'dispositivo']);
        } else {
            $query = Asignacion::activas()->with(['empleado', 'mobiliario', 'dispositivo']);
        }

        // Filtro por tipo de bien
        if ($tipo && in_array($tipo, [Asignacion::TIPO_MOBILIARIO, Asignacion::TIPO_DISPOSITIVO], true)) {
            $query->where('tipo', $tipo);
        }

        // Filtro por rango de fechas válidas de forma segura (sin errores 500 por manipulación)
        $fechaInicio = rescue(fn () => $request->filled('fecha_inicio') ? $request->date('fecha_inicio') : null);
        if ($fechaInicio) {
            $query->whereDate('fecha_entrega', '>=', $fechaInicio);
        }

        $fechaFin = rescue(fn () => $request->filled('fecha_fin') ? $request->date('fecha_fin') : null);
        if ($fechaFin) {
            $query->whereDate('fecha_entrega', '<=', $fechaFin);
        }

        // Búsqueda inteligente con escape de comodines
        if ($buscar !== '') {
            $buscarSafe = addcslashes($buscar, '%_\\');
            $buscarIdentidad = addcslashes(\App\Models\Empleado::normalizarIdentidad($buscar), '%_\\');

            $query->where(function ($q) use ($buscarSafe, $buscarIdentidad): void {
                $q->whereHas('empleado', function ($sub) use ($buscarSafe, $buscarIdentidad): void {
                    $sub->where('nombre_completo', 'like', "%{$buscarSafe}%")
                        ->orWhere('identidad', 'like', "%{$buscarSafe}%")
                        ->orWhere('codigo', 'like', "%{$buscarSafe}%");

                    if ($buscarIdentidad !== '' && $buscarIdentidad !== $buscarSafe) {
                        $sub->orWhere('identidad', 'like', "%{$buscarIdentidad}%");
                    }
                })
                    ->orWhere('area', 'like', "%{$buscarSafe}%")
                    ->orWhere('motivo_anulacion', 'like', "%{$buscarSafe}%")
                    ->orWhereHas('mobiliario', function ($sub) use ($buscarSafe): void {
                        $sub->where('etiqueta', 'like', "%{$buscarSafe}%")
                            ->orWhere('nombre', 'like', "%{$buscarSafe}%");
                    })
                    ->orWhereHas('dispositivo', function ($sub) use ($buscarSafe): void {
                        $sub->where('etiqueta', 'like', "%{$buscarSafe}%")
                            ->orWhere('nombre', 'like', "%{$buscarSafe}%")
                            ->orWhere('n_serie', 'like', "%{$buscarSafe}%");
                    });
            });
        }

        $asignaciones = $query->latest('id')->paginate(15)->withQueryString();

        return view('asignaciones.index', compact(
            'asignaciones',
            'tipo',
            'buscar',
            'estadoActa',
            'totalActivas',
            'totalAnuladas',
            'totalDispositivos',
            'totalMobiliario',
            'totalCustodios'
        ));
    }

    /**
     * Listar archivo histórico exclusivo de asignaciones anuladas (dadas de baja).
     */
    public function bajas(Request $request): View
    {
        $tipo = $request->input('tipo');
        $buscar = trim((string) $request->input('buscar'));
        $totalAnuladas = Asignacion::onlyTrashed()->count();

        $query = Asignacion::onlyTrashed()->with(['anuladoPor', 'empleado', 'mobiliario', 'dispositivo']);

        if ($tipo && in_array($tipo, [Asignacion::TIPO_MOBILIARIO, Asignacion::TIPO_DISPOSITIVO], true)) {
            $query->where('tipo', $tipo);
        }

        $fechaInicio = rescue(fn () => $request->filled('fecha_inicio') ? $request->date('fecha_inicio') : null);
        if ($fechaInicio) {
            $query->whereDate('deleted_at', '>=', $fechaInicio);
        }

        $fechaFin = rescue(fn () => $request->filled('fecha_fin') ? $request->date('fecha_fin') : null);
        if ($fechaFin) {
            $query->whereDate('deleted_at', '<=', $fechaFin);
        }

        if ($buscar !== '') {
            $buscarSafe = addcslashes($buscar, '%_\\');
            $buscarIdentidad = addcslashes(\App\Models\Empleado::normalizarIdentidad($buscar), '%_\\');

            $query->where(function ($q) use ($buscarSafe, $buscarIdentidad): void {
                $q->whereHas('empleado', function ($sub) use ($buscarSafe, $buscarIdentidad): void {
                    $sub->where('nombre_completo', 'like', "%{$buscarSafe}%")
                        ->orWhere('identidad', 'like', "%{$buscarSafe}%")
                        ->orWhere('codigo', 'like', "%{$buscarSafe}%");

                    if ($buscarIdentidad !== '' && $buscarIdentidad !== $buscarSafe) {
                        $sub->orWhere('identidad', 'like', "%{$buscarIdentidad}%");
                    }
                })
                    ->orWhere('area', 'like', "%{$buscarSafe}%")
                    ->orWhere('motivo_anulacion', 'like', "%{$buscarSafe}%")
                    ->orWhereHas('anuladoPor', function ($sub) use ($buscarSafe): void {
                        $sub->where('name', 'like', "%{$buscarSafe}%");
                    })
                    ->orWhereHas('mobiliario', function ($sub) use ($buscarSafe): void {
                        $sub->where('etiqueta', 'like', "%{$buscarSafe}%")
                            ->orWhere('nombre', 'like', "%{$buscarSafe}%");
                    })
                    ->orWhereHas('dispositivo', function ($sub) use ($buscarSafe): void {
                        $sub->where('etiqueta', 'like', "%{$buscarSafe}%")
                            ->orWhere('nombre', 'like', "%{$buscarSafe}%")
                            ->orWhere('n_serie', 'like', "%{$buscarSafe}%");
                    });
            });
        }

        $asignaciones = $query->latest('deleted_at')->paginate(15)->withQueryString();

        return view('asignaciones.bajas', compact('asignaciones', 'tipo', 'buscar', 'totalAnuladas'));
    }

    /**
     * Exportar asignaciones según los filtros aplicados a Excel.
     */
    public function exportar(Request $request)
    {
        return Excel::download(new AsignacionesExport($request), 'reporte_asignaciones.xlsx');
    }

    /**
     * Exportar archivo exclusivo de asignaciones anuladas a Excel.
     */
    public function exportarBajas(Request $request)
    {
        $request->merge(['estado_acta' => 'anuladas']);

        return Excel::download(new AsignacionesExport($request), 'asignaciones_anuladas.xlsx');
    }

    /**
     * Formulario para nueva asignación.
     */
    public function create(Request $request): View
    {
        Gate::authorize('admin');

        $tipo = in_array($request->query('tipo'), [Asignacion::TIPO_MOBILIARIO, Asignacion::TIPO_DISPOSITIVO], true)
            ? $request->query('tipo')
            : Asignacion::TIPO_DISPOSITIVO;

        $id_referencia = filter_var($request->query('id'), FILTER_VALIDATE_INT) ?: null;

        // Cargar colaboradores activos con identidad formateada y versión corrida para búsqueda
        $empleados = Empleado::orderBy('nombre_completo')
            ->get(['id', 'codigo', 'nombre_completo', 'identidad', 'gerencia'])
            ->map(fn (Empleado $emp): array => [
                'id' => $emp->id,
                'codigo' => $emp->codigo,
                'nombre_completo' => $emp->nombre_completo,
                'identidad' => $emp->identidad_formateada,
                'identidad_raw' => Empleado::normalizarIdentidad($emp->identidad),
                'gerencia' => $emp->gerencia,
            ]);

        // Categorías institucionales predefinidas
        $categoriasDispositivos = Dispositivo::TIPOS;
        $categoriasMobiliario = Mobiliario::TIPOS;

        // Preselección si viene por URL
        $itemPreseleccionado = null;
        if ($id_referencia) {
            $itemPreseleccionado = $tipo === Asignacion::TIPO_MOBILIARIO
                ? Mobiliario::find($id_referencia)
                : Dispositivo::find($id_referencia);
        }

        $destinoSugerido = ($itemPreseleccionado && $itemPreseleccionado->esEquipoAreaComun())
            ? 'area_comun'
            : 'empleado';

        // Bienes actualmente disponibles (tanto generales como exclusivos de área común)
        $dispositivosDisponibles = Dispositivo::disponibles()
            ->orWhere(function ($q) use ($id_referencia, $tipo): void {
                if ($tipo === Asignacion::TIPO_DISPOSITIVO && $id_referencia) {
                    $q->where('id', $id_referencia);
                }
            })
            ->get(['id', 'nombre', 'tipo', 'marca', 'modelo', 'n_serie', 'estado', 'disponibilidad', 'etiqueta'])
            ->map(fn (Dispositivo $d): array => [
                'id' => $d->id,
                'nombre' => $d->nombre,
                'tipo' => $d->tipo,
                'marca' => $d->marca,
                'modelo' => $d->modelo,
                'n_serie' => $d->n_serie,
                'estado' => $d->estado,
                'etiqueta' => $d->etiqueta,
                'es_area_comun' => $d->esEquipoAreaComun(),
            ]);

        $mobiliarioDisponible = Mobiliario::disponibles()
            ->orWhere(function ($q) use ($id_referencia, $tipo): void {
                if ($tipo === Asignacion::TIPO_MOBILIARIO && $id_referencia) {
                    $q->where('id', $id_referencia);
                }
            })
            ->get(['id', 'nombre', 'tipo', 'estado', 'disponibilidad', 'etiqueta'])
            ->map(fn (Mobiliario $m): array => [
                'id' => $m->id,
                'nombre' => $m->nombre,
                'tipo' => $m->tipo,
                'estado' => $m->estado,
                'etiqueta' => $m->etiqueta,
                'es_area_comun' => $m->esEquipoAreaComun(),
            ]);

        return view('asignaciones.create', compact(
            'tipo',
            'id_referencia',
            'destinoSugerido',
            'empleados',
            'categoriasDispositivos',
            'categoriasMobiliario',
            'dispositivosDisponibles',
            'mobiliarioDisponible'
        ));
    }

    /**
     * Guardar nueva asignación.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('admin');

        $validated = $request->validate([
            'tipo' => ['required', 'string', Rule::in([Asignacion::TIPO_MOBILIARIO, Asignacion::TIPO_DISPOSITIVO])],
            'id_referencia' => ['required', 'integer', 'min:1'],
            'destino' => ['required', 'string', Rule::in(['empleado', 'area_comun'])],
            'empleado_id' => [
                Rule::requiredIf($request->input('destino') === 'empleado'),
                'nullable',
                Rule::exists('empleados', 'id')->whereNull('deleted_at'),
            ],
            'area' => ['required', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'fecha_entrega' => ['required', 'date'],
            'entregado_por' => ['required', 'string', 'max:255'],
        ], [
            'tipo.required' => 'Debe seleccionar si asignará mobiliario o un dispositivo.',
            'id_referencia.required' => 'Debe seleccionar el artículo específico a asignar.',
            'destino.required' => 'Indique si el bien se asigna a un colaborador o a un área común.',
            'empleado_id.required_if' => 'Debe seleccionar al colaborador responsable del bien.',
            'empleado_id.exists' => 'El colaborador seleccionado no existe o se encuentra inactivo.',
            'area.required' => 'El área o departamento es obligatorio.',
            'fecha_entrega.required' => 'La fecha de entrega es obligatoria.',
            'entregado_por.required' => 'Indique el nombre de la persona que entrega el bien.',
        ]);

        $esAreaComun = $validated['destino'] === 'area_comun';

        // Si es a un colaborador, asegurar el área con la gerencia real del empleado en el servidor
        if (! $esAreaComun) {
            $empleado = Empleado::findOrFail($validated['empleado_id']);
            $validated['area'] = $empleado->gerencia ?? $validated['area'];
        }

        $errorMensaje = DB::transaction(function () use ($validated, $esAreaComun): ?string {
            $query = $validated['tipo'] === Asignacion::TIPO_MOBILIARIO
                ? Mobiliario::query()
                : Dispositivo::query();

            $item = $query->lockForUpdate()->find($validated['id_referencia']);

            if (! $item) {
                return 'El bien seleccionado no existe en el inventario.';
            }

            if (! $item->puedeAsignarseA($validated['destino'])) {
                if ($validated['destino'] === 'empleado' && $item->esEquipoAreaComun()) {
                    return 'Este bien fue registrado para uso exclusivo en áreas comunes o infraestructura y no puede asignarse de forma individual a un colaborador.';
                }

                return 'Este bien no está disponible para ser asignado (ya se encuentra asignado o su estado físico no lo permite).';
            }

            Asignacion::create([
                'tipo' => $validated['tipo'],
                'id_referencia' => (int) $validated['id_referencia'],
                'mobiliario_id' => $validated['tipo'] === Asignacion::TIPO_MOBILIARIO ? (int) $validated['id_referencia'] : null,
                'dispositivo_id' => $validated['tipo'] === Asignacion::TIPO_DISPOSITIVO ? (int) $validated['id_referencia'] : null,
                'es_area_comun' => $esAreaComun,
                'empleado_id' => $esAreaComun ? null : (int) $validated['empleado_id'],
                'area' => $validated['area'],
                'observaciones' => $validated['observaciones'] ?? null,
                'fecha_entrega' => $validated['fecha_entrega'],
                'entregado_por' => $validated['entregado_por'],
            ]);

            $item->disponibilidad = $item::DISPONIBILIDAD_ASIGNADO;
            $item->save();

            return null;
        });

        if ($errorMensaje !== null) {
            return redirect()->back()->withInput()->with('error', $errorMensaje);
        }

        return redirect()->route('asignaciones.index')->with('success', 'Asignación registrada correctamente.');
    }

    /**
     * Mostrar detalles de la asignación.
     */
    public function show(Asignacion $asignacion): View
    {
        $asignacion->load(['anuladoPor', 'empleado', 'mobiliario', 'dispositivo']);

        $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
            ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
            : Dispositivo::withTrashed()->find($asignacion->id_referencia);

        return view('asignaciones.show', compact('asignacion', 'item'));
    }

    /**
     * Anular asignación por error humano (requiere contraseña y motivo, aplica Soft Delete con trazabilidad).
     */
    public function destroy(Request $request, Asignacion $asignacion): RedirectResponse
    {
        Gate::authorize('admin');

        // 1. Prohibir eliminación si ya cuenta con devolución
        if ($asignacion->devolucion()->exists()) {
            return redirect()->back()->with('error', 'Acción denegada: No se puede anular una asignación que ya cuenta con un acta de devolución formal registrada.');
        }

        // 2. Exigir contraseña del usuario en sesión y motivo de anulación
        $validated = $request->validate([
            'password' => ['required', 'string'],
            'motivo_anulacion' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'password.required' => 'Debe ingresar su contraseña de usuario para autorizar la anulación de esta asignación.',
            'motivo_anulacion.required' => 'Debe ingresar el motivo por el cual se anula la asignación.',
            'motivo_anulacion.min' => 'El motivo de anulación debe contener al menos 5 caracteres.',
        ]);

        if (! Hash::check($validated['password'], $request->user()->password)) {
            return redirect()->back()->with('error', 'La contraseña ingresada es incorrecta. La asignación NO fue anulada.');
        }

        // 3. Ejecutar anulación lógica y liberación del bien en transacción
        DB::transaction(function () use ($asignacion, $request, $validated): void {
            $tipo = $asignacion->tipo;
            $idReferencia = $asignacion->id_referencia;

            $asignacion->motivo_anulacion = $validated['motivo_anulacion'];
            $asignacion->anulado_por_user_id = $request->user()->id;
            $asignacion->save();
            $asignacion->delete();

            $item = $tipo === Asignacion::TIPO_MOBILIARIO
                ? Mobiliario::find($idReferencia)
                : Dispositivo::find($idReferencia);

            if ($item) {
                $item->disponibilidad = $item::calcularDisponibilidad($item->estado, (bool) $asignacion->es_area_comun, $item->estaAsignado());
                $item->save();
            }
        });

        return redirect()->route('asignaciones.index')->with('success', 'Asignación anulada exitosamente. El bien ha sido liberado y queda nuevamente disponible en el inventario.');
    }

    /**
     * Reactivar una asignación anulada por error previo (requiere contraseña y disponibilidad del bien).
     */
    public function restore(Request $request, int $id): RedirectResponse
    {
        Gate::authorize('admin');

        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Debe ingresar su contraseña de usuario para autorizar la reactivación de esta asignación.',
        ]);

        if (! Hash::check($request->password, $request->user()->password)) {
            return redirect()->back()->with('error', 'La contraseña ingresada es incorrecta. La asignación NO fue reactivada.');
        }

        $asignacion = Asignacion::onlyTrashed()->findOrFail($id);

        $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
            ? Mobiliario::find($asignacion->id_referencia)
            : Dispositivo::find($asignacion->id_referencia);

        if (! $item) {
            return redirect()->back()->with('error', 'No se puede reactivar: el bien asociado ya no existe en el inventario.');
        }

        if ($item->estaAsignado()) {
            return redirect()->back()->with('error', 'Acción denegada: El bien ya cuenta con una asignación activa actualmente en el inventario.');
        }

        if (! $item::esEstadoAsignable($item->estado)) {
            return redirect()->back()->with('error', 'Acción denegada: El bien se encuentra en un estado físico no asignable ('.$item->estado.').');
        }

        DB::transaction(function () use ($asignacion, $item): void {
            $asignacion->motivo_anulacion = null;
            $asignacion->anulado_por_user_id = null;
            $asignacion->save();
            $asignacion->restore();

            $item->disponibilidad = $item::DISPONIBILIDAD_ASIGNADO;
            $item->save();
        });

        return redirect()->route('asignaciones.show', $asignacion->id)
            ->with('success', 'Asignación reactivada exitosamente. El bien vuelve a encontrarse asignado.');
    }

    /**
     * Generar PDF del Acta de Entrega con membrete institucional CCISur.
     */
    public function pdf(Asignacion $asignacion)
    {
        $item = $asignacion->tipo === Asignacion::TIPO_MOBILIARIO
            ? Mobiliario::withTrashed()->find($asignacion->id_referencia)
            : Dispositivo::withTrashed()->find($asignacion->id_referencia);

        $pdf = Pdf::loadView('asignaciones.acta_pdf', compact('asignacion', 'item'));

        return $pdf->stream('Acta_Entrega_'.$asignacion->id.'.pdf');
    }
}
