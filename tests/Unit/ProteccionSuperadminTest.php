<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class ProteccionSuperadminTest extends TestCase
{
    /**
     * Helper para construir instancias de usuario en memoria.
     */
    protected function makeUser(int $id, string $rol, bool $activo = true): User
    {
        $user = new User();
        $user->id = $id;
        $user->name = 'Usuario '.$id;
        $user->email = "usuario{$id}@ccisur.org";
        $user->rol = $rol;
        $user->activo = $activo;

        return $user;
    }

    /**
     * Escenario 1: Un Superadministrador NO puede auto-gestionarse, eliminarse o
     * auto-desactivarse desde el catálogo administrativo de cuentas (para ello existe Mi Perfil).
     */
    public function test_superadministrador_no_puede_gestionarse_a_si_mismo(): void
    {
        $superadmin = $this->makeUser(1, User::ROL_SUPERADMIN, true);

        // Intento de auto-gestión ($actor es el mismo $usuario)
        $this->assertFalse($superadmin->puedeSerGestionadoPor($superadmin));
    }

    /**
     * Escenario 2: Un Superadministrador SÍ tiene facultades para gestionar
     * cuentas de otros colaboradores (usuarios distintos a él).
     */
    public function test_superadministrador_puede_gestionar_a_otros_usuarios(): void
    {
        $superadmin = $this->makeUser(1, User::ROL_SUPERADMIN, true);
        $otroUsuario = $this->makeUser(2, User::ROL_ADMINISTRADOR, true);

        $this->assertTrue($otroUsuario->puedeSerGestionadoPor($superadmin));
    }

    /**
     * Escenario 3: Un usuario con rol Administrador estándar o Lectura NO puede gestionar
     * usuarios del sistema, quedando reservado exclusivamente para Superadministradores.
     */
    public function test_usuario_no_superadmin_no_puede_gestionar_usuarios(): void
    {
        $adminEstándar = $this->makeUser(2, User::ROL_ADMINISTRADOR, true);
        $usuarioObjetivo = $this->makeUser(3, User::ROL_LECTURA, true);

        $this->assertFalse($usuarioObjetivo->puedeSerGestionadoPor($adminEstándar));
    }

    /**
     * Escenario 4: Un usuario no puede considerarse 'último superadmin activo'
     * si su rol es distinto a Superadministrador o si ya se encuentra inactivo.
     */
    public function test_usuario_inactivo_o_no_superadmin_no_es_ultimo_superadmin(): void
    {
        $admin = $this->makeUser(3, User::ROL_ADMINISTRADOR, true);
        $superadminInactivo = $this->makeUser(4, User::ROL_SUPERADMIN, false);

        // Al evaluar las precondiciones ($this->esSuperAdmin() && $this->estaActivo())
        $this->assertFalse($admin->esSuperAdmin());
        $this->assertFalse($superadminInactivo->estaActivo());
    }
}
