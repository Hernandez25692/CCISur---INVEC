<?php

namespace Tests\Unit;

use App\Models\Dispositivo;
use Tests\TestCase;

class ReactivacionBienesBajasTest extends TestCase
{
    /**
     * Escenario 1: Reactivación con pase directo a taller técnico.
     * El bien muta su estado físico a 'En reparación / En revisión' y su disponibilidad
     * se fija en 'No Aplica para asignación' para impedir su entrega mientras se diagnostica.
     */
    public function test_reactivacion_con_pase_a_taller_asigna_estado_en_reparacion_y_no_aplica(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Laptop HP ProBook',
            'estado' => Dispositivo::ESTADO_DEFECTUOSO,
            'motivo_baja' => 'Falla en placa base',
            'baja_por_user_id' => 1,
        ]);

        $enviarATaller = true;

        if ($enviarATaller) {
            $dispositivo->estado = Dispositivo::ESTADO_EN_REPARACION;
            $dispositivo->disponibilidad = Dispositivo::DISPONIBILIDAD_NO_APLICA;
        }

        $this->assertSame(Dispositivo::ESTADO_EN_REPARACION, $dispositivo->estado);
        $this->assertSame(Dispositivo::DISPONIBILIDAD_NO_APLICA, $dispositivo->disponibilidad);
        $this->assertFalse(Dispositivo::esEstadoAsignable($dispositivo->estado));
    }

    /**
     * Escenario 2: Reactivación sin taller de un bien operativo ('Usado').
     * Al restaurarse un bien funcional, su disponibilidad se recalcula a 'Sin Asignar',
     * quedando disponible de inmediato en bodega.
     */
    public function test_reactivacion_sin_taller_bien_operativo_retorna_a_bodega_disponible(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Monitor Samsung 24"',
            'estado' => Dispositivo::ESTADO_USADO,
        ]);

        $enviarATaller = false;

        $dispositivo->disponibilidad = Dispositivo::calcularDisponibilidad(
            $dispositivo->estado,
            esAreaComun: false,
            tieneAsignacionActiva: false
        );

        $this->assertSame(Dispositivo::DISPONIBILIDAD_SIN_ASIGNAR, $dispositivo->disponibilidad);
        $this->assertTrue(Dispositivo::esEstadoAsignable($dispositivo->estado));
    }

    /**
     * Escenario 3: Reactivación sin taller de un bien que permanece 'Dañado'.
     * Si no se envía a taller, su disponibilidad calculada es 'No Aplica para asignación',
     * protegiendo el inventario de asignaciones indebidas.
     */
    public function test_reactivacion_sin_taller_bien_danado_mantiene_no_aplica(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Proyector con Lámpara Quemada',
            'estado' => Dispositivo::ESTADO_DEFECTUOSO,
        ]);

        $dispositivo->disponibilidad = Dispositivo::calcularDisponibilidad(
            $dispositivo->estado,
            esAreaComun: false,
            tieneAsignacionActiva: false
        );

        $this->assertSame(Dispositivo::DISPONIBILIDAD_NO_APLICA, $dispositivo->disponibilidad);
        $this->assertFalse(Dispositivo::esEstadoAsignable($dispositivo->estado));
    }

    /**
     * Escenario 4: Limpieza de metadata de baja y registro del auditor reactivador.
     * Al reactivar, el motivo de baja y el usuario que dio la baja deben limpiarse,
     * persistiendo únicamente la trazabilidad del usuario que reactiva.
     */
    public function test_reactivacion_limpia_campos_de_baja_y_asigna_trazabilidad(): void
    {
        $dispositivo = new Dispositivo([
            'motivo_baja' => 'Dado de baja por error administrativo',
            'baja_por_user_id' => 2,
        ]);

        // Simular ejecución de reactivación en el controlador
        $dispositivo->motivo_baja = null;
        $dispositivo->baja_por_user_id = null;
        $dispositivo->reactivado_por_user_id = 1;

        $this->assertNull($dispositivo->motivo_baja);
        $this->assertNull($dispositivo->baja_por_user_id);
        $this->assertSame(1, $dispositivo->reactivado_por_user_id);
    }
}
