<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Devolucion;
use App\Models\Dispositivo;
use App\Models\MantenimientoDispositivo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class MantenimientoDispositivoController extends Controller
{
    /**
     * Registrar el ingreso de un dispositivo a taller / mantenimiento.
     */
    public function store(Request $request, Dispositivo $dispositivo): RedirectResponse
    {
        Gate::authorize('admin');

        $request->merge([
            'motivo_falla' => trim((string) $request->input('motivo_falla')),
            'taller_proveedor' => trim((string) $request->input('taller_proveedor')),
        ]);

        $validated = $request->validate([
            'tipo' => ['required', 'string', Rule::in(MantenimientoDispositivo::TIPOS)],
            'motivo_falla' => ['required', 'string', 'min:3', 'max:2000'],
            'taller_proveedor' => ['nullable', 'string', 'max:255'],
        ], [
            'tipo.required' => 'Debe seleccionar el tipo de mantenimiento.',
            'tipo.in' => 'El tipo de mantenimiento seleccionado no es válido.',
            'motivo_falla.required' => 'Debe describir la falla o motivo del mantenimiento.',
            'motivo_falla.min' => 'El motivo debe contener al menos 3 caracteres.',
            'motivo_falla.max' => 'El motivo no puede exceder 2000 caracteres.',
        ]);

        return DB::transaction(function () use ($dispositivo, $validated): RedirectResponse {
            $dispositivoBloqueado = Dispositivo::withTrashed()->lockForUpdate()->findOrFail($dispositivo->id);

            // Bloquear ingreso a taller si el dispositivo está en el archivo de bajas (🔴-1)
            if ($dispositivoBloqueado->trashed()) {
                return redirect()->back()->with('error', 'No se puede ingresar a mantenimiento un dispositivo dado de baja. Debe reactivarlo formalmente desde el Archivo de Bajas antes de enviarlo a taller.');
            }

            // Verificar si ya tiene un mantenimiento activo en proceso
            if ($dispositivoBloqueado->mantenimientoActivo()->exists()) {
                return redirect()->back()->with('error', 'Este dispositivo ya tiene un mantenimiento activo en proceso.');
            }

            $asignacionActiva = $dispositivoBloqueado->asignacionesActivas()->first();
            $estadoAnterior = $dispositivoBloqueado->estado;
            $esAreaComun = (bool) ($asignacionActiva?->es_area_comun ?? $dispositivoBloqueado->esEquipoAreaComun());

            MantenimientoDispositivo::create([
                'dispositivo_id' => $dispositivoBloqueado->id,
                'user_id' => Auth::id(),
                'asignacion_id' => $asignacionActiva?->id,
                'empleado_id' => $asignacionActiva?->empleado_id,
                'tipo' => $validated['tipo'],
                'motivo_falla' => $validated['motivo_falla'],
                'taller_proveedor' => $validated['taller_proveedor'] ?: null,
                'estado' => MantenimientoDispositivo::ESTADO_EN_PROCESO,
                'fecha_inicio' => now(),
                'estado_fisico_anterior' => $estadoAnterior,
                'es_area_comun' => $esAreaComun,
            ]);

            // Actualizar estado físico a "En reparación / En revisión" preservando condición de área común
            $dispositivoBloqueado->estado = 'En reparación / En revisión';
            $dispositivoBloqueado->disponibilidad = Dispositivo::calcularDisponibilidad(
                $dispositivoBloqueado->estado,
                $esAreaComun,
                $asignacionActiva !== null
            );
            $dispositivoBloqueado->save();

            return redirect()->route('dispositivos.historial', $dispositivoBloqueado->id)
                ->with('success', "Dispositivo {$dispositivoBloqueado->etiqueta} ingresado a taller / mantenimiento correctamente.");
        });
    }

    /**
     * Finalizar el mantenimiento (con éxito o declarándolo irreparable).
     */
    public function finalizar(Request $request, MantenimientoDispositivo $mantenimiento): RedirectResponse
    {
        Gate::authorize('admin');

        $resultado = (string) $request->input('resultado');

        if ($resultado === 'reparado') {
            $request->merge([
                'solucion_aplicada' => trim((string) $request->input('solucion_aplicada')),
            ]);

            $dispositivo = $mantenimiento->dispositivo;
            $puedeConservarNuevo = ($mantenimiento->estado_fisico_anterior === 'Nuevo / En perfectas condiciones') && ! $dispositivo->tieneHistorialAsignaciones();

            $estadosPermitidos = [
                'Usado / Segunda mano',
            ];

            if ($puedeConservarNuevo) {
                $estadosPermitidos[] = 'Nuevo / En perfectas condiciones';
            }

            $reglasValidacion = [
                'resultado' => ['required', 'in:reparado,irreparable'],
                'solucion_aplicada' => ['required', 'string', 'min:3', 'max:2000'],
                'estado_fisico_resultante' => ['required', 'string', Rule::in($estadosPermitidos)],
            ];

            if ($request->boolean('hubo_cambio_hardware')) {
                $reglasValidacion['almacenamiento'] = ['nullable', 'string', 'max:100'];
                $reglasValidacion['ram'] = ['nullable', 'string', 'max:50'];
                $reglasValidacion['procesador'] = ['nullable', 'string', 'max:100'];
                $reglasValidacion['puertos_red'] = ['nullable', 'string', 'max:50'];
                $reglasValidacion['capacidad_ups'] = ['nullable', 'string', 'max:50'];
                $reglasValidacion['notas_tecnicas'] = ['nullable', 'string', 'max:500'];
            }

            $request->validate($reglasValidacion, [
                'solucion_aplicada.required' => 'Debe detallar la solución o trabajo técnico realizado.',
                'solucion_aplicada.min' => 'La solución debe contener al menos 3 caracteres.',
                'estado_fisico_resultante.required' => 'Debe seleccionar el estado físico resultante del equipo.',
                'estado_fisico_resultante.in' => 'El estado físico resultante seleccionado no es válido.',
            ]);
        } else {
            $request->merge([
                'diagnostico_falla' => trim((string) $request->input('diagnostico_falla')),
            ]);

            $request->validate([
                'resultado' => ['required', 'in:reparado,irreparable'],
                'diagnostico_falla' => ['required', 'string', 'min:3', 'max:2000'],
            ], [
                'diagnostico_falla.required' => 'Debe ingresar el diagnóstico de la falla irreparable.',
                'diagnostico_falla.min' => 'El diagnóstico debe contener al menos 3 caracteres.',
            ]);
        }

        try {
            return DB::transaction(function () use ($request, $mantenimiento, $resultado): RedirectResponse {
                $mantenimientoBloqueado = MantenimientoDispositivo::lockForUpdate()->findOrFail($mantenimiento->id);

                if ($mantenimientoBloqueado->estado !== MantenimientoDispositivo::ESTADO_EN_PROCESO) {
                    return redirect()->back()->with('error', 'Este mantenimiento ya no se encuentra en proceso.');
                }

                // Bloquear primero la asignación si existe para mantener el mismo orden de locks (Asignacion -> Dispositivo) y evitar deadlocks (M4)
                $asignacionActiva = null;
                if ($mantenimientoBloqueado->asignacion_id) {
                    $asignacionActiva = Asignacion::lockForUpdate()->find($mantenimientoBloqueado->asignacion_id);
                } else {
                    $asignacionActiva = Asignacion::where('tipo', Asignacion::TIPO_DISPOSITIVO)
                        ->where('id_referencia', $mantenimientoBloqueado->dispositivo_id)
                        ->whereNull('deleted_at')
                        ->whereDoesntHave('devolucion')
                        ->lockForUpdate()
                        ->first();
                }

                $dispositivo = Dispositivo::withTrashed()->lockForUpdate()->findOrFail($mantenimientoBloqueado->dispositivo_id);

                if ($resultado === 'reparado') {
                    $estadoResultante = (string) $request->input('estado_fisico_resultante');
                    $solucion = (string) $request->input('solucion_aplicada');

                    // Revalidar atómicamente dentro del bloqueo pesimista contra TOCTOU (🟡-2)
                    $puedeConservarNuevo = ($mantenimientoBloqueado->estado_fisico_anterior === 'Nuevo / En perfectas condiciones') && ! $dispositivo->tieneHistorialAsignaciones();
                    if ($estadoResultante === 'Nuevo / En perfectas condiciones' && ! $puedeConservarNuevo) {
                        $estadoResultante = 'Usado / Segunda mano';
                    }

                    $operador = Auth::user()?->name ?? 'Administración TI';
                    $fechaCierre = now()->format('d/m/Y h:i A');
                    $solucionConFirma = $solucion."\n[Finalizado por {$operador} el {$fechaCierre}]";

                    $mantenimientoBloqueado->update([
                        'estado' => MantenimientoDispositivo::ESTADO_REPARADO,
                        'fecha_fin' => now(),
                        'solucion_aplicada' => $solucionConFirma,
                        'estado_fisico_resultante' => $estadoResultante,
                    ]);

                    // Si se indicó reemplazo o actualización de piezas en este mantenimiento
                    $mensajeHw = '';
                    if ($request->boolean('hubo_cambio_hardware')) {
                        $nuevasSpecs = is_array($dispositivo->especificaciones) ? $dispositivo->especificaciones : [];
                        if ($request->filled('almacenamiento')) {
                            $nuevasSpecs['almacenamiento'] = trim((string) $request->input('almacenamiento'));
                        }
                        if ($request->filled('ram')) {
                            $nuevasSpecs['ram'] = trim((string) $request->input('ram'));
                        }
                        if ($request->filled('procesador')) {
                            $nuevasSpecs['procesador'] = trim((string) $request->input('procesador'));
                        }
                        if ($request->filled('puertos_red')) {
                            $nuevasSpecs['puertos_red'] = trim((string) $request->input('puertos_red'));
                        }
                        if ($request->filled('capacidad_ups')) {
                            $nuevasSpecs['capacidad_ups'] = trim((string) $request->input('capacidad_ups'));
                        }
                        if ($request->filled('notas_tecnicas')) {
                            $nuevasSpecs['notas_tecnicas'] = trim((string) $request->input('notas_tecnicas'));
                        }

                        $cambiosDetectados = \App\Models\HistorialCambioHardware::detectarCambios(
                            $dispositivo->especificaciones ?? [],
                            $nuevasSpecs
                        );

                        if (! empty($cambiosDetectados)) {
                            \App\Models\HistorialCambioHardware::create([
                                'dispositivo_id' => $dispositivo->id,
                                'user_id' => Auth::id(),
                                'asignacion_id' => $mantenimientoBloqueado->asignacion_id,
                                'empleado_id' => $mantenimientoBloqueado->empleado_id,
                                'especificaciones_anteriores' => $dispositivo->especificaciones,
                                'especificaciones_nuevas' => $nuevasSpecs,
                                'cambios' => $cambiosDetectados,
                                'motivo' => \Illuminate\Support\Str::limit("Reemplazo por mantenimiento técnico: {$solucion}", 480),
                            ]);

                            $dispositivo->especificaciones = $nuevasSpecs;
                            $mensajeHw = ' y se actualizó la ficha técnica de hardware';
                        }
                    }

                    // Recalcular disponibilidad según asignaciones realmente activas y condición de área común (🔴-2 & 🟡-1)
                    $tieneCustodiaActiva = $dispositivo->asignacionesActivas()->exists();
                    $esAreaComun = (bool) ($mantenimientoBloqueado->es_area_comun || ($asignacionActiva?->es_area_comun ?? $dispositivo->esEquipoAreaComun()));

                    $dispositivo->estado = $estadoResultante;
                    $dispositivo->disponibilidad = Dispositivo::calcularDisponibilidad(
                        $dispositivo->estado,
                        $esAreaComun,
                        $tieneCustodiaActiva
                    );
                    $dispositivo->save();

                    return redirect()->route('dispositivos.historial', $dispositivo->id)
                        ->with('success', "Mantenimiento finalizado con éxito{$mensajeHw}. El dispositivo {$dispositivo->etiqueta} volvió a estar operativo.");
                }

                // CASO: Daño irreparable (sin solución)
                $diagnostico = (string) $request->input('diagnostico_falla');
                $operador = Auth::user()?->name ?? 'Administración TI';
                $fechaCierre = now()->format('d/m/Y h:i A');

                $mantenimientoBloqueado->update([
                    'estado' => MantenimientoDispositivo::ESTADO_IRREPARABLE,
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
                            'estado_anterior' => $mantenimientoBloqueado->estado_fisico_anterior ?? $dispositivo->estado,
                            'recibido_por' => Auth::user()->name ?? 'Administración TI',
                            'observaciones' => \Illuminate\Support\Str::limit("Devuelto por daño irreparable en revisión técnica: {$diagnostico}", 950),
                        ]);

                        $mantenimientoBloqueado->update(['devolucion_id' => $devolucion->id]);
                    }
                }

                $dispositivo->estado = 'Dañado / Defectuoso';

                // Mover directamente al archivo de bajas por daño irreparable con límite seguro (A1)
                $dispositivo->update([
                    'motivo_baja' => \Illuminate\Support\Str::limit("Daño irreparable en mantenimiento: {$diagnostico}", 480),
                    'baja_por_user_id' => Auth::id(),
                    'disponibilidad' => Dispositivo::DISPONIBILIDAD_NO_APLICA,
                ]);

                if (! $dispositivo->trashed()) {
                    $dispositivo->delete();
                }

                return redirect()->route('dispositivos.historial', $dispositivo->id)
                    ->with('success', "Mantenimiento cerrado como irreparable. Custodia liberada y dispositivo {$dispositivo->etiqueta} trasladado al archivo de bajas.");
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
    public function cancelar(Request $request, MantenimientoDispositivo $mantenimiento): RedirectResponse
    {
        Gate::authorize('admin');

        return DB::transaction(function () use ($request, $mantenimiento): RedirectResponse {
            $mantenimientoBloqueado = MantenimientoDispositivo::lockForUpdate()->findOrFail($mantenimiento->id);

            if ($mantenimientoBloqueado->estado !== MantenimientoDispositivo::ESTADO_EN_PROCESO) {
                return redirect()->back()->with('error', 'Solo se pueden cancelar mantenimientos en proceso.');
            }

            $dispositivo = Dispositivo::withTrashed()->lockForUpdate()->findOrFail($mantenimientoBloqueado->dispositivo_id);

            // Bloquear cancelación si el bien está en bajas (🟡-4)
            if ($dispositivo->trashed()) {
                return redirect()->back()->with('error', 'No se puede cancelar el mantenimiento de un dispositivo que se encuentra en el archivo de bajas.');
            }

            $motivoCancelacion = trim((string) $request->input('motivo_cancelacion'));
            $operador = Auth::user()?->name ?? 'Administración TI';
            $fechaCancelacion = now()->format('d/m/Y h:i A');

            $solucion = $motivoCancelacion !== ''
                ? "Mantenimiento cancelado: {$motivoCancelacion}\n[Cancelado por {$operador} el {$fechaCancelacion}]"
                : "Mantenimiento cancelado por {$operador} el {$fechaCancelacion}.";

            $mantenimientoBloqueado->update([
                'estado' => MantenimientoDispositivo::ESTADO_CANCELADO,
                'fecha_fin' => now(),
                'solucion_aplicada' => $solucion,
            ]);

            // Restaurar estado físico previo del dispositivo preservando Área Común (🟡-1 & 🟡-2)
            if ($mantenimientoBloqueado->estado_fisico_anterior) {
                $tieneAsignacion = $dispositivo->asignacionesActivas()->exists();
                $asignacionActiva = $mantenimientoBloqueado->asignacion_id ? \App\Models\Asignacion::find($mantenimientoBloqueado->asignacion_id) : null;
                $esAreaComun = (bool) ($mantenimientoBloqueado->es_area_comun || ($asignacionActiva?->es_area_comun ?? $dispositivo->esEquipoAreaComun()));

                $dispositivo->estado = $mantenimientoBloqueado->estado_fisico_anterior;
                $dispositivo->disponibilidad = Dispositivo::calcularDisponibilidad(
                    $dispositivo->estado,
                    $esAreaComun,
                    $tieneAsignacion
                );
                $dispositivo->save();
            }

            return redirect()->route('dispositivos.historial', $dispositivo->id)
                ->with('info', 'El registro de mantenimiento fue cancelado.');
        });
    }
}
