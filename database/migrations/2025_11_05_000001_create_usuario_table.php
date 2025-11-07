<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id('usu_id');
            $table->string('usu_email', 100)->unique();
            $table->string('usu_pass', 255);
            $table->string('usu_rol', 50);
            $table->string('usu_nombre', 100);
            $table->string('usu_apellido', 100);
            $table->string('usu_estado', 20)->default('ACTIVO');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
