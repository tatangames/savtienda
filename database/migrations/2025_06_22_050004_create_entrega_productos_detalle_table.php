<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * DETALLE DE LOS PRODUCTOS ENTREGADOS
     */
    public function up(): void
    {
        Schema::create('entrega_productos_detalle', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_entregaproductos')->unsigned();

            $table->bigInteger('id_producto')->unsigned();
            $table->integer('cantidad');

            // se pondra una copia de precio, ya que puede cambiar
            $table->integer('precio_venta');

            // cantidad * precio unitario
            $table->decimal('subtotal', 10, 2);

            $table->foreign('id_entregaproductos')->references('id')->on('entrega_productos');
            $table->foreign('id_producto')->references('id')->on('productos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_productos_detalle');
    }
};
