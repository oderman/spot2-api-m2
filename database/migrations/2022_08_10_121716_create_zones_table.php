<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('call_numero');
            $table->integer('codigo_postal');
            $table->string('colonia_predio');
            $table->decimal('superficie_terreno', $precision = 8, $scale = 2);
            $table->decimal('superficie_construccion', $precision = 8, $scale = 2);
            $table->string('uso_construccion');
            $table->integer('clave_rango_nivel');
            $table->integer('anio_construccion');
            $table->char('instalaciones_especiales', 4);
            $table->decimal('valor_unitario_suelo', $precision = 8, $scale = 2);
            $table->decimal('valor_suelo', $precision = 15, $scale = 2);
            $table->string('clave_valor_unitario_suelo');
            $table->string('colonia_cumpliemiento');
            $table->string('alcaldia_cumplimiento');
            $table->decimal('subsidio', $precision = 8, $scale = 2);
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zones');
    }
};
