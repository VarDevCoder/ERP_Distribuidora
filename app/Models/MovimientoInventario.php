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
        'usuario_id', 'observaciones',
        'cantidad_presupuestada', 'diferencia', 'motivo_diferencia', 'hash_verificacion'
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'stock_anterior' => 'decimal:3',
        'stock_nuevo' => 'decimal:3',
        'cantidad_presupuestada' => 'decimal:3',
        'diferencia' => 'decimal:3',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function referencia()
    {
        return $this->morphTo('referencia', 'referencia_tipo', 'referencia_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Métodos para trazabilidad
    public function tieneDiferencia(): bool
    {
        return $this->diferencia !== null && abs($this->diferencia) > 0.001;
    }

    public function generarHash(): string
    {
        // Hash SHA256 de los datos críticos para prevenir alteraciones
        $datos = implode('|', [
            $this->id,
            $this->producto_id,
            $this->tipo,
            $this->cantidad,
            $this->stock_anterior,
            $this->stock_nuevo,
            $this->created_at?->timestamp ?? '',
        ]);

        return hash('sha256', $datos);
    }

    public function verificarIntegridad(): bool
    {
        if (!$this->hash_verificacion) {
            return true; // Sin hash, no se puede verificar
        }

        return $this->generarHash() === $this->hash_verificacion;
    }

    protected static function boot()
    {
        parent::boot();

        // Generar hash al crear el movimiento
        static::created(function ($movimiento) {
            if (!$movimiento->hash_verificacion) {
                $movimiento->update(['hash_verificacion' => $movimiento->generarHash()]);
            }
        });
    }
}
