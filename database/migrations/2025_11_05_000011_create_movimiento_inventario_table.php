<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimiento_inventario', function (Blueprint $table) {
            $table->id('mov_id');
            $table->foreignId('pro_id')->constrained('producto', 'pro_id');
            $table->foreignId('usu_id')->constrained('usuario', 'usu_id');
            $table->string('mov_tipo', 20); // ENTRADA, SALIDA, AJUSTE
            $table->string('mov_motivo', 50); // COMPRA, VENTA, DEVOLUCION, AJUSTE_INVENTARIO
            $table->integer('mov_cantidad');
            $table->integer('mov_stock_anterior');
            $table->integer('mov_stock_nuevo');
            $table->decimal('mov_costo', 10, 2)->nullable();
            $table->string('mov_referencia', 50)->nullable(); // Número de compra o venta
            $table->integer('mov_referencia_id')->nullable(); // ID de compra o venta
            $table->text('mov_observaciones')->nullable();
            $table->timestamp('mov_fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_inventario');
    }
};
