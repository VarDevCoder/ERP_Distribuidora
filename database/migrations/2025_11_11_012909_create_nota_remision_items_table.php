<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota_remision_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_remision_id')
                  ->constrained('notas_remision')
                  ->onDelete('cascade');
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('restrict');

            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 10, 2);

            $table->timestamps();

            // Índices
            $table->index('nota_remision_id');
            $table->index('producto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota_remision_items');
    }
};
