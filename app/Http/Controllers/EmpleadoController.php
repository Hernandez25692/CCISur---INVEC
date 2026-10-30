<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmpleadoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Empleado::query()->withCount(['asignacionesActivas', 'asignaciones']);

        if ($request->filled('buscar')) {
            $terminoRaw = trim((string) $request->input('buscar'));
            $termino = addcslashes($terminoRaw, '%_\\');
            $terminoSinGuiones = addcslashes(Empleado::normalizarIdentidad($terminoRaw), '%_\\');

            $query->where(function (Builder $q) use ($termino, $terminoSinGuiones): void {
                $q->where('nombre_completo', 'like', "%{$termino}%")
                    ->orWhere('codigo', 'like', "%{$termino}%")
                    ->orWhere('identidad', 'like', "%{$termino}%");

                if ($terminoSinGuiones !== '' && $terminoSinGuiones !== $termino) {
                    $q->orWhere('identidad', 'like', "%{$terminoSinGuiones}%");
                }
            });
        }

        if ($request->filled('gerencia')) {
            $query->where('gerencia', (string) $request->input('gerencia'));
        }

        if ($request->filled('ubicacion')) {
            $query->where('ubicacion', (string) $request->input('ubicacion'));
        }

        $hayFiltros = collect($request->only(['buscar', 'gerencia', 'ubicacion']))
            ->filter(fn ($valor): bool => filled($valor))
            ->isNotEmpty();

        $empleados = $query->latest()->orderByDesc('id')->paginate(15)->withQueryString();
        $inactivosCount = Empleado::onlyTrashed()->count();

        return view('empleados.index', compact('empleados', 'hayFiltros', 'inactivosCount'));
    }

    public function create(): View
    {
        return view('empleados.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'identidad' => Empleado::normalizarIdentidad($request->input('identidad')),
            'nombre_completo' => trim((string) $request->input('nombre_completo')),
        ]);

        $validated = $request->validate([
            'identidad' => ['required', 'string', 'regex:/^\d{13}$/', Rule::unique('empleados', 'identidad')],
            'nombre_completo' => ['required', 'string', 'max:255'],
            'gerencia' => ['required', Rule::in(Empleado::GERENCIAS)],
            'ubicacion' => ['required', Rule::in(Empleado::UBICACIONES)],
        ], [
            'identidad.required' => 'El número de identidad (DNI) es obligatorio.',
            'identidad.regex' => 'El número de identidad debe contener exactamente 13 dígitos numéricos.',
            'identidad.unique' => 'Este número de identidad ya se encuentra registrado en otro colaborador.',
            'nombre_completo.required' => 'El nombre completo del colaborador es obligatorio.',
            'gerencia.required' => 'Debe seleccionar una gerencia.',
            'ubicacion.required' => 'Debe seleccionar una sede o ubicación.',
        ]);

        DB::transaction(function () use ($validated): void {
            $maxNumero = Empleado::query()
                ->lockForUpdate()
                ->pluck('codigo')
                ->map(fn ($cod): int => (int) preg_replace('/\D+/', '', (string) $cod))
                ->max() ?? 0;

            $proximoNumero = $maxNumero + 1;
            $codigo = 'EM'.str_pad((string) $proximoNumero, 3, '0', STR_PAD_LEFT);

            while (Empleado::query()->where('codigo', $codigo)->exists()) {
                $proximoNumero++;
                $codigo = 'EM'.str_pad((string) $proximoNumero, 3, '0', STR_PAD_LEFT);
            }

            Empleado::create([
                'codigo' => $codigo,
                ...$validated,
            ]);
        });

        return redirect()->route('empleados.index')->with('success', 'Colaborador registrado correctamente.');
    }

    public function show(Empleado $empleado): RedirectResponse
    {
        return redirect()->route('empleados.historial', $empleado->id);
    }

    public function edit(Empleado $empleado): View
    {
        $empleado->loadCount(['asignacionesActivas', 'asignaciones']);

        return view('empleados.edit', compact('empleado'));
    }

    public function update(Request $request, Empleado $empleado): RedirectResponse
    {
        $request->merge([
            'identidad' => Empleado::normalizarIdentidad($request->input('identidad')),
            'nombre_completo' => trim((string) $request->input('nombre_completo')),
        ]);

        $validated = $request->validate([
            'identidad' => [
                'required',
                'string',
                'regex:/^\d{13}$/',
                Rule::unique('empleados', 'identidad')->ignore($empleado->id),
            ],
            'nombre_completo' => ['required', 'string', 'max:255'],
            'gerencia' => ['required', Rule::in(Empleado::GERENCIAS)],
            'ubicacion' => ['required', Rule::in(Empleado::UBICACIONES)],
        ], [
            'identidad.required' => 'El número de identidad (DNI) es obligatorio.',
            'identidad.regex' => 'El número de identidad debe contener exactamente 13 dígitos numéricos.',
            'identidad.unique' => 'Este número de identidad ya pertenece a otro colaborador registrado.',
            'nombre_completo.required' => 'El nombre completo del colaborador es obligatorio.',
            'gerencia.required' => 'Debe seleccionar una gerencia.',
            'ubicacion.required' => 'Debe seleccionar una sede o ubicación.',
        ]);

        $empleado->update($validated);

        return redirect()->route('empleados.index')->with('success', 'Colaborador actualizado correctamente.');
    }

    public function destroy(Empleado $empleado): RedirectResponse
    {
        $empleado->loadCount(['asignacionesActivas']);

        if (! $empleado->puede_darse_de_baja) {
            return redirect()
                ->route('empleados.index')
                ->with('error', $empleado->motivo_no_baja ?? 'No se puede dar de baja a este colaborador.');
        }

        $empleado->delete();

        return redirect()->route('empleados.index')->with('success', 'Colaborador dado de baja correctamente.');
    }

    public function bajas(Request $request): View
    {
        $query = Empleado::onlyTrashed()->withCount(['asignacionesActivas', 'asignaciones']);

        if ($request->filled('buscar')) {
            $terminoRaw = trim((string) $request->input('buscar'));
            $termino = addcslashes($terminoRaw, '%_\\');
            $terminoSinGuiones = addcslashes(Empleado::normalizarIdentidad($terminoRaw), '%_\\');

            $query->where(function (Builder $q) use ($termino, $terminoSinGuiones): void {
                $q->where('nombre_completo', 'like', "%{$termino}%")
                    ->orWhere('codigo', 'like', "%{$termino}%")
                    ->orWhere('identidad', 'like', "%{$termino}%");

                if ($terminoSinGuiones !== '' && $terminoSinGuiones !== $termino) {
                    $q->orWhere('identidad', 'like', "%{$terminoSinGuiones}%");
                }
            });
        }

        if ($request->filled('gerencia')) {
            $query->where('gerencia', (string) $request->input('gerencia'));
        }

        if ($request->filled('ubicacion')) {
            $query->where('ubicacion', (string) $request->input('ubicacion'));
        }

        $hayFiltros = collect($request->only(['buscar', 'gerencia', 'ubicacion']))
            ->filter(fn ($valor): bool => filled($valor))
            ->isNotEmpty();

        $empleados = $query->latest('deleted_at')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('empleados.bajas', compact('empleados', 'hayFiltros'));
    }

    public function reactivar(int $id): RedirectResponse
    {
        $empleado = Empleado::onlyTrashed()->findOrFail($id);
        $empleado->restore();

        return redirect()->route('empleados.bajas')->with('success', "Colaborador {$empleado->nombre_completo} reactivado correctamente.");
    }

    public function historial(Request $request, Empleado $empleado): View
    {
        $empleado->loadCount(['asignacionesActivas', 'asignaciones']);

        $totalActivos = (int) $empleado->asignaciones_activas_count;
        $totalDevueltos = max(0, (int) $empleado->asignaciones_count - $totalActivos);

        $query = $empleado->asignaciones()->with(['mobiliario', 'dispositivo', 'devolucion']);

        if ($request->filled('buscar')) {
            $termino = addcslashes(trim((string) $request->input('buscar')), '%_\\');
            $query->where(function (Builder $q) use ($termino): void {
                $q->where('area', 'like', "%{$termino}%")
                    ->orWhere(function (Builder $sub) use ($termino): void {
                        $sub->where('tipo', 'mobiliario')
                            ->whereHas('mobiliario', function (Builder $m) use ($termino): void {
                                $m->where('nombre', 'like', "%{$termino}%")
                                    ->orWhere('etiqueta', 'like', "%{$termino}%");
                            });
                    })
                    ->orWhere(function (Builder $sub) use ($termino): void {
                        $sub->where('tipo', 'dispositivo')
                            ->whereHas('dispositivo', function (Builder $d) use ($termino): void {
                                $d->where('nombre', 'like', "%{$termino}%")
                                    ->orWhere('etiqueta', 'like', "%{$termino}%")
                                    ->orWhere('n_serie', 'like', "%{$termino}%");
                            });
                    });
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', (string) $request->input('tipo'));
        }

        $hayFiltros = collect($request->only(['buscar', 'tipo']))
            ->filter(fn ($valor): bool => filled($valor))
            ->isNotEmpty();

        $asignaciones = $query->latest('fecha_entrega')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('asignaciones.historial', compact(
            'empleado',
            'asignaciones',
            'totalActivos',
            'totalDevueltos',
            'hayFiltros'
        ));
    }
}
