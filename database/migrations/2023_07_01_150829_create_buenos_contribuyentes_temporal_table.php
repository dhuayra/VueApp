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
        Schema::create('buenos_contribuyentes_temporal', function (Blueprint $table) {
            $table->id();
            $table->string('ruc')->index();;
            $table->string('nombre_razon_social', 155);
            $table->string('a_partir_del', 50);
            $table->string('resolucion', 155);
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buenos_contribuyentes_temporal');
    }
};
