<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'user_id',
        'razon_social',
        'ruc',
        'telefono',
        'direccion',
        'ciudad',
        'rubros',
        'notas',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function solicitudesPresupuesto(): HasMany
    {
        return $this->hasMany(SolicitudPresupuesto::class);
    }

    public function ordenesCompra(): HasMany
    {
        return $this->hasMany(OrdenCompra::class);
    }

    public function proveedorProductos(): HasMany
    {
        return $this->hasMany(ProveedorProducto::class);
    }

    /**
     * Solicitudes pendientes de respuesta
     */
    public function solicitudesPendientes(): HasMany
    {
        return $this->hasMany(SolicitudPresupuesto::class)
            ->whereIn('estado', ['ENVIADA', 'VISTA']);
    }
}
