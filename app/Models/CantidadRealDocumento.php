<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CantidadRealDocumento extends Model
{
    protected $table = 'cantidades_reales_documentos';

    protected $fillable = [
        'presupuesto_id',
        'producto_id',
        'tipo_documento',
        'cantidad_presupuestada',
        'cantidad_real',
        'diferencia',
        'motivo_diferencia',
        'usuario_id',
    ];

    protected $casts = [
        'cantidad_presupuestada' => 'decimal:3',
        'cantidad_real' => 'decimal:3',
        'diferencia' => 'decimal:3',
    ];

    // Relaciones
    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Métodos útiles
    public function tieneDiferencia(): bool
    {
        return abs($this->diferencia) > 0.001; // Tolerancia para decimales
    }

    public function esFaltante(): bool
    {
        return $this->diferencia < 0;
    }

    public function esSobrante(): bool
    {
        return $this->diferencia > 0;
    }

    // Boot para calcular diferencia automáticamente
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cantidad) {
            $cantidad->diferencia = $cantidad->cantidad_real - $cantidad->cantidad_presupuestada;
        });

        static::updating(function ($cantidad) {
            $cantidad->diferencia = $cantidad->cantidad_real - $cantidad->cantidad_presupuestada;
        });
    }
}
