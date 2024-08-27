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
        Schema::create('resenas', function (Blueprint $table) {
            $table->bigIncrements('resenaID');
            $table->unsignedBigInteger('clienteID');
            $table->unsignedBigInteger('hotelID');
            $table->string('nombre_cliente', 40);
            $table->timestamp('fecha');
            $table->text('texto');
            $table->string('puntuacion');
            $table->timestamps();

            $table->foreign('clienteID')->references('clienteID')->on('clientes');
            $table->foreign('hotelID')->references('hotelID')->on('hoteles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
