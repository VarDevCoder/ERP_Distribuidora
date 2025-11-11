<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NotaRemision extends Model
{
    protected $table = 'notas_remision';

    protected $fillable = [
        'numero', 'presupuesto_id', 'tipo', 'contacto_nombre',
        'contacto_empresa', 'fecha', 'estado', 'observaciones',
        'factura_numero', 'contrafactura_numero', 'remision_numero'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // Relaciones
    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function items()
    {
        return $this->hasMany(NotaRemisionItem::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    // Métodos
    public function aplicarAInventario()
    {
        if ($this->estado === 'APLICADA') {
            throw new \Exception('Esta nota de remisión ya fue aplicada al inventario');
        }

        DB::transaction(function () {
            foreach ($this->items as $item) {
                $producto = $item->producto;
                $stockAnterior = $producto->stock_actual;

                if ($this->tipo === 'ENTRADA') {
                    $producto->stock_actual += $item->cantidad;
                } else { // SALIDA
                    if ($producto->stock_actual < $item->cantidad) {
                        throw new \Exception("Stock insuficiente para el producto: {$producto->nombre}");
                    }
                    $producto->stock_actual -= $item->cantidad;
                }

                $producto->save();

                // Registrar movimiento
                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'nota_remision_id' => $this->id,
                    'tipo' => $this->tipo,
                    'cantidad' => $item->cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $producto->stock_actual,
                    'referencia' => "Nota Remisión {$this->numero}",
                ]);
            }

            $this->update(['estado' => 'APLICADA']);

            // Actualizar presupuesto como CONVERTIDO
            $this->presupuesto->update(['estado' => 'CONVERTIDO']);
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($nota) {
            if (!$nota->numero) {
                $year = date('Y');
                $prefijo = $nota->tipo === 'ENTRADA' ? 'NE' : 'NS';
                $ultimo = NotaRemision::where('tipo', $nota->tipo)
                    ->whereYear('created_at', $year)
                    ->count();
                $nota->numero = $prefijo . '-' . $year . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
