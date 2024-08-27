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
        Schema::create('clientes', function (Blueprint $table) {
            $table->bigIncrements('clienteID');
            $table->string('nombre', 40);
            $table->string('apellidos', 40);
            $table->string('direccion', 40);
            $table->string('telefono', 9);
            $table->string('dni', 9);
            $table->string('email', 40);
            $table->string('password', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
