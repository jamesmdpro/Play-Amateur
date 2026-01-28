<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Resultado;
use App\Models\Partido;
use App\Models\User;

class ResultadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partidos = Partido::where('estado', 'finalizado')->get();

        foreach ($partidos as $partido) {
            if ($partido->arbitro_id) {
                Resultado::create([
                    'partido_id' => $partido->id,
                    'arbitro_id' => $partido->arbitro_id,
                    'marcador_equipo1' => rand(0, 5),
                    'marcador_equipo2' => rand(0, 5),
                    'estado' => collect(['pendiente_validacion', 'validado', 'rechazado'])->random(),
                    'notas' => rand(0, 1) ? 'Resultado registrado correctamente' : null,
                    'validaciones_equipo1' => rand(0, 1) ? [
                        rand(1, 10) => [
                            'aceptado' => (bool) rand(0, 1),
                            'notas' => rand(0, 1) ? 'Buen partido' : null,
                            'fecha' => now()->subMinutes(rand(1, 60))
                        ]
                    ] : null,
                    'validaciones_equipo2' => rand(0, 1) ? [
                        rand(1, 10) => [
                            'aceptado' => (bool) rand(0, 1),
                            'notas' => rand(0, 1) ? 'Excelente arbitraje' : null,
                            'fecha' => now()->subMinutes(rand(1, 60))
                        ]
                    ] : null,
                ]);
            }
        }
    }
}