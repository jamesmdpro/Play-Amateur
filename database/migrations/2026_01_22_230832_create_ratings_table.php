<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calificador_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('calificado_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->enum('tipo', ['jugador_jugador', 'arbitro_jugador', 'jugador_arbitro']);
            $table->integer('puntuacion')->min(1)->max(5);
            $table->text('comentario')->nullable();
            $table->timestamps();

            $table->unique(['calificador_id', 'calificado_id', 'partido_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};