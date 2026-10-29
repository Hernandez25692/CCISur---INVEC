<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dispositivo extends Model
{
    public const DISPONIBILIDAD_ASIGNADO = 'Asignado';

    public const DISPONIBILIDAD_SIN_ASIGNAR = 'Sin Asignar';

    public const DISPONIBILIDAD_NO_APLICA = 'No Aplica para asignación';

    protected $fillable = [
        'nombre',
        'tipo',
        'marca',
        'modelo',
        'n_serie',
        'etiqueta',
        'ubicacion',
        'estado',
        'disponibilidad',
        'fecha_registro',
    ];

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class, 'id_referencia')
            ->where('tipo', Asignacion::TIPO_DISPOSITIVO);
    }

    public function asignacionesActivas(): HasMany
    {
        return $this->asignaciones()->whereDoesntHave('devolucion');
    }

    public function scopeAsignables(Builder $query): Builder
    {
        return $query->where('disponibilidad', '!=', self::DISPONIBILIDAD_NO_APLICA);
    }

    public function scopeAsignados(Builder $query): Builder
    {
        return $query->asignables()->whereHas('asignacionesActivas');
    }

    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->asignables()->whereDoesntHave('asignacionesActivas');
    }
}
