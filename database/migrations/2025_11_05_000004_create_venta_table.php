<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venta', function (Blueprint $table) {
            $table->id('ven_id');
            $table->string('ven_numero', 50)->unique();
            $table->foreignId('cli_id')->constrained('cliente', 'cli_id');
            $table->foreignId('usu_id')->constrained('usuario', 'usu_id');
            $table->date('ven_fecha');
            $table->decimal('ven_subtotal', 10, 2);
            $table->decimal('ven_descuento', 10, 2)->default(0);
            $table->decimal('ven_total', 10, 2);
            $table->string('ven_estado', 20)->default('COMPLETADA'); // COMPLETADA, PENDIENTE, ANULADA
            $table->text('ven_observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta');
    }
};
