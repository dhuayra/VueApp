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
        Schema::create('contribuyentes', function (Blueprint $table) {
            $table->id();
            $table->string('ruc')->index();;
            $table->string('nombre_razon_social', 155);
            $table->string('estado_contribuyente');
            $table->string('condicion_domicilio', 155);
            $table->string('ubigeo');
            $table->string('tipo_via');
            $table->string('nombre_via');
            $table->string('codigo_zona', 155);
            $table->string('tipo_zona');
            $table->string('numero', 155);
            $table->string('interior');
            $table->string('lote');
            $table->string('departamento');
            $table->string('manzana');
            $table->string('kilometro');
            $table->tinyInteger('estado')->default(1);
            $table->tinyInteger('agenperc_estado')->default(1);
            $table->tinyInteger('agenret_estado')->default(1);
            $table->tinyInteger('buecont_estado')->default(1);
            $table->string('fecha_actualizado', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contribuyentes');
    }
};
