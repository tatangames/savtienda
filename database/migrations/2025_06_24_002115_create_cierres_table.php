<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CIERRE
     */
    public function up(): void
    {
        Schema::create('cierres', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_trabajador')->unsigned();
            $table->date('fecha_inicio'); // fecha desde la que comienza el conteo
            $table->date('fecha_cierre'); // fecha del cierre actual (hoy)
            $table->integer('total_esperado'); // -- suma total de productos vendidos según entregas y conteo
            $table->decimal('total_recibido', 10, 2); // -- suma de todos los pagos registrados en ese ciclo
            $table->decimal('diferencia', 10, 2); // total_esperado - total_recibido

            $table->string('descripcion', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cierres');
    }
};
