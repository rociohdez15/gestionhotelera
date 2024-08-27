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
        Schema::table('users', function (Blueprint $table) {
            // Agregar nuevos campos
            $table->string('apellidos')->nullable();
            $table->unsignedBigInteger('rolID');
            
            $table->foreign('rolID')->references('rolID')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            /// Eliminar los nuevos campos agregados
            $table->dropColumn(['apellidos','rolID']);

            // Eliminar la clave foránea
            $table->dropForeign(['rolID']);
        });
    }
};
