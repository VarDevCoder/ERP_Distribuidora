<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_compra', function (Blueprint $table) {
            $table->id('det_com_id');
            $table->foreignId('com_id')->constrained('compra', 'com_id')->onDelete('cascade');
            $table->foreignId('pro_id')->constrained('producto', 'pro_id');
            $table->integer('det_com_cantidad');
            $table->decimal('det_com_precio_unitario', 10, 2);
            $table->decimal('det_com_subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_compra');
    }
};
