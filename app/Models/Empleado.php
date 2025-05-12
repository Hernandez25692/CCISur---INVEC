<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $fillable = [
        'codigo',
        'identidad',
        'nombre_completo',
        'gerencia',
        'ubicacion'
    ];

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class);
    }
}
