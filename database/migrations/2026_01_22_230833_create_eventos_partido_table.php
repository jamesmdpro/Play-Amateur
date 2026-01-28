<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos_partido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->enum('tipo', ['gol', 'tarjeta_amarilla', 'tarjeta_roja', 'lesion', 'cambio']);
            $table->foreignId('jugador_id')->constrained('users')->onDelete('cascade');
            $table->integer('minuto');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_partido');
    }
};