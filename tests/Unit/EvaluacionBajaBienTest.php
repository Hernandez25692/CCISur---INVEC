<?php

namespace Tests\Unit;

use App\Models\Dispositivo;
use App\Models\MantenimientoDispositivo;
use Tests\TestCase;

class EvaluacionBajaBienTest extends TestCase
{
    /**
     * Escenario 1: Un bien que tiene asignación activa NO puede darse de baja,
     * debiendo registrarse primero su devolución.
     */
    public function test_bien_con_asignacion_activa_no_puede_darse_de_baja(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Laptop Dell Inspiron',
            'estado' => Dispositivo::ESTADO_DEFECTUOSO,
        ]);

        $dispositivo->setAttribute('asignaciones_activas_count', 1);
        $dispositivo->setRelation('mantenimientoActivo', null);

        $this->assertFalse($dispositivo->puede_darse_de_baja);
        $this->assertNotNull($dispositivo->motivo_no_baja);
        $this->assertStringContainsString('asignación activa', $dispositivo->motivo_no_baja);
    }

    /**
     * Escenario 2: Un bien que se encuentra en taller/mantenimiento NO puede darse de baja
     * hasta que se finalice o cancele su mantenimiento.
     */
    public function test_bien_en_taller_no_puede_darse_de_baja(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Impresora HP LaserJet',
            'estado' => Dispositivo::ESTADO_DEFECTUOSO,
        ]);

        $dispositivo->setAttribute('asignaciones_activas_count', 0);
        $dispositivo->setRelation('mantenimientoActivo', new MantenimientoDispositivo());

        $this->assertFalse($dispositivo->puede_darse_de_baja);
        $this->assertNotNull($dispositivo->motivo_no_baja);
        $this->assertStringContainsString('taller / mantenimiento', $dispositivo->motivo_no_baja);
    }

    /**
     * Escenario 3: Un bien en estado operativo ('Usado' o 'Nuevo') NO amerita baja,
     * ya que la política institucional solo permite descartar bienes 'Dañado / Defectuoso'.
     */
    public function test_bien_en_buen_estado_no_amerita_baja(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Monitor Dell 27"',
            'estado' => Dispositivo::ESTADO_USADO,
        ]);

        $dispositivo->setAttribute('asignaciones_activas_count', 0);
        $dispositivo->setRelation('mantenimientoActivo', null);

        $this->assertFalse($dispositivo->puede_darse_de_baja);
        $this->assertNotNull($dispositivo->motivo_no_baja);
        $this->assertStringContainsString('Solo se pueden dar de baja bienes en estado «Dañado / Defectuoso»', $dispositivo->motivo_no_baja);
    }

    /**
     * Escenario 4: Un bien sin custodio, fuera de taller y en estado 'Dañado / Defectuoso'
     * cumple todas las condiciones y PUEDE darse de baja exitosamente.
     */
    public function test_bien_sin_custodio_sin_taller_y_danado_puede_darse_de_baja(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Teclado USB Averiado',
            'estado' => Dispositivo::ESTADO_DEFECTUOSO,
        ]);

        $dispositivo->setAttribute('asignaciones_activas_count', 0);
        $dispositivo->setRelation('mantenimientoActivo', null);

        $this->assertTrue($dispositivo->puede_darse_de_baja);
        $this->assertNull($dispositivo->motivo_no_baja);
    }
}
