<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MantenimientoMobiliario extends Model
{
    protected $table = 'mantenimientos_mobiliarios';

    public const TIPO_MECANICA = 'Reparación mecánica / Estructural (rodajas, pistón, rieles)';

    public const TIPO_TAPICERIA = 'Tapicería y acolchado';

    public const TIPO_CARPINTERIA = 'Carpintería, barniz y pintura';

    public const TIPO_CERRAJERIA = 'Cerrajería y cambio de chapas';

    public const TIPO_PREVENTIVO = 'Mantenimiento preventivo / Ajuste general';

    public const TIPOS = [
        self::TIPO_MECANICA,
        self::TIPO_TAPICERIA,
        self::TIPO_CARPINTERIA,
        self::TIPO_CERRAJERIA,
        self::TIPO_PREVENTIVO,
    ];

    public const ESTADO_EN_PROCESO = 'en_proceso';

    public const ESTADO_REPARADO = 'completado_reparado';

    public const ESTADO_IRREPARABLE = 'completado_irreparable';

    public const ESTADO_CANCELADO = 'cancelado';

    protected $fillable = [
        'mobiliario_id',
        'user_id',
        'asignacion_id',
        'empleado_id',
        'tipo',
        'motivo_falla',
        'taller_proveedor',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'solucion_aplicada',
        'estado_fisico_anterior',
        'es_area_comun',
        'estado_fisico_resultante',
        'devolucion_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'es_area_comun' => 'boolean',
    ];

    public function mobiliario(): BelongsTo
    {
        return $this->belongsTo(Mobiliario::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class)->withTrashed();
    }

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(Asignacion::class, 'asignacion_id')->withTrashed();
    }

    public function devolucion(): BelongsTo
    {
        return $this->belongsTo(Devolucion::class)->withTrashed();
    }

    public function getEstadoFormateadoAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_EN_PROCESO => 'En Proceso',
            self::ESTADO_REPARADO => 'Reparado',
            self::ESTADO_IRREPARABLE => 'Irreparable',
            self::ESTADO_CANCELADO => 'Cancelado',
            default => ucfirst(str_replace('_', ' ', (string) $this->estado)),
        };
    }

    public function getClaseEstadoAttribute(): string
    {
        return match ($this->estado) {
            self::ESTADO_EN_PROCESO => 'bg-amber-100 text-amber-900 ring-1 ring-inset ring-amber-600/30',
            self::ESTADO_REPARADO => 'bg-emerald-100 text-emerald-800 ring-1 ring-inset ring-emerald-600/30',
            self::ESTADO_IRREPARABLE => 'bg-red-100 text-red-800 ring-1 ring-inset ring-red-600/30',
            self::ESTADO_CANCELADO => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-600/30',
            default => 'bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-600/30',
        };
    }

    public function getCustodioTextoAttribute(): ?string
    {
        if ($this->asignacion) {
            return $this->asignacion->destinatario_texto;
        }

        if ($this->empleado) {
            return $this->empleado->nombre_completo;
        }

        return null;
    }

    public function scopeEnProceso(Builder $query): void
    {
        $query->where('estado', self::ESTADO_EN_PROCESO);
    }

    public function scopeCompletados(Builder $query): void
    {
        $query->whereIn('estado', [self::ESTADO_REPARADO, self::ESTADO_IRREPARABLE]);
    }

    public function getEstaEnProcesoAttribute(): bool
    {
        return $this->estado === self::ESTADO_EN_PROCESO;
    }

    public function getEsReparadoAttribute(): bool
    {
        return $this->estado === self::ESTADO_REPARADO;
    }

    public function getEsIrreparableAttribute(): bool
    {
        return $this->estado === self::ESTADO_IRREPARABLE;
    }
}
