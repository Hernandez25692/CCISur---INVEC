<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialCambioHardware extends Model
{
    protected $table = 'historial_cambios_hardware';

    protected $fillable = [
        'dispositivo_id',
        'user_id',
        'asignacion_id',
        'empleado_id',
        'especificaciones_anteriores',
        'especificaciones_nuevas',
        'cambios',
        'motivo',
        'revertido',
        'revertido_por',
        'revertido_at',
        'motivo_reversion',
    ];

    protected $casts = [
        'especificaciones_anteriores' => 'array',
        'especificaciones_nuevas' => 'array',
        'cambios' => 'array',
        'revertido' => 'boolean',
        'revertido_at' => 'datetime',
    ];

    public const ETIQUETAS_CAMPOS = [
        'procesador' => 'Procesador',
        'ram' => 'Memoria RAM',
        'almacenamiento' => 'Almacenamiento',
        'sistema_operativo' => 'Sistema Operativo',
        'puertos_red' => 'Puertos de Red',
        'velocidad_red' => 'Velocidad de Red',
        'poe' => 'Alimentación PoE',
        'gestion_red' => 'Gestión de Red',
        'tamano_pantalla' => 'Tamaño de Pantalla',
        'conectores_pantalla' => 'Conectores de Pantalla',
        'tecnologia_impresion' => 'Tecnología de Impresión',
        'conectividad_impresion' => 'Conectividad de Impresión',
        'capacidad_ups' => 'Capacidad Eléctrica',
        'notas_tecnicas' => 'Notas Técnicas',
    ];

    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(Dispositivo::class, 'dispositivo_id')->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(Asignacion::class, 'asignacion_id')->withTrashed();
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id')->withTrashed();
    }

    public function revertidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revertido_por');
    }

    /**
     * Compara dos arreglos de especificaciones y genera el listado estructurado de cambios.
     */
    public static function detectarCambios(?array $antes, ?array $despues): array
    {
        $antes = is_array($antes) ? $antes : [];
        $despues = is_array($despues) ? $despues : [];

        $todasLasClaves = array_unique(array_merge(array_keys($antes), array_keys($despues)));
        $cambios = [];

        foreach ($todasLasClaves as $clave) {
            $valAntes = isset($antes[$clave]) && is_scalar($antes[$clave]) ? trim((string) $antes[$clave]) : null;
            $valDespues = isset($despues[$clave]) && is_scalar($despues[$clave]) ? trim((string) $despues[$clave]) : null;

            if ($valAntes !== $valDespues) {
                $cambios[$clave] = [
                    'campo' => $clave,
                    'etiqueta' => self::ETIQUETAS_CAMPOS[$clave] ?? ucfirst(str_replace('_', ' ', $clave)),
                    'antes' => filled($valAntes) ? $valAntes : 'Sin registrar',
                    'despues' => filled($valDespues) ? $valDespues : 'Eliminado / Vacío',
                ];
            }
        }

        return $cambios;
    }
}
