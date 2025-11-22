<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudPresupuestoItem extends Model
{
    use HasFactory;

    protected $table = 'solicitud_presupuesto_items';

    protected $fillable = [
        'solicitud_presupuesto_id',
        'producto_id',
        'cantidad_solicitada',
        'tiene_stock',
        'cantidad_disponible',
        'precio_unitario_cotizado',
        'subtotal_cotizado',
    ];

    protected $casts = [
        'cantidad_solicitada' => 'decimal:3',
        'cantidad_disponible' => 'decimal:3',
        'tiene_stock' => 'boolean',
        'precio_unitario_cotizado' => 'integer',
        'subtotal_cotizado' => 'integer',
    ];

    // Relaciones
    public function solicitudPresupuesto(): BelongsTo
    {
        return $this->belongsTo(SolicitudPresupuesto::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Calcular subtotal al actualizar
     */
    public function calcularSubtotal(): void
    {
        if ($this->cantidad_disponible && $this->precio_unitario_cotizado) {
            $this->subtotal_cotizado = (int) round($this->cantidad_disponible * $this->precio_unitario_cotizado);
            $this->save();
        }
    }
}
