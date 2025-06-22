<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * registro de productos entregados al trabajador
     * SOLO HABRA 1 TRABAJADOR
     */
    public function up(): void
    {
        Schema::create('entrega_productos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('descripcion', 300)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_productos');
    }
};
