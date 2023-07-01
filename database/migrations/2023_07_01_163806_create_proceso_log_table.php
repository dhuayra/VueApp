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
        Schema::create('proceso_log', function (Blueprint $table) {
            $table->id();
            $table->string('ruc')->index();
            $table->string('fecha_actualizacion', 50);
            $table->string('tipo_actualizacion', 50);
            $table->string('tabla_actualizado', 200);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proceso_log');
    }
};
