<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Asignacion extends Model
{
    protected $fillable = [
        'tipo',
        'id_referencia',
        'colaborador',
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
}
