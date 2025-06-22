<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PRODUCTOS
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('id_unidadmedida')->unsigned();
            $table->string('nombre', 200);
            $table->decimal('precio',10,2);
            $table->string('codigo', 100)->nullable();

            $table->foreign('id_unidadmedida')->references('id')->on('unidadmedida');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
