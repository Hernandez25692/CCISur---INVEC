<?php

namespace App\Http\Controllers;

use App\Exports\MobiliarioExport;
use App\Models\Mobiliario;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        }

        if ($request->filled('disponibilidad')) {
            match ((string) $request->input('disponibilidad')) {
                Mobiliario::DISPONIBILIDAD_ASIGNADO => $query->asignados(),
                Mobiliario::DISPONIBILIDAD_SIN_ASIGNAR => $query->disponibles(),
                Mobiliario::DISPONIBILIDAD_NO_APLICA => $query->noAsignables(),
                default => null,
            };
        }

        $hayFiltros = collect($request->only(['nombre', 'tipo', 'ubicacion', 'estado', 'disponibilidad', 'incluir_bajas']))
            ->filter(fn ($valor): bool => filled($valor) && $valor !== '0')
            ->isNotEmpty();

        $mobiliarios = $query->latest('id')->paginate(15)->withQueryString();
        $inactivosCount = Mobiliario::onlyTrashed()->count();

        return view('mobiliario.index', compact('mobiliarios', 'hayFiltros', 'inactivosCount'));
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
            'ubicacion' => ['required', 'string', 'max:255'],
            'estado' => ['required', Rule::in(Mobiliario::ESTADOS)],
            'fecha_registro' => ['required', 'date'],
            'es_area_comun' => ['nullable', 'boolean'],
        ]);

        $validated['disponibilidad'] = Mobiliario::calcularDisponibilidad(
            $validated['estado'],
            $request->boolean('es_area_comun'),
            false
        );

        unset($validated['es_area_comun']);

        DB::transaction(function () use ($validated): void {
            $mobiliario = Mobiliario::create($validated);
            $anio = Carbon::parse($validated['fecha_registro'])->format('Y');
            $mobiliario->etiqueta = 'INV-MOB-'.$anio.'-'.str_pad((string) $mobiliario->id, 4, '0', STR_PAD_LEFT);
            $mobiliario->save();
        });

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

        $tieneAsignacionActiva = $mobiliario->estaAsignado();

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'string', Rule::in(Mobiliario::TIPOS)],
            'ubicacion' => ['required', 'string', 'max:255'],
            'estado' => [$tieneAsignacionActiva ? 'nullable' : 'required', Rule::in(Mobiliario::ESTADOS)],
            'fecha_registro' => ['required', 'date'],
            'es_area_comun' => ['nullable', 'boolean'],
        ]);

        if ($tieneAsignacionActiva) {
            // Mientras el mueble está asignado, su estado físico y disponibilidad están bloqueados
            $validated['estado'] = $mobiliario->estado;
            $validated['disponibilidad'] = Mobiliario::DISPONIBILIDAD_ASIGNADO;
        } else {
            $validated['disponibilidad'] = Mobiliario::calcularDisponibilidad(
                $validated['estado'],
                $request->boolean('es_area_comun'),
                false
            );
        }

        unset($validated['es_area_comun']);

        $mobiliario->update($validated);

        return redirect()->route('mobiliario.index')->with('success', 'Mobiliario actualizado correctamente.');
    }

    public function show(Mobiliario $mobiliario): RedirectResponse
    {
        return redirect()->route('mobiliario.historial', $mobiliario->id);
    }

    // Dar de baja registro (Soft Delete)
    public function destroy(Mobiliario $mobiliario): RedirectResponse
    {
        Gate::authorize('admin');

        return DB::transaction(function () use ($mobiliario): RedirectResponse {
            $mobiliarioActualizado = Mobiliario::lockForUpdate()->find($mobiliario->id);

            if (! $mobiliarioActualizado) {
                return redirect()->route('mobiliario.index')->with('error', 'El mobiliario ya no existe.');
            }

            $mobiliarioActualizado->loadCount(['asignacionesActivas']);

            if (! $mobiliarioActualizado->puede_darse_de_baja) {
                return redirect()
                    ->route('mobiliario.index')
                    ->with('error', $mobiliarioActualizado->motivo_no_baja ?? 'No se puede dar de baja este mueble.');
            }

            $mobiliarioActualizado->delete();

            return redirect()->route('mobiliario.index')->with('success', 'Mobiliario dado de baja correctamente.');
        });
    }

    // Listar mobiliario dado de baja
    public function bajas(Request $request): View
    {
        $query = Mobiliario::onlyTrashed()->withCount(['asignacionesActivas', 'asignaciones']);

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
        }

        $hayFiltros = collect($request->only(['nombre', 'tipo', 'ubicacion', 'estado']))
            ->filter(fn ($valor): bool => filled($valor))
            ->isNotEmpty();

        $mobiliarios = $query->latest('deleted_at')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('mobiliario.bajas', compact('mobiliarios', 'hayFiltros'));
    }

    // Reactivar mobiliario dado de baja
    public function reactivar(int $id): RedirectResponse
    {
        Gate::authorize('admin');

        $mobiliario = Mobiliario::onlyTrashed()->findOrFail($id);
        $mobiliario->restore();

        // Al restaurar, recalcular disponibilidad según su estado físico actual
        $mobiliario->disponibilidad = Mobiliario::calcularDisponibilidad($mobiliario->estado, false, false);
        $mobiliario->save();

        return redirect()->route('mobiliario.bajas')->with('success', "Mobiliario {$mobiliario->etiqueta} reactivado correctamente.");
    }

    // Hoja de Vida / Historial del bien
    public function historial(Request $request, Mobiliario $mobiliario): View
    {
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

        return view('mobiliario.historial', compact(
            'mobiliario',
            'asignaciones',
            'totalAsignaciones',
            'totalDevueltos',
            'hayFiltros'
        ));
    }
}
