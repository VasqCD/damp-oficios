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
        Schema::create('respuestas_oficios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_oficio_id')->unique()->constrained('solicitudes_oficios')->onDelete('cascade');
            $table->string('numero_oficio_respuesta')->unique();
            $table->integer('correlativo');
            $table->year('anio');
            $table->date('fecha_respuesta');
            $table->foreignId('analista_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jefe_regional_id')->constrained('users')->onDelete('cascade');
            $table->text('contenido_respuesta')->nullable();
            $table->enum('estado', ['borrador', 'firmada', 'enviada'])->default('borrador');
            $table->timestamps();

            $table->index('numero_oficio_respuesta');
            $table->index(['correlativo', 'anio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respuestas_oficios');
    }
};
