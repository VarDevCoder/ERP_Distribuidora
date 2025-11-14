<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    protected $table = 'presupuestos';

    protected $fillable = [
        'numero', 'tipo', 'contacto_nombre', 'contacto_email', 'contacto_telefono',
        'contacto_empresa', 'fecha', 'fecha_vencimiento', 'subtotal',
        'descuento', 'impuesto', 'total', 'estado', 'nota_remision_id', 'notas',
        'factura_numero', 'factura_fecha', 'contrafactura_numero', 'contrafactura_fecha',
        'venta_validada', 'remision_numero', 'remision_fecha', 'compra_validada'
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'integer',  // Guaraníes (sin decimales)
        'descuento' => 'integer', // Guaraníes (sin decimales)
        'impuesto' => 'integer',  // Guaraníes (sin decimales)
        'total' => 'integer',     // Guaraníes (sin decimales)
        'venta_validada' => 'boolean',
        'compra_validada' => 'boolean',
        'factura_fecha' => 'date',
        'contrafactura_fecha' => 'date',
        'remision_fecha' => 'date',
    ];

    // Relaciones
    public function items()
    {
        return $this->hasMany(PresupuestoItem::class)->orderBy('orden');
    }

    public function notaRemision()
    {
        return $this->belongsTo(NotaRemision::class);
    }

    public function notaRemisiones()
    {
        return $this->hasMany(NotaRemision::class);
    }

    public function productos()
    {
        return $this->hasMany(PresupuestoItem::class);
    }

    public function cantidadesReales()
    {
        return $this->hasMany(CantidadRealDocumento::class);
    }

    // Métodos
    public function calcularTotales()
    {
        $subtotal = $this->items->sum('subtotal');
        $descuento = $this->descuento ?? 0;
        $total = $subtotal - $descuento;

        // Por ahora sin IVA - se agregará después si es necesario
        $this->update([
            'subtotal' => round($subtotal),
            'impuesto' => 0,
            'total' => round($total),
        ]);
    }

    public function puedeConvertirANotaRemision()
    {
        return $this->estado === 'APROBADO' && !$this->nota_remision_id;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($presupuesto) {
            if (!$presupuesto->numero) {
                $year = date('Y');
                $prefijo = $presupuesto->tipo === 'COMPRA' ? 'PC' : 'PV';
                $ultimo = Presupuesto::where('tipo', $presupuesto->tipo)
                    ->whereYear('created_at', $year)
                    ->count();
                $presupuesto->numero = $prefijo . '-' . $year . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
