<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
