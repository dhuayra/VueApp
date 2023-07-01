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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('dni')->index();
            $table->string('ruc')->index();
            $table->string('nombre_completo', 200);
            $table->string('nombres', 200);
            $table->string('apellido_paterno', 155);
            $table->string('apellido_materno', 155);
            $table->string('fecha_actualizado', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
