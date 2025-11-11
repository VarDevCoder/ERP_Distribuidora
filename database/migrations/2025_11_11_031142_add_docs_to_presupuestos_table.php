<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('presupuestos', function (Blueprint $table) {
            // VENTAS
            $table->string('factura_numero')->nullable();
            $table->date('factura_fecha')->nullable();
            $table->string('contrafactura_numero')->nullable();
            $table->date('contrafactura_fecha')->nullable();
            $table->boolean('venta_validada')->default(false);

            // COMPRAS
            $table->string('remision_numero')->nullable();
            $table->date('remision_fecha')->nullable();
            $table->boolean('compra_validada')->default(false);

            // Solo crear índices si no existen
            $table->index('factura_numero');
            $table->index('contrafactura_numero');
            $table->index('remision_numero');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presupuestos', function (Blueprint $table) {
            $table->dropIndex(['factura_numero']);
            $table->dropIndex(['contrafactura_numero']);
            $table->dropIndex(['remision_numero']);

            $table->dropColumn([
                'factura_numero', 'factura_fecha',
                'contrafactura_numero', 'contrafactura_fecha',
                'venta_validada',
                'remision_numero', 'remision_fecha',
                'compra_validada'
            ]);
        });
    }
};
