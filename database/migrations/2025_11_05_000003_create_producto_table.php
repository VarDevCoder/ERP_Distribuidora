<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->id('pro_id');
            $table->string('pro_codigo', 50)->unique();
            $table->string('pro_nombre', 100);
            $table->string('pro_categoria', 50);
            $table->text('pro_descripcion')->nullable();
            $table->decimal('pro_precio_compra', 10, 2);
            $table->decimal('pro_precio_venta', 10, 2);
            $table->integer('pro_stock');
            $table->integer('pro_stock_minimo')->default(10);
            $table->string('pro_unidad_medida', 20)->default('UNIDAD'); // UNIDAD, CAJA, KG, LITRO
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};
