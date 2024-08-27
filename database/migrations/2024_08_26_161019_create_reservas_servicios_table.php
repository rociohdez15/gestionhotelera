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
        Schema::create('reservas_servicios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservaID');
            $table->unsignedBigInteger('servicioID');
            $table->timestamps();

            $table->foreign('reservaID')->references('reservaID')->on('reservas');
            $table->foreign('servicioID')->references('servicioID')->on('servicios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas_servicios');
    }
};
