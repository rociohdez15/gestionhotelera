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
        Schema::create('imagenes_hoteles', function (Blueprint $table) {
            $table->bigIncrements('imagenID');
            $table->string('imagen', 255);
            $table->string('nombre_imagen', 40);
            $table->unsignedBigInteger('hotelID');
            $table->timestamps();

            $table->foreign('hotelID')->references('hotelID')->on('hoteles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes_hoteles');
    }
};
