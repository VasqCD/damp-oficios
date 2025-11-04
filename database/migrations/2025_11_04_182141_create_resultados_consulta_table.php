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
        Schema::create('resultados_consulta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('respuesta_oficio_id')->constrained('respuestas_oficios')->onDelete('cascade');
            $table->foreignId('persona_solicitada_id')->constrained('personas_solicitadas')->onDelete('cascade');
            $table->foreignId('persona_registrada_id')->nullable()->constrained('personas_registradas')->onDelete('set null');
            $table->boolean('encontrada')->default(false);
            $table->json('detalles')->nullable();
            $table->timestamps();

            $table->index('respuesta_oficio_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados_consulta');
    }
};
