<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_venta', function (Blueprint $table) {
            $table->id('det_id');
            $table->foreignId('ven_id')->constrained('venta', 'ven_id')->onDelete('cascade');
            $table->foreignId('pro_id')->constrained('producto', 'pro_id');
            $table->integer('det_cantidad');
            $table->decimal('det_precio_unitario', 10, 2);
            $table->decimal('det_subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_venta');
    }
};
