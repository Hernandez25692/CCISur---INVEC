<?php

namespace App\Models;

use App\Models\Concerns\TieneEstadoInventario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dispositivo extends Model
{
    use SoftDeletes;
    use TieneEstadoInventario;

    public const TIPO_ASIGNACION = Asignacion::TIPO_DISPOSITIVO;

    public const ETIQUETA_BIEN = 'dispositivo';

    public const UBICACIONES = Empleado::UBICACIONES;

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

    public const OPCIONES_RAM = [
        '4 GB',
        '8 GB',
        '16 GB',
        '24 GB',
        '32 GB',
        '64 GB',
        'Otro',
    ];

    public const OPCIONES_ALMACENAMIENTO = [
        '128 GB SSD',
        '256 GB SSD',
        '512 GB SSD',
        '1 TB SSD',
        '1 TB HDD',
        '2 TB HDD',
        'Otro',
    ];

    public const OPCIONES_SO = [
        'Windows 11 Pro',
        'Windows 11 Home',
        'Windows 10 Pro',
        'macOS',
        'Linux / Ubuntu',
        'Sin S.O.',
        'Otro',
    ];

    public const OPCIONES_PUERTOS_RED = [
        '5 Puertos',
        '8 Puertos',
        '16 Puertos',
        '24 Puertos',
        '48 Puertos',
        'Otro',
    ];

    public const OPCIONES_VELOCIDAD_RED = [
        'Gigabit (10/100/1000 Mbps)',
        'Fast Ethernet (10/100 Mbps)',
        '10 Gigabit (10G)',
        'Otro',
    ];

    public const OPCIONES_POE = [
        'No PoE',
        'PoE (802.3af)',
        'PoE+ (802.3at)',
        'Otro',
    ];

    public const OPCIONES_GESTION_RED = [
        'Administrable (Managed)',
        'No Administrable',
    ];

    public const OPCIONES_TAMANO_PANTALLA = [
        '19"',
        '21.5"',
        '24"',
        '27"',
        '32"',
        '43"',
        '55"',
        '65"',
        'Otro',
    ];

    public const OPCIONES_TECNOLOGIA_IMPRESION = [
        'Láser Monocromática',
        'Láser a Color',
        'Inyección de Tinta / Continua',
        'Térmica',
        'Otro',
    ];

    public const OPCIONES_CONECTIVIDAD_IMPRESION = [
        'Red (Ethernet + Wi-Fi)',
        'Wi-Fi + USB',
        'Solo USB',
        'Otro',
    ];

    public const OPCIONES_CAPACIDAD_UPS = [
        '500 VA',
        '650 VA',
        '800 VA',
        '1000 VA',
        '1500 VA',
        '2000 VA',
        'Otro',
    ];

    public const MOTIVOS_BAJA_SUGERIDOS = [
        'Daño irreparable / Falla técnica definitiva',
        'Obsolescencia técnica / Fin de vida útil',
        'Pérdida / Extravío o Robo reportado',
        'Donación o Descarte formal',
        'Reemplazo por renovación institucional',
    ];

    protected $fillable = [
        'nombre',
        'tipo',
        'marca',
        'modelo',
        'n_serie',
        'especificaciones',
        'etiqueta',
        'ubicacion',
        'estado',
        'disponibilidad',
        'fecha_registro',
        'motivo_baja',
        'baja_por_user_id',
        'reactivado_por_user_id',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'especificaciones' => 'array',
    ];

    public static function requiereSerie(?string $tipo): bool
    {
        if (! $tipo) {
            return true;
        }

        return ! in_array($tipo, self::TIPOS_SERIE_OPCIONAL, true);
    }

    /**
     * Resuelve si un valor técnico coincide con una opción predefinida o debe abrir el campo 'Otro'.
     *
     * @return array{select: string, otro: string}
     */
    public static function resolverOpcionSelect(?string $valor, array $opciones): array
    {
        if (blank($valor)) {
            return ['select' => '', 'otro' => ''];
        }

        $opcionesNormales = array_diff($opciones, ['Otro']);

        if (in_array($valor, $opcionesNormales, true)) {
            return ['select' => $valor, 'otro' => ''];
        }

        return ['select' => 'Otro', 'otro' => $valor];
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

    public function cambiosHardware(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HistorialCambioHardware::class, 'dispositivo_id')->latest('id');
    }

    /**
     * Determina la categoría de hardware según el tipo de dispositivo para la UI reactiva.
     */
    public function getCategoriaHardwareAttribute(): string
    {
        return self::obtenerCategoriaHardware($this->tipo);
    }

    public static function obtenerCategoriaHardware(?string $tipo): string
    {
        return match ($tipo) {
            'Laptop', 'Computadora de escritorio', 'Servidor', 'Tablet' => 'computadora',
            'Switch de red', 'Router', 'Access Point (AP)' => 'red',
            'Monitor', 'Pantalla interactiva' => 'pantalla',
            'Impresora', 'Escáner' => 'impresion',
            'No-break / UPS' => 'ups',
            default => 'general',
        };
    }

    /**
     * Genera un texto resumen amigable y conciso de las especificaciones técnicas del dispositivo.
     */
    public function getEspecificacionesTextoAttribute(): ?string
    {
        $esp = $this->especificaciones;
        if (! is_array($esp) || empty($esp)) {
            return null;
        }

        $partes = [];

        // Especificaciones de Computadoras / Servidores
        if (! empty($esp['procesador'])) {
            $partes[] = $esp['procesador'];
        }
        if (! empty($esp['ram'])) {
            $partes[] = str_contains($esp['ram'], 'RAM') ? $esp['ram'] : "{$esp['ram']} RAM";
        }
        if (! empty($esp['almacenamiento'])) {
            $partes[] = $esp['almacenamiento'];
        }
        if (! empty($esp['sistema_operativo'])) {
            $partes[] = $esp['sistema_operativo'];
        }

        // Especificaciones de Red
        if (! empty($esp['puertos_red'])) {
            $partes[] = $esp['puertos_red'];
        }
        if (! empty($esp['velocidad_red'])) {
            $partes[] = $esp['velocidad_red'];
        }
        if (! empty($esp['poe']) && $esp['poe'] !== 'No PoE') {
            $partes[] = $esp['poe'];
        }
        if (! empty($esp['gestion_red'])) {
            $partes[] = $esp['gestion_red'];
        }

        // Especificaciones de Pantalla
        if (! empty($esp['tamano_pantalla'])) {
            $partes[] = $esp['tamano_pantalla'];
        }
        if (! empty($esp['conectores_pantalla'])) {
            $partes[] = $esp['conectores_pantalla'];
        }

        // Especificaciones de Impresión
        if (! empty($esp['tecnologia_impresion'])) {
            $partes[] = $esp['tecnologia_impresion'];
        }
        if (! empty($esp['conectividad_impresion'])) {
            $partes[] = $esp['conectividad_impresion'];
        }

        // Especificaciones de UPS
        if (! empty($esp['capacidad_ups'])) {
            $partes[] = $esp['capacidad_ups'];
        }

        // NOTA: 'notas_tecnicas' se excluye intencionalmente de este resumen para mantener
        // la tabla y listados compactos. Las notas se consultan directamente en la Hoja de Vida.

        return empty($partes) ? null : implode(' · ', $partes);
    }

    /**
     * Determina si el dispositivo cuenta con al menos una especificación técnica o nota registrada.
     */
    public function getTieneEspecificacionesAttribute(): bool
    {
        $esp = $this->especificaciones;
        if (! is_array($esp) || empty($esp)) {
            return false;
        }

        return ! empty($this->especificaciones_texto) || ! empty($esp['notas_tecnicas']);
    }
}
