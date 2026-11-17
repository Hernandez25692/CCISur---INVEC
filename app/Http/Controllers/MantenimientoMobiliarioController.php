<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Devolucion;
use App\Models\MantenimientoMobiliario;
use App\Models\Mobiliario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MantenimientoMobiliarioController extends Controller
{
    /**
     * Registrar el ingreso de un mobiliario a taller / mantenimiento.
     */
    public function store(Request $request, Mobiliario $mobiliario): RedirectResponse
    {
        Gate::authorize('admin');

        $request->merge([
            'motivo_falla' => trim((string) $request->input('motivo_falla')),
            'taller_proveedor' => trim((string) $request->input('taller_proveedor')),
        ]);

        $validated = $request->validate([
            'tipo' => ['required', 'string', Rule::in(MantenimientoMobiliario::TIPOS)],
            'motivo_falla' => ['required', 'string', 'min:3', 'max:2000'],
            'taller_proveedor' => ['nullable', 'string', 'max:255'],
        ], [
            'tipo.required' => 'Debe seleccionar el tipo de mantenimiento o reparación.',
            'tipo.in' => 'El tipo de mantenimiento seleccionado no es válido.',
            'motivo_falla.required' => 'Debe describir el desperfecto, daño o trabajo requerido.',
            'motivo_falla.min' => 'El motivo debe contener al menos 3 caracteres.',
            'motivo_falla.max' => 'El motivo no puede exceder 2000 caracteres.',
        ]);

        return DB::transaction(function () use ($mobiliario, $validated): RedirectResponse {
            $mobiliarioBloqueado = Mobiliario::withTrashed()->lockForUpdate()->findOrFail($mobiliario->id);

            // Bloquear ingreso a taller si el bien está en el archivo de bajas (🔴-1)
            if ($mobiliarioBloqueado->trashed()) {
                return redirect()->back()->with('error', 'No se puede ingresar a mantenimiento un mueble dado de baja. Debe reactivarlo formalmente desde el Archivo de Bajas antes de enviarlo a taller.');
            }

            // Verificar si ya tiene un mantenimiento activo en proceso
            if ($mobiliarioBloqueado->mantenimientoActivo()->exists()) {
                return redirect()->back()->with('error', 'Este mueble ya tiene un mantenimiento activo en proceso.');
            }

            $asignacionActiva = $mobiliarioBloqueado->asignacionesActivas()->first();
            $estadoAnterior = $mobiliarioBloqueado->estado;
            $esAreaComun = (bool) ($asignacionActiva?->es_area_comun ?? $mobiliarioBloqueado->esEquipoAreaComun());

            MantenimientoMobiliario::create([
                'mobiliario_id' => $mobiliarioBloqueado->id,
                'user_id' => Auth::id(),
                'asignacion_id' => $asignacionActiva?->id,
                'empleado_id' => $asignacionActiva?->empleado_id,
                'tipo' => $validated['tipo'],
                'motivo_falla' => $validated['motivo_falla'],
                'taller_proveedor' => $validated['taller_proveedor'] ?: null,
                'estado' => MantenimientoMobiliario::ESTADO_EN_PROCESO,
                'fecha_inicio' => now(),
                'estado_fisico_anterior' => $estadoAnterior,
                'es_area_comun' => $esAreaComun,
            ]);

            // Actualizar estado físico a "En reparación / En revisión" preservando condición de área común
            $mobiliarioBloqueado->estado = 'En reparación / En revisión';
            $mobiliarioBloqueado->disponibilidad = Mobiliario::calcularDisponibilidad(
                $mobiliarioBloqueado->estado,
                $esAreaComun,
                $asignacionActiva !== null
            );
            $mobiliarioBloqueado->save();

            return redirect()->route('mobiliario.historial', $mobiliarioBloqueado->id)
                ->with('success', "Mobiliario {$mobiliarioBloqueado->etiqueta} ingresado a taller / mantenimiento correctamente.");
        });
    }

    /**
     * Finalizar el mantenimiento de mobiliario (reparado o irreparable).
     */
    public function finalizar(Request $request, MantenimientoMobiliario $mantenimiento): RedirectResponse
    {
        Gate::authorize('admin');

        $resultado = (string) $request->input('resultado');

        if ($resultado === 'reparado') {
            $request->merge([
                'solucion_aplicada' => trim((string) $request->input('solucion_aplicada')),
            ]);

            $mobiliario = $mantenimiento->mobiliario;
            $puedeConservarNuevo = ($mantenimiento->estado_fisico_anterior === 'Nuevo / En perfectas condiciones') && ! $mobiliario->tieneHistorialAsignaciones();

            $estadosPermitidos = [
                'Usado / Segunda mano',
            ];

            if ($puedeConservarNuevo) {
                $estadosPermitidos[] = 'Nuevo / En perfectas condiciones';
            }

            $request->validate([
                'resultado' => ['required', 'in:reparado,irreparable'],
                'solucion_aplicada' => ['required', 'string', 'min:3', 'max:2000'],
                'estado_fisico_resultante' => ['required', 'string', Rule::in($estadosPermitidos)],
            ], [
                'solucion_aplicada.required' => 'Debe detallar la reparación o trabajo realizado en el mueble.',
                'solucion_aplicada.min' => 'El detalle debe contener al menos 3 caracteres.',
                'estado_fisico_resultante.required' => 'Debe seleccionar el estado físico resultante del mueble.',
                'estado_fisico_resultante.in' => 'El estado físico resultante no es válido.',
            ]);
        } else {
            $request->merge([
                'diagnostico_falla' => trim((string) $request->input('diagnostico_falla')),
            ]);

            $request->validate([
                'resultado' => ['required', 'in:reparado,irreparable'],
                'diagnostico_falla' => ['required', 'string', 'min:3', 'max:2000'],
            ], [
                'diagnostico_falla.required' => 'Debe ingresar el motivo por el cual el mueble es irreparable.',
                'diagnostico_falla.min' => 'El motivo debe contener al menos 3 caracteres.',
            ]);
        }

        try {
            return DB::transaction(function () use ($request, $mantenimiento, $resultado): RedirectResponse {
                $mantenimientoBloqueado = MantenimientoMobiliario::lockForUpdate()->findOrFail($mantenimiento->id);

                if ($mantenimientoBloqueado->estado !== MantenimientoMobiliario::ESTADO_EN_PROCESO) {
                    return redirect()->back()->with('error', 'Este mantenimiento ya no se encuentra en proceso.');
                }

                // Bloquear primero la asignación si existe para mantener el mismo orden de locks (Asignacion -> Mobiliario) y evitar deadlocks (M4)
                $asignacionActiva = null;
                if ($mantenimientoBloqueado->asignacion_id) {
                    $asignacionActiva = Asignacion::lockForUpdate()->find($mantenimientoBloqueado->asignacion_id);
                } else {
                    $asignacionActiva = Asignacion::where('tipo', Asignacion::TIPO_MOBILIARIO)
                        ->where('id_referencia', $mantenimientoBloqueado->mobiliario_id)
                        ->whereNull('deleted_at')
                        ->whereDoesntHave('devolucion')
                        ->lockForUpdate()
                        ->first();
                }

                $mobiliario = Mobiliario::withTrashed()->lockForUpdate()->findOrFail($mantenimientoBloqueado->mobiliario_id);

                if ($resultado === 'reparado') {
                    $estadoResultante = (string) $request->input('estado_fisico_resultante');
                    $solucion = (string) $request->input('solucion_aplicada');

                    // Revalidar atómicamente dentro del bloqueo pesimista contra TOCTOU
                    $puedeConservarNuevo = ($mantenimientoBloqueado->estado_fisico_anterior === 'Nuevo / En perfectas condiciones') && ! $mobiliario->tieneHistorialAsignaciones();
                    if ($estadoResultante === 'Nuevo / En perfectas condiciones' && ! $puedeConservarNuevo) {
                        $estadoResultante = 'Usado / Segunda mano';
                    }

                    $operador = Auth::user()?->name ?? 'Administración';
                    $fechaCierre = now()->format('d/m/Y h:i A');
                    $solucionConFirma = $solucion."\n[Finalizado por {$operador} el {$fechaCierre}]";

                    $mantenimientoBloqueado->update([
                        'estado' => MantenimientoMobiliario::ESTADO_REPARADO,
                        'fecha_fin' => now(),
                        'solucion_aplicada' => $solucionConFirma,
                        'estado_fisico_resultante' => $estadoResultante,
                    ]);

                    // Recalcular disponibilidad según asignaciones realmente activas y preservar Área Común (🔴-2 & 🟡-1)
                    $tieneCustodiaActiva = $mobiliario->asignacionesActivas()->exists();
                    $esAreaComun = (bool) ($mantenimientoBloqueado->es_area_comun || ($asignacionActiva?->es_area_comun ?? $mobiliario->esEquipoAreaComun()));

                    $mobiliario->estado = $estadoResultante;
                    $mobiliario->disponibilidad = Mobiliario::calcularDisponibilidad(
                        $mobiliario->estado,
                        $esAreaComun,
                        $tieneCustodiaActiva
                    );
                    $mobiliario->save();

                    return redirect()->route('mobiliario.historial', $mobiliario->id)
                        ->with('success', "Mantenimiento finalizado con éxito. El mobiliario {$mobiliario->etiqueta} volvió a estar operativo.");
                }

                // CASO: Daño irreparable (sin solución)
                $diagnostico = (string) $request->input('diagnostico_falla');
                $operador = Auth::user()?->name ?? 'Administración';
                $fechaCierre = now()->format('d/m/Y h:i A');

                $mantenimientoBloqueado->update([
                    'estado' => MantenimientoMobiliario::ESTADO_IRREPARABLE,
                    'fecha_fin' => now(),
                    'solucion_aplicada' => "Sin solución / Irreparable: {$diagnostico}\n[Dictamen emitido por {$operador} el {$fechaCierre}]",
                    'estado_fisico_resultante' => 'Dañado / Defectuoso',
                ]);

                // Si estaba asignado o tiene devolución previa, sincronizar trazabilidad de devolución (🟡-13)
                $asignacionADevolver = $asignacionActiva;
                if ($asignacionADevolver) {
                    $devolucionExistente = $asignacionADevolver->devolucion()->lockForUpdate()->first();
                    if ($devolucionExistente) {
                        $devolucionExistente->update([
                            'estado_devuelto' => 'Dañado / Defectuoso',
                            'observaciones' => \Illuminate\Support\Str::limit(
                                rtrim($devolucionExistente->observaciones ?? '')."\n[Actualización Taller] Declarado irreparable: {$diagnostico}",
                                950
                            ),
                        ]);
                        if (! $mantenimientoBloqueado->devolucion_id) {
                            $mantenimientoBloqueado->update(['devolucion_id' => $devolucionExistente->id]);
                        }
                    } else {
                        $devolucion = Devolucion::create([
                            'asignacion_id' => $asignacionADevolver->id,
                            'fecha_devolucion' => now()->toDateString(),
                            'estado_devuelto' => 'Dañado / Defectuoso',
                            'estado_anterior' => $mantenimientoBloqueado->estado_fisico_anterior ?? $mobiliario->estado,
                            'recibido_por' => Auth::user()->name ?? 'Administración',
                            'observaciones' => \Illuminate\Support\Str::limit("Devuelto por daño irreparable en taller de mobiliario: {$diagnostico}", 950),
                        ]);

                        $mantenimientoBloqueado->update(['devolucion_id' => $devolucion->id]);
                    }
                }

                $mobiliario->estado = 'Dañado / Defectuoso';

                $mobiliario->update([
                    'motivo_baja' => \Illuminate\Support\Str::limit("Daño irreparable en mantenimiento: {$diagnostico}", 480),
                    'baja_por_user_id' => Auth::id(),
                    'disponibilidad' => Mobiliario::DISPONIBILIDAD_NO_APLICA,
                ]);

                if (! $mobiliario->trashed()) {
                    $mobiliario->delete();
                }

                return redirect()->route('mobiliario.historial', $mobiliario->id)
                    ->with('success', "Mantenimiento cerrado como irreparable. Mobiliario {$mobiliario->etiqueta} trasladado al archivo de bajas.");
            });
        } catch (\Throwable $e) {
            report($e);

            $mensajeError = $e instanceof \Illuminate\Database\QueryException
                ? 'Ocurrió un error en la base de datos al procesar el cierre del mantenimiento.'
                : ($e instanceof \RuntimeException
                    ? $e->getMessage()
                    : 'Error inesperado al procesar el cierre del mantenimiento. Intente nuevamente.');

            return redirect()->back()
                ->withInput()
                ->with('error', $mensajeError);
        }
    }

    /**
     * Cancelar un mantenimiento registrado por error.
     */
    public function cancelar(Request $request, MantenimientoMobiliario $mantenimiento): RedirectResponse
    {
        Gate::authorize('admin');

        return DB::transaction(function () use ($request, $mantenimiento): RedirectResponse {
            $mantenimientoBloqueado = MantenimientoMobiliario::lockForUpdate()->findOrFail($mantenimiento->id);

            if ($mantenimientoBloqueado->estado !== MantenimientoMobiliario::ESTADO_EN_PROCESO) {
                return redirect()->back()->with('error', 'Solo se pueden cancelar mantenimientos en proceso.');
            }

            $mobiliario = Mobiliario::withTrashed()->lockForUpdate()->findOrFail($mantenimientoBloqueado->mobiliario_id);

            // Bloquear cancelación si el bien está en bajas (🟡-4)
            if ($mobiliario->trashed()) {
                return redirect()->back()->with('error', 'No se puede cancelar el mantenimiento de un mueble que se encuentra en el archivo de bajas.');
            }

            $motivoCancelacion = trim((string) $request->input('motivo_cancelacion'));
            $operador = Auth::user()?->name ?? 'Administración';
            $fechaCancelacion = now()->format('d/m/Y h:i A');

            $solucion = $motivoCancelacion !== ''
                ? "Mantenimiento cancelado: {$motivoCancelacion}\n[Cancelado por {$operador} el {$fechaCancelacion}]"
                : "Mantenimiento cancelado por {$operador} el {$fechaCancelacion}.";

            $mantenimientoBloqueado->update([
                'estado' => MantenimientoMobiliario::ESTADO_CANCELADO,
                'fecha_fin' => now(),
                'solucion_aplicada' => $solucion,
            ]);

            // Restaurar estado físico previo del mueble preservando Área Común (🟡-1 & 🟡-2)
            if ($mantenimientoBloqueado->estado_fisico_anterior) {
                $tieneAsignacion = $mobiliario->asignacionesActivas()->exists();
                $asignacionActiva = $mantenimientoBloqueado->asignacion_id ? Asignacion::find($mantenimientoBloqueado->asignacion_id) : null;
                $esAreaComun = (bool) ($mantenimientoBloqueado->es_area_comun || ($asignacionActiva?->es_area_comun ?? $mobiliario->esEquipoAreaComun()));

                $mobiliario->estado = $mantenimientoBloqueado->estado_fisico_anterior;
                $mobiliario->disponibilidad = Mobiliario::calcularDisponibilidad(
                    $mobiliario->estado,
                    $esAreaComun,
                    $tieneAsignacion
                );
                $mobiliario->save();
            }

            return redirect()->route('mobiliario.historial', $mobiliario->id)
                ->with('info', 'El registro de mantenimiento de mobiliario fue cancelado.');
        });
    }
}
