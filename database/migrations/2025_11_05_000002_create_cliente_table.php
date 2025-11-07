<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente', function (Blueprint $table) {
            $table->id('cli_id');
            $table->string('cli_nombre', 100);
            $table->string('cli_apellido', 100);
            $table->string('cli_ci', 20)->unique();
            $table->string('cli_telefono', 20)->nullable();
            $table->string('cli_direccion', 150)->nullable();
            $table->string('cli_email', 100)->nullable();
            $table->string('cli_tipo', 50)->default('MINORISTA'); // MINORISTA, MAYORISTA
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
