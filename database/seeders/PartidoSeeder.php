<?php

namespace Database\Seeders;

use App\Models\Partido;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PartidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cancha = User::where('rol', 'cancha')->first();
        $admin = User::where('rol', 'admin')->first();

        Partido::create([
            'nombre' => 'Partido Amistoso - Sábado',
            'descripcion' => 'Partido amistoso para todos los niveles. ¡Todos bienvenidos!',
            'fecha_hora' => Carbon::now()->addDays(3)->setTime(18, 0),
            'ubicacion' => 'Cancha Central - Buenos Aires',
            'cupos_totales' => 14,
            'cupos_suplentes' => 4,
            'costo' => 150.00,
            'estado' => 'abierto',
            'creador_id' => $cancha->id,
        ]);

        Partido::create([
            'nombre' => 'Torneo Relámpago',
            'descripcion' => 'Torneo de fútbol 7. Nivel intermedio-avanzado.',
            'fecha_hora' => Carbon::now()->addDays(7)->setTime(16, 0),
            'ubicacion' => 'Complejo Deportivo Norte',
            'cupos_totales' => 14,
            'cupos_suplentes' => 2,
            'costo' => 200.00,
            'estado' => 'abierto',
            'creador_id' => $admin->id,
        ]);

        Partido::create([
            'nombre' => 'Partido Nocturno',
            'descripcion' => 'Partido bajo las luces. Cancha sintética.',
            'fecha_hora' => Carbon::now()->addDays(5)->setTime(21, 0),
            'ubicacion' => 'Cancha Sintética Sur',
            'cupos_totales' => 10,
            'cupos_suplentes' => 2,
            'costo' => 120.00,
            'estado' => 'abierto',
            'creador_id' => $cancha->id,
        ]);

        Partido::create([
            'nombre' => 'Clásico del Domingo',
            'descripcion' => 'El clásico partido de los domingos. Tradición futbolera.',
            'fecha_hora' => Carbon::now()->addDays(10)->setTime(10, 0),
            'ubicacion' => 'Cancha Central - Buenos Aires',
            'cupos_totales' => 16,
            'cupos_suplentes' => 4,
            'costo' => 180.00,
            'estado' => 'abierto',
            'creador_id' => $admin->id,
        ]);
    }
}
