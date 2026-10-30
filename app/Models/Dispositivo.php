<?php

namespace App\Models;

use App\Models\Concerns\TieneEstadoInventario;
use Illuminate\Database\Eloquent\Model;

class Dispositivo extends Model
{
    use TieneEstadoInventario;

    public const TIPO_ASIGNACION = Asignacion::TIPO_DISPOSITIVO;

    public const ETIQUETA_BIEN = 'dispositivo';

    public const TIPOS = [
        'Laptop',
        'Computadora de escritorio',
        'Proyector',
        'Impresora',
        'Escáner',
        'Monitor',
        'Teclado',
        'Mouse',
        'Tablet',
        'Switch de red',
        'Router',
        'Access Point (AP)',
        'Servidor',
        'Disco duro externo',
        'No-break / UPS',
        'Cámara Web',
        'Micrófono',
        'Sistema de sonido',
        'Pantalla interactiva',
        'Control remoto',
        'Cargador de laptop',
        'Cable HDMI',
        'Otro',
    ];

    public const TIPOS_SERIE_OPCIONAL = [
        'Teclado',
        'Mouse',
        'Disco duro externo',
        'Cámara Web',
        'Micrófono',
        'Sistema de sonido',
        'Control remoto',
        'Cargador de laptop',
        'Cable HDMI',
        'Otro',
    ];

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

    protected $casts = [
        'fecha_registro' => 'date',
    ];

    public static function requiereSerie(?string $tipo): bool
    {
        if (! $tipo) {
            return true;
        }

        return ! in_array($tipo, self::TIPOS_SERIE_OPCIONAL, true);
    }

    public static function normalizarSerie(?string $serie): ?string
    {
        if ($serie === null) {
            return null;
        }

        $limpio = trim($serie);

        if ($limpio === '' || in_array(mb_strtoupper($limpio), ['N/A', 'NA', 'S/N', 'SN', 'SIN SERIE', '-'], true)) {
            return null;
        }

        return $limpio;
    }
}
