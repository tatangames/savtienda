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
        Schema::create('fondo_iniciales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_trabajador')->unsigned();
            $table->date('fecha');
            $table->decimal('monto', 10, 2);
            $table->string('descripcion', 300)->nullable();

            $table->foreign('id_trabajador')->references('id')->on('trabajadores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fondo_iniciales');
    }
};
