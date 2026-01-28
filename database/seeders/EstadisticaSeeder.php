<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Estadistica;
use App\Models\User;

class EstadisticaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jugadores = User::where('rol', 'jugador')->get();

        foreach ($jugadores as $jugador) {
            Estadistica::create([
                'jugador_id' => $jugador->id,
                'partidos_jugados' => rand(5, 20),
                'goles' => rand(0, 15),
                'tarjetas_amarillas' => rand(0, 5),
                'tarjetas_rojas' => rand(0, 2),
                'posicion_mas_usada' => collect(['arquero', 'defensa', 'medio', 'ataque'])->random(),
                'puntualidad_promedio' => rand(30, 50) / 10,
                'calificaciones' => [
                    'comportamiento_competitivo' => [
                        'total' => rand(50, 200),
                        'cantidad' => rand(5, 20)
                    ],
                    'toma_decisiones' => [
                        'total' => rand(50, 200),
                        'cantidad' => rand(5, 20)
                    ],
                    'impacto_resultado' => [
                        'total' => rand(50, 200),
                        'cantidad' => rand(5, 20)
                    ],
                    'capacidad_defensiva' => [
                        'total' => rand(50, 200),
                        'cantidad' => rand(5, 20)
                    ],
                    'eficacia' => [
                        'total' => rand(50, 200),
                        'cantidad' => rand(5, 20)
                    ],
                    'participacion_juego' => [
                        'total' => rand(50, 200),
                        'cantidad' => rand(5, 20)
                    ],
                ],
            ]);
        }
    }
}