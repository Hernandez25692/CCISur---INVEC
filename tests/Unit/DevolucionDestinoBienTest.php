<?php

namespace Tests\Unit;

use App\Models\Dispositivo;
use PHPUnit\Framework\TestCase;

class DevolucionDestinoBienTest extends TestCase
{
    /**
     * Escenario 1: Devolución en estado operativo ('Usado / Segunda mano').
     * El bien debe quedar con disponibilidad 'Sin Asignar' (disponible en bodega)
     * y no debe considerarse descartable/eliminable.
     */
    public function test_devolucion_operativa_retorna_a_bodega_disponible(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Laptop Dell Latitude',
            'estado' => Dispositivo::ESTADO_USADO,
        ]);

        $dispositivo->estado = Dispositivo::ESTADO_USADO;
        $dispositivo->disponibilidad = Dispositivo::calcularDisponibilidad(
            estado: $dispositivo->estado,
            esAreaComun: false,
            tieneAsignacionActiva: false
        );

        $this->assertSame(Dispositivo::DISPONIBILIDAD_SIN_ASIGNAR, $dispositivo->disponibilidad);
        $this->assertTrue(Dispositivo::esEstadoAsignable($dispositivo->estado));
        $this->assertFalse($dispositivo->esEstadoDescartable());
    }

    /**
     * Escenario 2: Devolución por daño irreparable ('Dañado / Defectuoso').
     * El bien pasa a disponibilidad 'No Aplica para asignación', se marca como
     * descartable para baja y no puede ser asignado nuevamente.
     */
    public function test_devolucion_por_dano_aplica_para_baja_definitiva(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Monitor HP 24"',
            'estado' => Dispositivo::ESTADO_USADO,
        ]);

        $dispositivo->estado = Dispositivo::ESTADO_DEFECTUOSO;
        $dispositivo->disponibilidad = Dispositivo::calcularDisponibilidad(
            estado: $dispositivo->estado,
            esAreaComun: false,
            tieneAsignacionActiva: false
        );

        $this->assertSame(Dispositivo::DISPONIBILIDAD_NO_APLICA, $dispositivo->disponibilidad);
        $this->assertFalse(Dispositivo::esEstadoAsignable($dispositivo->estado));
        $this->assertTrue($dispositivo->esEstadoDescartable());
    }

    /**
     * Escenario 3: Devolución con requerimiento técnico ('En reparación / En revisión').
     * El bien queda en estado no asignable y con disponibilidad 'No Aplica',
     * bloqueando cualquier intento de asignación hasta recibir dictamen técnico.
     */
    public function test_devolucion_a_taller_bloquea_disponibilidad_inmediata(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Impresora Epson L3150',
            'estado' => Dispositivo::ESTADO_USADO,
        ]);

        $dispositivo->estado = Dispositivo::ESTADO_EN_REPARACION;
        $dispositivo->disponibilidad = Dispositivo::calcularDisponibilidad(
            estado: $dispositivo->estado,
            esAreaComun: false,
            tieneAsignacionActiva: false
        );

        $this->assertSame(Dispositivo::DISPONIBILIDAD_NO_APLICA, $dispositivo->disponibilidad);
        $this->assertFalse(Dispositivo::esEstadoAsignable($dispositivo->estado));
        $this->assertFalse($dispositivo->esEstadoDescartable());
    }
}
