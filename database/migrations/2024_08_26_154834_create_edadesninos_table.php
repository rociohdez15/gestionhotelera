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
        Schema::create('edadesninos', function (Blueprint $table) {
            $table->bigIncrements('edadesninosID');
            $table->integer('edad'); 
            $table->unsignedBigInteger('reservaID');
            $table->timestamps();

            $table->foreign('reservaID')->references('reservaID')->on('reservas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edadesninos');
    }
};
