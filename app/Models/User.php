<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Roles del sistema
    const ROL_ADMIN = 'admin';
    const ROL_COLABORADOR = 'colaborador';
    const ROL_PROVEEDOR = 'proveedor';

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
        ];
    }

    // Relaciones
    public function proveedor(): HasOne
    {
        return $this->hasOne(Proveedor::class);
    }

    // Helpers de rol
    public function esAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function esColaborador(): bool
    {
        return $this->rol === self::ROL_COLABORADOR || $this->rol === self::ROL_ADMIN;
    }

    public function esProveedor(): bool
    {
        return $this->rol === self::ROL_PROVEEDOR;
    }

    /**
     * Obtener el nombre del rol para mostrar
     */
    public function getRolNombreAttribute(): string
    {
        return match($this->rol) {
            self::ROL_ADMIN => 'Administrador',
            self::ROL_COLABORADOR => 'Colaborador',
            self::ROL_PROVEEDOR => 'Proveedor',
            default => 'Usuario',
        };
    }
}
