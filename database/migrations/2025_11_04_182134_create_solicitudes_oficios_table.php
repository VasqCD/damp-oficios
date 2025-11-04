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
        Schema::create('solicitudes_oficios', function (Blueprint $table) {
            $table->id();
            $table->string('numero_oficio_entrante')->unique();
            $table->date('fecha_recepcion');
            $table->foreignId('institucion_id')->constrained('instituciones')->onDelete('cascade');
            $table->foreignId('unidad_id')->constrained('unidades')->onDelete('cascade');
            $table->foreignId('agente_solicitante_id')->constrained('agentes')->onDelete('cascade');
            $table->foreignId('delito_id')->nullable()->constrained('delitos')->onDelete('set null');
            $table->string('ofendido')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['pendiente', 'en_proceso', 'respondida'])->default('pendiente');
            $table->foreignId('usuario_registro_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('numero_oficio_entrante');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_oficios');
    }
};
