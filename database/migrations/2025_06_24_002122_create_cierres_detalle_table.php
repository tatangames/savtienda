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
        Schema::create('cierres_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cierres')->unsigned();
            $table->bigInteger('id_producto')->unsigned();
            $table->integer('entregado_total'); // cantidad entregada acumulada
            $table->integer('quedo_total'); // cantidad que quedo fisicamente al cierre
            $table->integer('vendido_total'); // entregado_total - quedo_total
            $table->decimal('precio_unitario', 10, 2); // para calcular el total
            $table->decimal('total_esperado', 10, 2); // vendido_total * precio unitario

            $table->foreign('id_cierres')->references('id')->on('cierres');
            $table->foreign('id_producto')->references('id')->on('productos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cierres_detalle');
    }
};
