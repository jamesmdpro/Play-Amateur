<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained('partidos')->onDelete('cascade');
            $table->foreignId('arbitro_id')->constrained('users')->onDelete('cascade');
            $table->integer('marcador_equipo1')->default(0);
            $table->integer('marcador_equipo2')->default(0);
            $table->enum('estado', ['pendiente_validacion', 'validado', 'rechazado'])->default('pendiente_validacion');
            $table->text('notas')->nullable();
            $table->json('validaciones_equipo1')->nullable();
            $table->json('validaciones_equipo2')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};