<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presupuesto_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presupuesto_id')
                  ->constrained('presupuestos')
                  ->onDelete('cascade');
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('restrict'); // No permitir eliminar productos con presupuestos

            $table->integer('orden')->default(0);
            $table->text('descripcion'); // Descripción del producto al momento del presupuesto
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();

            // Índices
            $table->index('presupuesto_id');
            $table->index('producto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presupuesto_items');
    }
};
