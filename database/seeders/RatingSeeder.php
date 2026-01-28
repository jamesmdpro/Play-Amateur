<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partidos = \App\Models\Partido::with(['jugadores', 'arbitro'])->get();

        foreach ($partidos as $partido) {
            $jugadores = $partido->jugadores;

            // Ratings de jugadores a árbitro
            if ($partido->arbitro) {
                foreach ($jugadores->random(min(3, $jugadores->count())) as $jugador) {
                    \App\Models\Rating::create([
                        'calificador_id' => $jugador->id,
                        'calificado_id' => $partido->arbitro->id,
                        'partido_id' => $partido->id,
                        'tipo' => 'jugador_arbitro',
                        'puntuacion' => rand(1, 5),
                        'comentario' => collect([
                            'Excelente arbitraje',
                            'Muy justo en las decisiones',
                            'Podría mejorar la puntualidad',
                            'Buen control del partido'
                        ])->random(),
                    ]);
                }
            }

            // Ratings entre jugadores
            if ($jugadores->count() >= 2) {
                $pares = $jugadores->random(min(4, $jugadores->count() * 2));

                for ($i = 0; $i < $pares->count() - 1; $i += 2) {
                    $calificador = $pares[$i];
                    $calificado = $pares[$i + 1];

                    \App\Models\Rating::create([
                        'calificador_id' => $calificador->id,
                        'calificado_id' => $calificado->id,
                        'partido_id' => $partido->id,
                        'tipo' => 'jugador_jugador',
                        'puntuacion' => rand(1, 5),
                        'comentario' => collect([
                            'Buen compañero de equipo',
                            'Excelente en su posición',
                            'Siempre dispuesto a ayudar',
                            'Mejoró mucho últimamente'
                        ])->random(),
                    ]);
                }
            }

            // Ratings de árbitro a jugadores
            if ($partido->arbitro) {
                foreach ($jugadores->random(min(5, $jugadores->count())) as $jugador) {
                    \App\Models\Rating::create([
                        'calificador_id' => $partido->arbitro->id,
                        'calificado_id' => $jugador->id,
                        'partido_id' => $partido->id,
                        'tipo' => 'arbitro_jugador',
                        'puntuacion' => rand(1, 5),
                        'comentario' => collect([
                            'Buen comportamiento',
                            'Excelente técnica',
                            'Comprometido con el equipo',
                            'Necesita trabajar la disciplina'
                        ])->random(),
                    ]);
                }
            }
        }
    }
}
