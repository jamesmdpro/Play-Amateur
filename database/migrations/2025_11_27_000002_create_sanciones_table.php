<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('partido_id')->nullable()->constrained('partidos')->onDelete('cascade');
            $table->string('tipo');
            $table->integer('numero_sancion')->default(1);
            $table->integer('dias_suspension');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('monto_reactivacion', 10, 2)->default(15000);
            $table->boolean('pagada')->default(false);
            $table->boolean('activa')->default(true);
            $table->text('motivo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanciones');
    }
};
