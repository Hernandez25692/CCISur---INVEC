<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROL_SUPERADMIN = 'superadmin';

    public const ROL_ADMINISTRADOR = 'administrador';

    public const ROL_LECTURA = 'lectura';

    public const ROLES = [
        self::ROL_SUPERADMIN => 'Superadministrador',
        self::ROL_ADMINISTRADOR => 'Administrador',
        self::ROL_LECTURA => 'Solo Lectura',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'activo',
        'debe_cambiar_password',
        'creado_por',
        'actualizado_por',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'debe_cambiar_password' => 'boolean',
        ];
    }

    /**
     * Comprobar si el usuario es Superadministrador.
     */
    public function esSuperAdmin(): bool
    {
        return $this->rol === self::ROL_SUPERADMIN;
    }

    /**
     * Comprobar si el usuario tiene privilegios administrativos (Superadmin o Administrador).
     */
    public function esAdministrador(): bool
    {
        return in_array($this->rol, [self::ROL_SUPERADMIN, self::ROL_ADMINISTRADOR], true);
    }

    /**
     * Comprobar si el usuario tiene rol exclusivo de Administrador secundario.
     */
    public function esAdministradorEstricto(): bool
    {
        return $this->rol === self::ROL_ADMINISTRADOR;
    }

    /**
     * Comprobar si el usuario tiene rol de Solo Lectura.
     */
    public function esLectura(): bool
    {
        return $this->rol === self::ROL_LECTURA;
    }

    /**
     * Comprobar si la cuenta de usuario se encuentra activa.
     */
    public function estaActivo(): bool
    {
        return (bool) $this->activo;
    }

    /**
     * Comprobar si el usuario tiene pendiente un cambio de contraseña obligatorio.
     */
    public function debeCambiarPassword(): bool
    {
        return (bool) $this->debe_cambiar_password;
    }

    /**
     * Comprobar si este usuario es el único Superadministrador activo del sistema.
     */
    public function esUltimoSuperadminActivo(): bool
    {
        return $this->esSuperAdmin()
            && $this->estaActivo()
            && self::where('rol', self::ROL_SUPERADMIN)->where('activo', true)->count() <= 1;
    }

    /**
     * Determina si este usuario puede ser gestionado (editado, desactivado o eliminado)
     * por el usuario operador dado ($actor).
     */
    public function puedeSerGestionadoPor(self $actor): bool
    {
        // Nadie se gestiona a sí mismo desde el panel de usuarios (para eso está Mi Perfil)
        if ($this->id === $actor->id) {
            return false;
        }

        // Únicamente los Superadministradores tienen acceso a gestionar usuarios
        return $actor->esSuperAdmin();
    }

    /**
     * Nombre legible del rol.
     */
    public function getRolNombreAttribute(): string
    {
        return self::ROLES[$this->rol] ?? ucfirst($this->rol);
    }

    /**
     * Superadministrador o usuario que creó esta cuenta.
     */
    public function creador(): BelongsTo
    {
        return $this->belongsTo(self::class, 'creado_por');
    }

    /**
     * Último usuario o superadministrador que modificó esta cuenta.
     */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(self::class, 'actualizado_por');
    }
}
