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
        Schema::create('proveedor_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->string('codigo_proveedor')->nullable();
            $table->string('nombre_proveedor')->nullable();
            $table->integer('precio')->default(0);
            $table->boolean('disponible')->default(true);
            $table->integer('tiempo_entrega_dias')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['proveedor_id', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedor_productos');
    }
};
