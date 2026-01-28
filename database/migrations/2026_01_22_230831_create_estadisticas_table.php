<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estadisticas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_id')->constrained('users')->onDelete('cascade');
            $table->integer('partidos_jugados')->default(0);
            $table->integer('goles')->default(0);
            $table->integer('tarjetas_amarillas')->default(0);
            $table->integer('tarjetas_rojas')->default(0);
            $table->string('posicion_mas_usada')->nullable();
            $table->decimal('puntualidad_promedio', 5, 2)->default(0);
            $table->json('calificaciones')->nullable();
            $table->timestamps();

            $table->unique('jugador_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estadisticas');
    }
};