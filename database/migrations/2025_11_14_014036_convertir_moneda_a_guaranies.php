<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convierte columnas de decimal a integer para usar Guaraníes (sin decimales)
     */
    public function up(): void
    {
        // PRODUCTOS: precio_compra y precio_venta
        Schema::table('productos', function (Blueprint $table) {
            // Convertir valores existentes multiplicando por 100 y redondeando
            DB::statement('UPDATE productos SET precio_compra = ROUND(precio_compra)');
            DB::statement('UPDATE productos SET precio_venta = ROUND(precio_venta)');

            $table->bigInteger('precio_compra')->default(0)->change();
            $table->bigInteger('precio_venta')->default(0)->change();
        });

        // PRESUPUESTOS: subtotal, descuento, impuesto, total
        Schema::table('presupuestos', function (Blueprint $table) {
            DB::statement('UPDATE presupuestos SET subtotal = ROUND(subtotal)');
            DB::statement('UPDATE presupuestos SET descuento = ROUND(descuento)');
            DB::statement('UPDATE presupuestos SET impuesto = ROUND(impuesto)');
            DB::statement('UPDATE presupuestos SET total = ROUND(total)');

            $table->bigInteger('subtotal')->default(0)->change();
            $table->bigInteger('descuento')->default(0)->change();
            $table->bigInteger('impuesto')->default(0)->change();
            $table->bigInteger('total')->default(0)->change();
        });

        // PRESUPUESTO_ITEMS: precio_unitario y subtotal (cantidad puede quedar decimal para peso/volumen)
        Schema::table('presupuesto_items', function (Blueprint $table) {
            DB::statement('UPDATE presupuesto_items SET precio_unitario = ROUND(precio_unitario)');
            DB::statement('UPDATE presupuesto_items SET subtotal = ROUND(subtotal)');

            $table->bigInteger('precio_unitario')->change();
            $table->bigInteger('subtotal')->change();
            // cantidad se queda como decimal para productos por peso/volumen
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // PRODUCTOS
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_compra', 10, 2)->default(0)->change();
            $table->decimal('precio_venta', 10, 2)->default(0)->change();
        });

        // PRESUPUESTOS
        Schema::table('presupuestos', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->change();
            $table->decimal('descuento', 10, 2)->default(0)->change();
            $table->decimal('impuesto', 10, 2)->default(0)->change();
            $table->decimal('total', 10, 2)->default(0)->change();
        });

        // PRESUPUESTO_ITEMS
        Schema::table('presupuesto_items', function (Blueprint $table) {
            $table->decimal('precio_unitario', 10, 2)->change();
            $table->decimal('subtotal', 10, 2)->change();
        });
    }
};
