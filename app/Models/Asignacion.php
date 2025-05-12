<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Asignacion extends Model
{
    protected $fillable = [
        'tipo',
        'id_referencia',
        'empleado_id',
        'area',
        'observaciones',
        'fecha_entrega',
        'entregado_por',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = Str::uuid()->toString();
        });
    }

    public function devolucion()
    {
        return $this->hasOne(\App\Models\Devolucion::class);
    }

    public function mobiliario()
    {
        return $this->belongsTo(\App\Models\Mobiliario::class, 'id_referencia');
    }

    public function dispositivo()
    {
        return $this->belongsTo(\App\Models\Dispositivo::class, 'id_referencia');
    }

    public function empleado()
    {
        return $this->belongsTo(\App\Models\Empleado::class);
    }
}
