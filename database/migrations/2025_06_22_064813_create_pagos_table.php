<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * dinero que el trabajador entrega
     * // SIEMPRE SERA EL MISMO TRABAJADOR
     */
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_trabajador')->unsigned();

            $table->date('fecha');
            $table->decimal('monto', 10, 2);

            //(opcional) nota como "faltó una soda"
            $table->string('descripcion', 300)->nullable();

            $table->foreign('id_trabajador')->references('id')->on('trabajadores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
