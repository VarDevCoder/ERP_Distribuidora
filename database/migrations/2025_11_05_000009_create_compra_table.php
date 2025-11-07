<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compra', function (Blueprint $table) {
            $table->id('com_id');
            $table->string('com_numero', 50)->unique();
            $table->foreignId('prov_id')->constrained('proveedor', 'prov_id');
            $table->foreignId('usu_id')->constrained('usuario', 'usu_id');
            $table->date('com_fecha');
            $table->string('com_factura', 50)->nullable(); // Número de factura del proveedor
            $table->decimal('com_subtotal', 10, 2);
            $table->decimal('com_descuento', 10, 2)->default(0);
            $table->decimal('com_total', 10, 2);
            $table->string('com_estado', 20)->default('COMPLETADA'); // COMPLETADA, PENDIENTE, ANULADA
            $table->text('com_observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra');
    }
};
