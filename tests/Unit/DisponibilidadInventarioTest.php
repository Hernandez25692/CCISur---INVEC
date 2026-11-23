<?php

namespace Tests\Unit;

use App\Models\Dispositivo;
use PHPUnit\Framework\TestCase;

class DisponibilidadInventarioTest extends TestCase
{
    /**
     * Escenario 1: Un bien con asignación activa siempre debe tener disponibilidad 'Asignado'.
     */
    public function test_bien_con_asignacion_activa_retorna_disponibilidad_asignado(): void
    {
        $disponibilidad = Dispositivo::calcularDisponibilidad(
            estado: Dispositivo::ESTADO_USADO,
            esAreaComun: false,
            tieneAsignacionActiva: true
        );

        $this->assertSame(Dispositivo::DISPONIBILIDAD_ASIGNADO, $disponibilidad);
    }

    /**
     * Escenario 2: Un bien en estado Dañado o En reparación sin custodio retorna 'No Aplica para asignación'.
     */
    public function test_bien_en_estado_no_asignable_retorna_no_aplica(): void
    {
        $disponibilidadDanado = Dispositivo::calcularDisponibilidad(
            estado: Dispositivo::ESTADO_DEFECTUOSO,
            esAreaComun: false,
            tieneAsignacionActiva: false
        );

        $disponibilidadReparacion = Dispositivo::calcularDisponibilidad(
            estado: Dispositivo::ESTADO_EN_REPARACION,
            esAreaComun: false,
            tieneAsignacionActiva: false
        );

        $this->assertSame(Dispositivo::DISPONIBILIDAD_NO_APLICA, $disponibilidadDanado);
        $this->assertSame(Dispositivo::DISPONIBILIDAD_NO_APLICA, $disponibilidadReparacion);
    }

    /**
     * Escenario 3: Un bien en perfectas condiciones pero destinado a Área Común retorna 'No Aplica para asignación'.
     */
    public function test_bien_destinado_a_area_comun_retorna_no_aplica_para_asignacion_individual(): void
    {
        $disponibilidad = Dispositivo::calcularDisponibilidad(
            estado: Dispositivo::ESTADO_NUEVO,
            esAreaComun: true,
            tieneAsignacionActiva: false
        );

        $this->assertSame(Dispositivo::DISPONIBILIDAD_NO_APLICA, $disponibilidad);
    }

    /**
     * Escenario 4: Un bien operativo en bodega sin asignación y sin ser área común retorna 'Sin Asignar' (Disponible).
     */
    public function test_bien_operativo_en_bodega_retorna_sin_asignar(): void
    {
        $disponibilidad = Dispositivo::calcularDisponibilidad(
            estado: Dispositivo::ESTADO_USADO,
            esAreaComun: false,
            tieneAsignacionActiva: false
        );

        $this->assertSame(Dispositivo::DISPONIBILIDAD_SIN_ASIGNAR, $disponibilidad);
    }
}
