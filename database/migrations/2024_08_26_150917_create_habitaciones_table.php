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
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->bigIncrements('habitacionID');
            $table->string('numhabitacion', 40);
            $table->string('tipohabitacion', 40);
            $table->string('disponibilidad', 40);
            $table->decimal('precio', 7, 2);
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
        Schema::dropIfExists('habitaciones');
    }
};
