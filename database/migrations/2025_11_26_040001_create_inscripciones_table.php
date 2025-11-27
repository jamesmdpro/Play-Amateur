<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->foreignId('jugador_id')->constrained('users')->onDelete('cascade');
            $table->boolean('es_suplente')->default(false);
            $table->integer('equipo')->nullable();
            $table->enum('estado', ['confirmado', 'pendiente', 'cancelado'])->default('confirmado');
            $table->timestamps();
            
            $table->unique(['partido_id', 'jugador_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
