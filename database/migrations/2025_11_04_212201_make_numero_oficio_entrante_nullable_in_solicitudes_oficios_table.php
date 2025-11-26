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
        Schema::table('solicitudes_oficios', function (Blueprint $table) {
            // Primero eliminamos el índice único
            $table->dropUnique('solicitudes_oficios_numero_oficio_entrante_unique');

            // Hacemos el campo nullable
            $table->string('numero_oficio_entrante', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes_oficios', function (Blueprint $table) {
            // Revertimos: hacemos el campo no nullable
            $table->string('numero_oficio_entrante', 100)->nullable(false)->change();

            // Recreamos el índice único
            $table->unique('numero_oficio_entrante');
        });
    }
};
