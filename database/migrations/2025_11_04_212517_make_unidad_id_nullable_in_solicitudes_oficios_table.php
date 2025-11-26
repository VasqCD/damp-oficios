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
            // Hacemos unidad_id nullable
            $table->foreignId('unidad_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes_oficios', function (Blueprint $table) {
            // Revertimos: hacemos unidad_id no nullable
            $table->foreignId('unidad_id')->nullable(false)->change();
        });
    }
};
