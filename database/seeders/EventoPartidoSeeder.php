<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EventoPartido;
use App\Models\Partido;
use App\Models\Inscripcion;

class EventoPartidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partidos = Partido::with('inscripciones.jugador')->get();

        foreach ($partidos as $partido) {
            $jugadores = $partido->inscripciones->pluck('jugador');

            // Solo crear eventos si hay jugadores inscritos
            if ($jugadores->isEmpty()) {
                continue;
            }

            // Crear algunos eventos aleatorios
            $numEventos = rand(0, 8);

            for ($i = 0; $i < $numEventos; $i++) {
                $jugador = $jugadores->random();
                $tipo = collect(['gol', 'tarjeta_amarilla', 'tarjeta_roja', 'lesion'])->random();

                EventoPartido::create([
                    'partido_id' => $partido->id,
                    'tipo' => $tipo,
                    'jugador_id' => $jugador->id,
                    'minuto' => rand(1, 90),
                    'descripcion' => $tipo === 'gol' ? 'Gol anotado' : ($tipo === 'lesion' ? 'Lesión sufrida' : null),
                ]);
            }
        }
    }
}