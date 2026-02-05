<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProveedorProducto extends Model
{
    use HasFactory;

    protected $table = 'proveedor_productos';

    protected $fillable = [
        'proveedor_id',
        'producto_id',
        'codigo_proveedor',
        'nombre_proveedor',
        'precio',
        'disponible',
        'tiempo_entrega_dias',
        'notas',
    ];

    protected $casts = [
        'precio' => 'integer',
        'disponible' => 'boolean',
        'tiempo_entrega_dias' => 'integer',
    ];

    // Relaciones
    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Scopes
    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }

    // Helpers
    public function getPrecioFormateadoAttribute(): string
    {
        return number_format($this->precio, 0, ',', '.') . ' Gs.';
    }

    public function getTiempoEntregaFormateadoAttribute(): string
    {
        if (!$this->tiempo_entrega_dias) {
            return '-';
        }

        return $this->tiempo_entrega_dias . ' ' . ($this->tiempo_entrega_dias === 1 ? 'día' : 'días');
    }
}
