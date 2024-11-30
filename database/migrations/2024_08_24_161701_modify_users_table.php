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
            // Verificar si la columna 'apellidos' no existe antes de agregarla
            if (!Schema::hasColumn('users', 'apellidos')) {
                $table->string('apellidos')->nullable();
            }

            // Verificar si la columna 'rolID' no existe antes de agregarla
            if (!Schema::hasColumn('users', 'rolID')) {
                $table->unsignedBigInteger('rolID')->nullable();
            } else {
                // Si la columna ya existe, asegurarse de que permita valores NULL
                $table->unsignedBigInteger('rolID')->nullable()->change();
            }
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
