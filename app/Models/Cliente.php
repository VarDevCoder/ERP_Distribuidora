<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'ruc',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'activo',
        'notas',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function pedidos(): HasMany
    {
        return $this->hasMany(PedidoCliente::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    // Helper methods
    public function getTelefonoFormateadoAttribute(): string
    {
        return $this->telefono ?? '-';
    }

    public function getEmailFormateadoAttribute(): string
    {
        return $this->email ?? '-';
    }
}
