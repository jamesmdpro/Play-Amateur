<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_hora');
            $table->string('ubicacion');
            $table->integer('cupos_totales');
            $table->integer('cupos_suplentes')->default(0);
            $table->decimal('costo', 10, 2)->default(0);
            $table->enum('estado', ['abierto', 'cerrado', 'en_curso', 'finalizado'])->default('abierto');
            $table->foreignId('creador_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};
