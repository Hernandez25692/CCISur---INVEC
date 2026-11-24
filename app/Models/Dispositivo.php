<?php

namespace App\Models;

use App\Models\Concerns\TieneEstadoInventario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'Cable HDMI',
        'Adaptador / Convertidor',
    ];

    public const TIPOS_SERIE_OPCIONAL = [
        'Teclado',
        'Mouse',
        'Disco duro externo',
        'Cámara Web',
        'Micrófono',
        'Sistema de sonido',
        'Control remoto',
        'Cable HDMI',
        'Adaptador / Convertidor',
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

    public const MARCAS_COMUNES = [
        'HP',
        'Dell',
        'Lenovo',
        'Epson',
        'Canon',
        'Brother',
        'Samsung',
        'LG',
        'Apple',
        'Asus',
        'Acer',
        'Cisco',
        'TP-Link',
        'Ubiquiti',
        'Logitech',
        'Genius',
        'Kingston',
        'SanDisk',
        'APC',
        'Forza',
        'CDP',
        'Sony',
    ];

    public const MARCAS_POR_TIPO = [
        'Laptop' => ['HP', 'Dell', 'Lenovo', 'Apple', 'Asus', 'Acer'],
        'Computadora de escritorio' => ['HP', 'Dell', 'Lenovo', 'Apple', 'Asus', 'Acer'],
        'Servidor' => ['Dell', 'HP', 'Lenovo', 'Cisco'],
        'Tablet' => ['Apple', 'Samsung', 'Lenovo'],
        'Impresora' => ['Epson', 'HP', 'Canon', 'Brother'],
        'Escáner' => ['Epson', 'Canon', 'HP', 'Brother'],
        'Monitor' => ['Samsung', 'LG', 'Dell', 'HP', 'Asus'],
        'Pantalla interactiva' => ['Samsung', 'LG'],
        'Proyector' => ['Epson', 'Sony', 'Canon'],
        'Switch de red' => ['Cisco', 'TP-Link', 'Ubiquiti'],
        'Router' => ['Cisco', 'TP-Link', 'Ubiquiti'],
        'Access Point (AP)' => ['Ubiquiti', 'Cisco', 'TP-Link'],
        'No-break / UPS' => ['APC', 'Forza', 'CDP'],
        'Disco duro externo' => ['Kingston', 'SanDisk'],
        'Teclado' => ['Logitech', 'Genius', 'HP', 'Dell'],
        'Mouse' => ['Logitech', 'Genius', 'HP', 'Dell'],
        'Cámara Web' => ['Logitech', 'Genius'],
        'Micrófono' => ['Logitech', 'Sony', 'Genius'],
        'Sistema de sonido' => ['Logitech', 'Sony', 'Genius'],
        'Cable HDMI' => ['Genérico'],
        'Control remoto' => ['Epson', 'Samsung', 'LG', 'Genérico'],
        'Adaptador / Convertidor' => ['Genérico', 'Apple', 'Dell', 'HP'],
    ];

    public static function marcasDisponibles(): array
    {
        try {
            $enBd = self::withTrashed()
                ->whereNotNull('marca')
                ->where('marca', '!=', '')
                ->distinct()
                ->pluck('marca')
                ->all();
        } catch (\Throwable) {
            $enBd = [];
        }

        $marcasMap = [];
        foreach (array_merge(self::MARCAS_COMUNES, $enBd) as $m) {
            $mTrim = trim($m);
            if ($mTrim === '') {
                continue;
            }
            $lower = mb_strtolower($mTrim);
            if (! isset($marcasMap[$lower])) {
                $marcasMap[$lower] = $mTrim;
            }
        }

        $marcas = array_values($marcasMap);
        natcasesort($marcas);

        return array_values($marcas);
    }

    public static function normalizarMarca(?string $marca): string
    {
        $marca = trim((string) $marca);
        if ($marca === '') {
            return '';
        }

        $marcaLower = mb_strtolower($marca);

        // 1. Coincidencia insensible a mayúsculas/minúsculas con marcas conocidas o en BD
        foreach (self::marcasDisponibles() as $conocida) {
            if (mb_strtolower($conocida) === $marcaLower) {
                return $conocida;
            }
        }

        // 2. Siglas cortas (<= 3 letras, ej: IBM, APC, CDP, MSI, AOC)
        if (mb_strlen($marca) <= 3 && ctype_alpha(str_replace(['.', '-', ' '], '', $marca))) {
            return mb_strtoupper($marca);
        }

        // 3. Caso general: Title Case (ej: "anker" -> "Anker")
        return mb_convert_case($marca, MB_CASE_TITLE, 'UTF-8');
    }

    public static function tiposDisponibles(): array
    {
        try {
            $enBd = self::withTrashed()
                ->whereNotNull('tipo')
                ->where('tipo', '!=', '')
                ->where('tipo', '!=', 'Otro')
                ->distinct()
                ->pluck('tipo')
                ->all();
        } catch (\Throwable) {
            $enBd = [];
        }

        $tiposMap = [];
        foreach (array_merge(self::TIPOS, $enBd) as $t) {
            $tTrim = trim($t);
            if ($tTrim === '') {
                continue;
            }
            $lower = mb_strtolower($tTrim);
            if (! isset($tiposMap[$lower])) {
                $tiposMap[$lower] = $tTrim;
            }
        }

        $tipos = array_values($tiposMap);
        natcasesort($tipos);

        return array_values($tipos);
    }

    public static function normalizarTipo(?string $tipo): string
    {
        $tipo = trim((string) $tipo);
        if ($tipo === '') {
            return '';
        }

        $tipoLower = mb_strtolower($tipo);

        // 1. Coincidencia insensible con tipos conocidos
        foreach (self::tiposDisponibles() as $conocido) {
            if (mb_strtolower($conocido) === $tipoLower) {
                return $conocido;
            }
        }

        // 2. Acrónimos y términos comunes en mayúsculas
        $acronimos = ['UPS', 'AP', 'HDMI', 'PC', 'USB', 'DVR', 'NVR', 'POS'];
        $palabras = explode(' ', $tipo);
        $resultado = [];

        foreach ($palabras as $p) {
            $pTrim = trim($p);
            if ($pTrim === '') {
                continue;
            }
            $pUpper = mb_strtoupper($pTrim);
            if (in_array($pUpper, $acronimos, true)) {
                $resultado[] = $pUpper;
            } elseif (in_array(mb_strtolower($pTrim), ['de', 'del', 'la', 'el', 'en', 'para', 'y', 'o', 'a'], true)) {
                $resultado[] = mb_strtolower($pTrim);
            } else {
                $resultado[] = mb_convert_case($pTrim, MB_CASE_TITLE, 'UTF-8');
            }
        }

        $tipoNormalizado = implode(' ', $resultado);

        return ucfirst($tipoNormalizado);
    }

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

    public function cambiosHardware(): HasMany
    {
        return $this->hasMany(HistorialCambioHardware::class, 'dispositivo_id')->latest('id');
    }

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(MantenimientoDispositivo::class, 'dispositivo_id')->latest('fecha_inicio')->latest('id');
    }

    public function mantenimientoActivo(): HasOne
    {
        return $this->hasOne(MantenimientoDispositivo::class, 'dispositivo_id')
            ->where('estado', MantenimientoDispositivo::ESTADO_EN_PROCESO)
            ->latest('id');
    }

    public function estaEnMantenimiento(): bool
    {
        if ($this->relationLoaded('mantenimientoActivo')) {
            return $this->mantenimientoActivo !== null;
        }

        return $this->mantenimientoActivo()->exists();
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
        if (! $tipo) {
            return 'general';
        }

        $normalizado = mb_strtolower(trim($tipo));

        return match ($normalizado) {
            'laptop', 'computadora de escritorio', 'servidor', 'tablet', 'computadora', 'pc' => 'computadora',
            'switch de red', 'switch', 'router', 'access point (ap)', 'access point', 'ap' => 'red',
            'monitor', 'pantalla interactiva', 'pantalla', 'proyector' => 'pantalla',
            'impresora', 'escáner', 'escaner' => 'impresion',
            'no-break / ups', 'no-break', 'ups' => 'ups',
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
