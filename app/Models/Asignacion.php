<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Asignacion extends Model
{
    public const TIPO_MOBILIARIO = 'mobiliario';

    public const TIPO_DISPOSITIVO = 'dispositivo';

    protected $fillable = [
        'tipo',
        'id_referencia',
        'mobiliario_id',
        'dispositivo_id',
        'empleado_id',
        'area',
        'observaciones',
        'fecha_entrega',
        'entregado_por',
    ];

    protected $casts = [
        'fecha_entrega' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->whereDoesntHave('devolucion');
    }

    public function devolucion(): HasOne
    {
        return $this->hasOne(Devolucion::class);
    }

    public function mobiliario(): BelongsTo
    {
        return $this->belongsTo(Mobiliario::class, 'id_referencia');
    }

    public function dispositivo(): BelongsTo
    {
        return $this->belongsTo(Dispositivo::class, 'id_referencia');
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function getNombreBienAttribute(): string
    {
        $bien = $this->tipo === self::TIPO_MOBILIARIO ? $this->mobiliario : $this->dispositivo;

        return $bien?->nombre ?? 'N/A';
    }

    public function getEtiquetaBienAttribute(): string
    {
        $bien = $this->tipo === self::TIPO_MOBILIARIO ? $this->mobiliario : $this->dispositivo;

        return $bien?->etiqueta ?? 'N/A';
    }
}
