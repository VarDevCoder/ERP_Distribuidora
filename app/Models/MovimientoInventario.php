<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_id', 'nota_remision_id', 'tipo', 'cantidad',
        'stock_anterior', 'stock_nuevo', 'referencia_tipo', 'referencia_id',
        'factura_numero', 'contrafactura_numero', 'remision_numero',
        'usuario_id', 'observaciones'
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'stock_anterior' => 'decimal:3',
        'stock_nuevo' => 'decimal:3',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function notaRemision()
    {
        return $this->belongsTo(NotaRemision::class);
    }
}
