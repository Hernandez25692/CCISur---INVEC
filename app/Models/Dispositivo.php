<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
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
}
