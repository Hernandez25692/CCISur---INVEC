<?php

namespace Tests\Unit;

use App\Models\Asignacion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Tests\TestCase;

class AnulacionAsignacionPlazoTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * Helper para instanciar un usuario simulado en memoria.
     */
    protected function makeUser(string $rol, bool $activo = true): User
    {
        $user = new User();
        $user->name = 'Usuario Test';
        $user->email = 'test@ccisur.org';
        $user->rol = $rol;
        $user->activo = $activo;

        return $user;
    }

    /**
     * Escenario 1: Un Administrador tiene un plazo de 1 día hábil (24 horas hábiles).
     * Si el acta se creó un viernes a las 10:00 AM, el lunes a las 09:00 AM aún puede anularse.
     */
    public function test_administrador_puede_anular_dentro_de_un_dia_habil(): void
    {
        $admin = $this->makeUser(User::ROL_ADMINISTRADOR, true);

        $fechaCreacion = Carbon::create(2026, 11, 20, 10, 0, 0);

        $asignacion = new Asignacion();
        $asignacion->created_at = $fechaCreacion;

        // Momento de evaluación: Lunes 2026-11-23 09:00:00 (dentro del día hábil siguiente)
        Carbon::setTestNow(Carbon::create(2026, 11, 23, 9, 0, 0));

        $limite = $asignacion->fechaLimiteAnulacion($admin);

        $this->assertNotNull($limite);
        $this->assertTrue(now()->lessThanOrEqualTo($limite));
    }

    /**
     * Escenario 2: Un Administrador NO puede anular si transcurrió más de 1 día hábil.
     * Creada viernes 10:00 AM, evaluada martes 10:00 AM (superó el día hábil permitido).
     */
    public function test_administrador_no_puede_anular_despues_de_un_dia_habil(): void
    {
        $admin = $this->makeUser(User::ROL_ADMINISTRADOR, true);

        $fechaCreacion = Carbon::create(2026, 11, 20, 10, 0, 0);

        $asignacion = new Asignacion();
        $asignacion->created_at = $fechaCreacion;

        // Momento de evaluación: Martes 2026-11-24 10:00:00
        Carbon::setTestNow(Carbon::create(2026, 11, 24, 10, 0, 0));

        $limite = $asignacion->fechaLimiteAnulacion($admin);

        $this->assertNotNull($limite);
        $this->assertFalse(now()->lessThanOrEqualTo($limite));
    }

    /**
     * Escenario 3: Un Superadministrador cuenta con 3 días hábiles completos de gracia.
     * Creada viernes 10:00 AM, evaluada miércoles a las 09:00 AM (dentro de los 3 días hábiles).
     */
    public function test_superadministrador_cuenta_con_tres_dias_habiles_de_gracia(): void
    {
        $superadmin = $this->makeUser(User::ROL_SUPERADMIN, true);

        $fechaCreacion = Carbon::create(2026, 11, 20, 10, 0, 0);

        $asignacion = new Asignacion();
        $asignacion->created_at = $fechaCreacion;

        // Momento de evaluación: Miércoles 2026-11-25 09:00:00
        Carbon::setTestNow(Carbon::create(2026, 11, 25, 9, 0, 0));

        $limite = $asignacion->fechaLimiteAnulacion($superadmin);

        $this->assertNotNull($limite);
        $this->assertTrue(now()->lessThanOrEqualTo($limite));
    }

    /**
     * Escenario 4: Si el acta ya cuenta con una devolución registrada, NO puede ser anulada.
     * Aunque se encuentre dentro del plazo temporal y sea solicitada por un Superadministrador.
     */
    public function test_acta_con_devolucion_registrada_no_puede_ser_anulada(): void
    {
        $superadmin = $this->makeUser(User::ROL_SUPERADMIN, true);

        $queryMock = $this->createMock(QueryBuilder::class);
        $queryMock->method('exists')->willReturn(true);
        $eloquentBuilder = new EloquentBuilder($queryMock);

        $asignacion = $this->getMockBuilder(Asignacion::class)
            ->onlyMethods(['devolucion'])
            ->getMock();

        $hasOne = new HasOne($eloquentBuilder, $asignacion, 'devoluciones.asignacion_id', 'id');
        $asignacion->method('devolucion')->willReturn($hasOne);
        $asignacion->created_at = Carbon::now();

        $this->assertFalse($asignacion->puedeSerAnuladaPor($superadmin));
    }

    /**
     * Escenario 5: Un usuario con rol Lectura no tiene privilegios de anulación bajo ninguna circunstancia.
     */
    public function test_usuario_lectura_no_tiene_privilegios_de_anulacion(): void
    {
        $lector = $this->makeUser(User::ROL_LECTURA, true);

        $asignacion = new Asignacion();
        $asignacion->created_at = Carbon::now();

        $this->assertFalse($asignacion->puedeSerAnuladaPor($lector));
    }

    /**
     * Escenario 6: Un usuario inactivo no puede anular actas aunque tenga rol administrativo.
     */
    public function test_usuario_inactivo_no_puede_anular_actas(): void
    {
        $adminInactivo = $this->makeUser(User::ROL_ADMINISTRADOR, false);

        $asignacion = new Asignacion();
        $asignacion->created_at = Carbon::now();

        $this->assertFalse($asignacion->puedeSerAnuladaPor($adminInactivo));
    }
}
