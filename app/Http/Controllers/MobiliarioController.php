<?php

namespace App\Http\Controllers;

use App\Exports\MobiliarioExport;
use App\Models\Mobiliario;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class MobiliarioController extends Controller
{
    // Mostrar listado paginado y filtrado
    public function index(Request $request): View
    {
        $incluirBajas = $request->boolean('incluir_bajas');
        $query = $incluirBajas
            ? Mobiliario::withTrashed()->withCount(['asignacionesActivas', 'asignaciones'])
            : Mobiliario::query()->withCount(['asignacionesActivas', 'asignaciones']);

        if ($request->filled('nombre')) {
            $termino = addcslashes(trim((string) $request->input('nombre')), '%_\\');
            $query->where(function (Builder $q) use ($termino): void {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('etiqueta', 'like', "%{$termino}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', (string) $request->input('tipo'));
        }

        if ($request->filled('ubicacion')) {
            $ubicacion = addcslashes(trim((string) $request->input('ubicacion')), '%_\\');
            $query->where('ubicacion', 'like', "%{$ubicacion}%");
        }

        if ($request->filled('estado')) {
            $estado = trim((string) $request->input('estado'));
            $estadoCompleto = array_search($estado, Mobiliario::ESTADOS_CORTOS, true) ?: $estado;
            $query->where(function (Builder $q) use ($estado, $estadoCompleto): void {
                $q->where('estado', $estadoCompleto)
                    ->orWhere('estado', $estado)
                    ->orWhere('estado', 'like', "{$estado}%");
            });

            // Si se envió un estado explícito, limpiar en_taller para evitar inconsistencia de badge (🟡-5)
            if ($request->has('en_taller')) {
                $request->offsetUnset('en_taller');
            }
        } elseif ($request->boolean('en_taller')) {
            $query->where('estado', 'En reparación / En revisión');
        }

        if ($request->filled('disponibilidad')) {
            match ((string) $request->input('disponibilidad')) {
                Mobiliario::DISPONIBILIDAD_ASIGNADO => $query->asignados(),
                Mobiliario::DISPONIBILIDAD_SIN_ASIGNAR => $query->disponibles(),
                Mobiliario::DISPONIBILIDAD_NO_APLICA => $query->noAsignables(),
                default => null,
            };
        }

        $hayFiltrosFormulario = collect($request->only(['nombre', 'tipo', 'ubicacion', 'estado', 'disponibilidad', 'incluir_bajas']))
            ->filter(fn ($valor): bool => filled($valor) && $valor !== '0')
            ->isNotEmpty();
        $hayFiltros = $hayFiltrosFormulario || $request->boolean('en_taller');

        $mobiliarios = $query->with('mantenimientoActivo')->latest('id')->paginate(15)->withQueryString();
        $inactivosCount = Mobiliario::onlyTrashed()->count();
        $enTallerCount = Mobiliario::where('estado', 'En reparación / En revisión')->count();

        return view('mobiliario.index', compact('mobiliarios', 'hayFiltros', 'inactivosCount', 'enTallerCount'));
    }

    // Exportar inventario actual de mobiliario a Excel
    public function exportar(Request $request)
    {
        $nombreArchivo = $request->boolean('incluir_bajas')
            ? 'inventario_historico_mobiliario.xlsx'
            : 'inventario_mobiliario.xlsx';

        return Excel::download(new MobiliarioExport($request), $nombreArchivo);
    }

    // Exportar archivo exclusivo de mobiliario dado de baja
    public function exportarBajas(Request $request)
    {
        $request->merge(['bajas_solamente' => true]);

        return Excel::download(new MobiliarioExport($request), 'mobiliario_dado_de_baja.xlsx');
    }

    // Mostrar formulario de creación
    public function create(): View
    {
        Gate::authorize('admin');

        return view('mobiliario.create');
    }

    // Guardar nuevo mobiliario
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('admin');

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', Rule::in(Mobiliario::TIPOS)],
            'ubicacion' => ['required', 'string', Rule::in(Mobiliario::UBICACIONES)],
            'estado' => ['required', Rule::in(Mobiliario::ESTADOS_MANUALES)],
            'fecha_registro' => ['required', 'date'],
            'es_area_comun' => ['nullable', 'boolean'],
        ]);

        $esDefectuoso = $validated['estado'] === Mobiliario::ESTADO_DEFECTUOSO;
        if ($esDefectuoso) {
            $validated['motivo_baja'] = 'Ingresado al inventario en estado Dañado / Defectuoso';
            $validated['baja_por_user_id'] = Auth::id();
            $validated['disponibilidad'] = Mobiliario::DISPONIBILIDAD_NO_APLICA;
        } else {
            $validated['disponibilidad'] = Mobiliario::calcularDisponibilidad(
                $validated['estado'],
                $request->boolean('es_area_comun'),
                false
            );
        }

        unset($validated['es_area_comun']);

        $mobiliario = DB::transaction(function () use ($validated, $esDefectuoso): Mobiliario {
            $mobiliario = Mobiliario::create($validated);
            $anio = Carbon::parse($validated['fecha_registro'])->format('Y');
            $mobiliario->etiqueta = 'INV-MOB-'.$anio.'-'.str_pad((string) $mobiliario->id, 4, '0', STR_PAD_LEFT);
            $mobiliario->save();

            if ($esDefectuoso) {
                $mobiliario->delete();
            }

            return $mobiliario;
        });

        if ($esDefectuoso) {
            return redirect()->route('mobiliario.bajas')
                ->with('warning', "Mobiliario {$mobiliario->etiqueta} registrado y trasladado directamente al Archivo de Bajas por haber ingresado en estado Dañado / Defectuoso.");
        }

        return redirect()->route('mobiliario.index')->with('success', 'Mobiliario registrado correctamente.');
    }

    // Mostrar formulario de edición
    public function edit(Mobiliario $mobiliario): View
    {
        Gate::authorize('admin');

        $mobiliario->loadCount('asignacionesActivas');

        return view('mobiliario.edit', compact('mobiliario'));
    }

    // Actualizar registro
    public function update(Request $request, Mobiliario $mobiliario): RedirectResponse
    {
        Gate::authorize('admin');

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', Rule::in(Mobiliario::TIPOS)],
            'ubicacion' => ['required', 'string', Rule::in(Mobiliario::UBICACIONES)],
            'estado' => ['nullable', Rule::in(Mobiliario::ESTADOS_MANUALES)],
            'fecha_registro' => ['required', 'date'],
            'es_area_comun' => ['nullable', 'boolean'],
        ]);

        return DB::transaction(function () use ($request, $mobiliario, $validated): RedirectResponse {
            $mobiliarioBloqueado = Mobiliario::lockForUpdate()->findOrFail($mobiliario->id);

            $tieneAsignacionActiva = $mobiliarioBloqueado->estaAsignado();
            $tieneMantenimientoActivo = $mobiliarioBloqueado->mantenimientoActivo()->exists();

            if (! $tieneAsignacionActiva && ! $tieneMantenimientoActivo && empty($validated['estado'])) {
                return redirect()->back()->withErrors(['estado' => 'El estado físico es obligatorio.'])->withInput();
            }

            if ($tieneMantenimientoActivo) {
                $validated['estado'] = 'En reparación / En revisión';
                $validated['disponibilidad'] = $tieneAsignacionActiva
                    ? Mobiliario::DISPONIBILIDAD_ASIGNADO
                    : Mobiliario::DISPONIBILIDAD_NO_APLICA;
            } elseif ($tieneAsignacionActiva) {
                // Mientras el mueble está asignado, su sede, estado físico y disponibilidad están bloqueados
                $validated['ubicacion'] = $mobiliarioBloqueado->ubicacion;
                $validated['estado'] = $mobiliarioBloqueado->estado;
                $validated['disponibilidad'] = Mobiliario::DISPONIBILIDAD_ASIGNADO;
            } else {
                if ($validated['estado'] === Mobiliario::ESTADO_NUEVO && $mobiliarioBloqueado->asignaciones()->exists()) {
                    return redirect()->back()
                        ->withErrors(['estado' => 'Un mueble que ya cuenta con historial de asignaciones no puede volver a registrarse como nuevo.'])
                        ->withInput();
                }

                $validated['disponibilidad'] = Mobiliario::calcularDisponibilidad(
                    $validated['estado'],
                    $request->boolean('es_area_comun'),
                    false
                );
            }

            unset($validated['es_area_comun']);

            $mobiliarioBloqueado->update($validated);

            if (! $tieneAsignacionActiva && ! $tieneMantenimientoActivo && $validated['estado'] === Mobiliario::ESTADO_DEFECTUOSO) {
                $mobiliarioBloqueado->update([
                    'motivo_baja' => 'Actualizado a estado Dañado / Defectuoso en edición de ficha',
                    'baja_por_user_id' => Auth::id(),
                    'disponibilidad' => Mobiliario::DISPONIBILIDAD_NO_APLICA,
                ]);
                $mobiliarioBloqueado->delete();

                return redirect()->route('mobiliario.bajas')
                    ->with('warning', "El mobiliario {$mobiliarioBloqueado->etiqueta} fue trasladado al Archivo de Bajas por registrar estado Dañado / Defectuoso.");
            }

            if ($request->input('from') === 'historial') {
                return redirect()->route('mobiliario.historial', $mobiliarioBloqueado->id)->with('success', 'Mobiliario actualizado correctamente.');
            }

            return redirect()->route('mobiliario.index')->with('success', 'Mobiliario actualizado correctamente.');
        });
    }

    public function show(Mobiliario $mobiliario): RedirectResponse
    {
        return redirect()->route('mobiliario.historial', $mobiliario->id);
    }

    // Dar de baja registro (Soft Delete)
    public function destroy(Request $request, Mobiliario $mobiliario): RedirectResponse
    {
        Gate::authorize('admin');

        $request->merge([
            'motivo_baja' => trim((string) $request->input('motivo_baja')),
        ]);

        $request->validate([
            'motivo_baja' => ['required', 'string', 'min:3', 'max:500'],
        ], [
            'motivo_baja.required' => 'Debe ingresar o seleccionar el motivo de la baja del mobiliario.',
            'motivo_baja.min' => 'El motivo de la baja debe contener al menos 3 caracteres.',
            'motivo_baja.max' => 'El motivo de la baja no puede exceder los 500 caracteres.',
        ]);

        return DB::transaction(function () use ($mobiliario, $request): RedirectResponse {
            $mobiliarioActualizado = Mobiliario::lockForUpdate()->find($mobiliario->id);

            if (! $mobiliarioActualizado) {
                return redirect()->route('mobiliario.index')->with('error', 'El mobiliario ya no existe.');
            }

            $mobiliarioActualizado->loadCount(['asignacionesActivas']);

            if ($mobiliarioActualizado->mantenimientoActivo()->exists()) {
                return redirect()
                    ->route('mobiliario.historial', $mobiliarioActualizado->id)
                    ->with('error', 'No se puede dar de baja un mobiliario que tiene un mantenimiento activo en proceso. Finalice o cancele el mantenimiento primero.');
            }

            if (! $mobiliarioActualizado->puede_darse_de_baja) {
                return redirect()
                    ->route('mobiliario.index')
                    ->with('error', $mobiliarioActualizado->motivo_no_baja ?? 'No se puede dar de baja este mueble.');
            }

            $mobiliarioActualizado->update([
                'motivo_baja' => (string) $request->input('motivo_baja'),
                'baja_por_user_id' => Auth::id(),
            ]);

            $mobiliarioActualizado->delete();

            return redirect()->route('mobiliario.index')->with('success', 'Mobiliario dado de baja correctamente.');
        });
    }

    // Listar mobiliario dado de baja
    public function bajas(Request $request): View
    {
        $query = Mobiliario::onlyTrashed()->with(['dadoDeBajaPor'])->withCount(['asignacionesActivas', 'asignaciones']);

        if ($request->filled('nombre')) {
            $termino = addcslashes(trim((string) $request->input('nombre')), '%_\\');
            $query->where(function (Builder $q) use ($termino): void {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('etiqueta', 'like', "%{$termino}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', (string) $request->input('tipo'));
        }

        if ($request->filled('ubicacion')) {
            $ubicacion = addcslashes(trim((string) $request->input('ubicacion')), '%_\\');
            $query->where('ubicacion', 'like', "%{$ubicacion}%");
        }

        $hayFiltros = collect($request->only(['nombre', 'tipo', 'ubicacion']))
            ->filter(fn ($valor): bool => filled($valor))
            ->isNotEmpty();

        $mobiliarios = $query->latest('deleted_at')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('mobiliario.bajas', compact('mobiliarios', 'hayFiltros'));
    }

    // Reactivar mobiliario dado de baja
    public function reactivar(Request $request, int $id): RedirectResponse
    {
        Gate::authorize('admin');

        return DB::transaction(function () use ($request, $id): RedirectResponse {
            $mobiliario = Mobiliario::onlyTrashed()->lockForUpdate()->find($id);

            if (! $mobiliario) {
                return redirect()->route('mobiliario.bajas')->with('info', 'El mobiliario ya fue reactivado o no se encuentra en bajas.');
            }

            if ($mobiliario->mantenimientoActivo()->exists()) {
                return redirect()->route('mobiliario.historial', $mobiliario->id)
                    ->with('error', 'Este mobiliario tiene un mantenimiento en proceso en taller. Debe finalizar o cancelar el mantenimiento para reactivarlo.');
            }

            $enviarATaller = $request->boolean('enviar_a_taller');
            $motivoTaller = trim((string) $request->input('motivo_taller'));

            $mobiliario->restore();

            $mobiliario->update([
                'motivo_baja' => null,
                'baja_por_user_id' => null,
                'reactivado_por_user_id' => Auth::id(),
            ]);

            if ($enviarATaller) {
                $estadoPrevio = $mobiliario->estado;
                $mobiliario->estado = 'En reparación / En revisión';
                $mobiliario->disponibilidad = Mobiliario::DISPONIBILIDAD_NO_APLICA;
                $mobiliario->save();

                \App\Models\MantenimientoMobiliario::create([
                    'mobiliario_id' => $mobiliario->id,
                    'user_id' => Auth::id(),
                    'tipo' => \App\Models\MantenimientoMobiliario::TIPO_MECANICA,
                    'motivo_falla' => $motivoTaller !== '' ? $motivoTaller : 'Reingreso a taller por reactivación desde archivo de bajas',
                    'estado' => \App\Models\MantenimientoMobiliario::ESTADO_EN_PROCESO,
                    'fecha_inicio' => now(),
                    'estado_fisico_anterior' => $estadoPrevio ?? Mobiliario::ESTADO_DEFECTUOSO,
                    'es_area_comun' => false,
                ]);

                return redirect()->route('mobiliario.historial', ['mobiliario' => $mobiliario->id, 'tab' => 'bitacora'])
                    ->with('success', "Mobiliario {$mobiliario->etiqueta} reactivado y enviado directamente a taller técnico.");
            }

            // Al restaurar, recalcular disponibilidad según su estado físico actual
            $mobiliario->disponibilidad = Mobiliario::calcularDisponibilidad($mobiliario->estado, false, false);
            $mobiliario->save();

            if ($request->input('from') === 'historial') {
                return redirect()->route('mobiliario.historial', $mobiliario->id)
                    ->with('success', "Mobiliario {$mobiliario->etiqueta} reactivado correctamente.");
            }

            return redirect()->route('mobiliario.bajas')->with('success', "Mobiliario {$mobiliario->etiqueta} reactivado correctamente.");
        });
    }

    // Hoja de Vida / Historial del bien
    public function historial(Request $request, Mobiliario $mobiliario): View
    {
        $mobiliario->load(['dadoDeBajaPor', 'reactivadoPor']);
        $mobiliario->loadCount(['asignacionesActivas', 'asignaciones']);

        $totalAsignaciones = (int) $mobiliario->asignaciones_count;
        $totalDevueltos = max(0, $totalAsignaciones - (int) $mobiliario->asignaciones_activas_count);

        $query = $mobiliario->asignaciones()->with(['empleado', 'devolucion']);

        if ($request->filled('buscar')) {
            $termino = addcslashes(trim((string) $request->input('buscar')), '%_\\');
            $query->where(function (Builder $q) use ($termino): void {
                $q->where('area', 'like', "%{$termino}%")
                    ->orWhereHas('empleado', function (Builder $emp) use ($termino): void {
                        $emp->where('nombre_completo', 'like', "%{$termino}%")
                            ->orWhere('codigo', 'like', "%{$termino}%")
                            ->orWhere('identidad', 'like', "%{$termino}%");
                    });
            });
        }

        $hayFiltros = collect($request->only(['buscar']))
            ->filter(fn ($valor): bool => filled($valor))
            ->isNotEmpty();

        $asignaciones = $query->latest('fecha_entrega')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $mantenimientos = $mobiliario->mantenimientos()
            ->with(['user', 'asignacion.empleado'])
            ->get();

        $mantenimientosAsc = $mantenimientos->sortBy('id')->values();
        foreach ($mantenimientosAsc as $index => $mant) {
            $mant->numero_secuencial = $index + 1;
        }

        $totalMantenimientos = $mantenimientos->count();
        $mantenimientoActivo = $mantenimientos->firstWhere('estado', \App\Models\MantenimientoMobiliario::ESTADO_EN_PROCESO);

        return view('mobiliario.historial', compact(
            'mobiliario',
            'asignaciones',
            'totalAsignaciones',
            'totalDevueltos',
            'hayFiltros',
            'mantenimientos',
            'totalMantenimientos',
            'mantenimientoActivo'
        ));
    }
}
