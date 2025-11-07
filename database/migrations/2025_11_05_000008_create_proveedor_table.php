<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedor', function (Blueprint $table) {
            $table->id('prov_id');
            $table->string('prov_nombre', 150);
            $table->string('prov_ruc', 20)->unique();
            $table->string('prov_telefono', 20)->nullable();
            $table->string('prov_email', 100)->nullable();
            $table->string('prov_direccion', 200)->nullable();
            $table->string('prov_ciudad', 100)->nullable();
            $table->string('prov_contacto', 100)->nullable(); // Persona de contacto
            $table->string('prov_estado', 20)->default('ACTIVO'); // ACTIVO, INACTIVO
            $table->text('prov_observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedor');
    }
};
