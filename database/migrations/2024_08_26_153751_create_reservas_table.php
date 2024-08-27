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
        Schema::create('reservas', function (Blueprint $table) {
            $table->bigIncrements('reservaID');
            $table->date('fechainicio'); 
            $table->date('fechafin'); 
            $table->string('estado', 40);
            $table->decimal('preciototal', 7, 2);
            $table->integer('num_adultos'); 
            $table->integer('num_ninos'); 
            $table->date('fecha_checkin'); 
            $table->date('fecha_checkout'); 
            $table->unsignedBigInteger('clienteID');
            $table->unsignedBigInteger('habitacionID');
            $table->timestamps();

            $table->foreign('clienteID')->references('clienteID')->on('clientes');
            $table->foreign('habitacionID')->references('habitacionID')->on('habitaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
