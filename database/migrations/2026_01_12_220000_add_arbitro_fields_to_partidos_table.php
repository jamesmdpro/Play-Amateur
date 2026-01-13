<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partidos', function (Blueprint $table) {
            $table->boolean('con_arbitro')->default(false)->after('costo');
            $table->decimal('costo_por_jugador', 10, 2)->default(0)->after('con_arbitro');
        });
    }

    public function down(): void
    {
        Schema::table('partidos', function (Blueprint $table) {
            $table->dropColumn(['con_arbitro', 'costo_por_jugador']);
        });
    }
};
