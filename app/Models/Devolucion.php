<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Devolucion extends Model
{
    public const ESTADOS_DEVOLUCION = [
        'Con pequeños detalles / Imperfecciones leves',
        'Usado / Segunda mano',
        'Dañado / Defectuoso',
        'En reparación / En revisión',
        'Producto incompleto',
        'Caducado / No apto para uso',
    ];

    protected $table = 'devoluciones';

    protected $fillable = [
        'asignacion_id',
        'fecha_devolucion',
        'estado_devuelto',
        'recibido_por',
        'observaciones',
    ];

    protected $casts = [
        'fecha_devolucion' => 'date',
    ];

    public function asignacion(): BelongsTo
    {
        return $this->belongsTo(Asignacion::class);
    }
}
