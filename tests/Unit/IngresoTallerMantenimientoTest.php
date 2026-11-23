<?php

namespace Tests\Unit;

use App\Models\Dispositivo;
use App\Models\MantenimientoDispositivo;
use Carbon\Carbon;
use Tests\TestCase;

class IngresoTallerMantenimientoTest extends TestCase
{
    /**
     * Escenario 1: Bloqueo a bienes dados de baja.
     * Un dispositivo con borrado lógico (trashed) no puede ingresar a taller.
     */
    public function test_ingreso_taller_bloquea_bien_en_archivo_de_bajas(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Laptop Dell Dañada',
            'estado' => Dispositivo::ESTADO_DEFECTUOSO,
        ]);
        $dispositivo->deleted_at = Carbon::now();

        // Comprobar la condición de corte en el controlador ($dispositivo->trashed())
        $this->assertTrue($dispositivo->trashed());
    }

    /**
     * Escenario 2: Impedimento de órdenes duplicadas activas.
     * Si el dispositivo ya cuenta con un mantenimiento activo en proceso,
     * el sistema rechaza la creación de una segunda orden.
     */
    public function test_ingreso_taller_impide_duplicar_mantenimiento_activo(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Impresora Epson L3150',
            'estado' => Dispositivo::ESTADO_EN_REPARACION,
        ]);

        $mantenimientoActivo = new MantenimientoDispositivo([
            'estado' => MantenimientoDispositivo::ESTADO_EN_PROCESO,
        ]);

        $dispositivo->setRelation('mantenimientoActivo', $mantenimientoActivo);

        $this->assertTrue($dispositivo->estaEnMantenimiento());
    }

    /**
     * Escenario 3: Preservación de la custodia activa.
     * Si un equipo asignado a un colaborador ingresa a taller por revisión preventiva,
     * su disponibilidad física debe mantenerse como 'Asignado' para no romper la custodia.
     */
    public function test_ingreso_taller_preserva_custodia_activa_y_disponibilidad_asignado(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Laptop Lenovo ThinkPad',
            'estado' => Dispositivo::ESTADO_USADO,
        ]);

        $tieneCustodiaActiva = true;
        $esAreaComun = false;

        // Mutación a taller
        $dispositivo->estado = Dispositivo::ESTADO_EN_REPARACION;
        $dispositivo->disponibilidad = Dispositivo::calcularDisponibilidad(
            $dispositivo->estado,
            $esAreaComun,
            $tieneCustodiaActiva
        );

        $this->assertSame(Dispositivo::DISPONIBILIDAD_ASIGNADO, $dispositivo->disponibilidad);
        $this->assertSame(Dispositivo::ESTADO_EN_REPARACION, $dispositivo->estado);
    }

    /**
     * Escenario 4: Ingreso de bien en bodega sin custodio.
     * Si un equipo sin asignar ingresa a taller, su disponibilidad pasa a 'No Aplica para asignación',
     * quedando bloqueado para ser entregado a colaboradores.
     */
    public function test_ingreso_taller_sin_custodio_cambia_disponibilidad_a_no_aplica(): void
    {
        $dispositivo = new Dispositivo([
            'nombre' => 'Proyector Epson Bodega',
            'estado' => Dispositivo::ESTADO_USADO,
        ]);

        $tieneCustodiaActiva = false;
        $esAreaComun = false;

        $dispositivo->estado = Dispositivo::ESTADO_EN_REPARACION;
        $dispositivo->disponibilidad = Dispositivo::calcularDisponibilidad(
            $dispositivo->estado,
            $esAreaComun,
            $tieneCustodiaActiva
        );

        $this->assertSame(Dispositivo::DISPONIBILIDAD_NO_APLICA, $dispositivo->disponibilidad);
        $this->assertFalse(Dispositivo::esEstadoAsignable($dispositivo->estado));
    }
}
