<?php

namespace Database\Seeders;

use App\Models\Partido;
use App\Models\User;
use App\Models\Inscripcion;
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
        $jugador = User::where('rol', 'jugador')->first();

        // Agregar saldo suficiente a los usuarios para las inscripciones
        if ($jugador) {
            $jugador->update(['wallet' => 1000000]); // 1 millón de pesos
        }
        if ($cancha) {
            $cancha->update(['wallet' => 1000000]);
        }
        if ($admin) {
            $admin->update(['wallet' => 1000000]);
        }

        Partido::create([
            'nombre' => 'Partido Amistoso - Sábado',
            'descripcion' => 'Partido amistoso para todos los niveles. ¡Todos bienvenidos!',
            'fecha_hora' => Carbon::now()->addDays(3)->setTime(18, 0),
            'ubicacion' => 'Cancha Central - Buenos Aires',
            'cupos_totales' => 14,
            'cupos_suplentes' => 4,
            'costo' => 150000.00,
            'costo_por_jugador' => 150000 / 14,
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
            'costo' => 200000.00,
            'costo_por_jugador' => 200000 / 14,
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
            'costo' => 210000.00,
            'costo_por_jugador' => 210000 / 10,
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
            'costo' => 240000.00,
            'costo_por_jugador' => 240000 / 16,
            'estado' => 'abierto',
            'creador_id' => $admin->id,
        ]);

        $jugador = User::where('rol', 'jugador')->first();

        // Partido al que se inscribió el usuario (estado: abierto)
        $partidoInscrito = Partido::create([
            'nombre' => 'Partido de Prueba - Inscrito',
            'descripcion' => 'Partido de prueba donde el usuario está inscrito.',
            'fecha_hora' => Carbon::now()->addDays(2)->setTime(19, 0),
            'ubicacion' => 'Cancha de Prueba',
            'cupos_totales' => 12,
            'cupos_suplentes' => 2,
            'costo' => 180000.00,
            'costo_por_jugador' => 180000 / 12,
            'estado' => 'abierto',
            'creador_id' => $cancha->id,
        ]);

        // Crear inscripción para el jugador
        if ($jugador) {
            Inscripcion::create([
                'partido_id' => $partidoInscrito->id,
                'jugador_id' => $jugador->id,
                'estado' => 'confirmado',
            ]);
        }

        // Partido en marcha (estado: en_curso)
        $partidoEnMarcha = Partido::create([
            'nombre' => 'Partido en Marcha - Actual',
            'descripcion' => 'Partido que está actualmente en curso.',
            'fecha_hora' => Carbon::now()->subHours(1), // Empezó hace 1 hora
            'ubicacion' => 'Cancha Activa',
            'cupos_totales' => 14,
            'cupos_suplentes' => 2,
            'costo' => 160000.00,
            'costo_por_jugador' => 160000 / 14,
            'estado' => 'en_curso',
            'creador_id' => $admin->id,
        ]);

        // Crear inscripción para el jugador en el partido en marcha
        if ($jugador) {
            Inscripcion::create([
                'partido_id' => $partidoEnMarcha->id,
                'jugador_id' => $jugador->id,
                'estado' => 'confirmado',
            ]);
        }

        // Primer partido finalizado
        $partidoFinalizado1 = Partido::create([
            'nombre' => 'Partido Finalizado - Semana Pasada',
            'descripcion' => 'Partido que terminó la semana pasada.',
            'fecha_hora' => Carbon::now()->subDays(7)->setTime(18, 0),
            'ubicacion' => 'Cancha Histórica',
            'cupos_totales' => 14,
            'cupos_suplentes' => 2,
            'costo' => 170000.00,
            'costo_por_jugador' => 170000 / 14,
            'estado' => 'finalizado',
            'creador_id' => $cancha->id,
        ]);

        // Crear inscripción para el jugador en el primer partido finalizado
        if ($jugador) {
            Inscripcion::create([
                'partido_id' => $partidoFinalizado1->id,
                'jugador_id' => $jugador->id,
                'estado' => 'confirmado',
            ]);
        }

        // Segundo partido finalizado
        $partidoFinalizado2 = Partido::create([
            'nombre' => 'Torneo Finalizado - Mes Pasado',
            'descripcion' => 'Torneo que terminó el mes pasado.',
            'fecha_hora' => Carbon::now()->subDays(30)->setTime(16, 0),
            'ubicacion' => 'Complejo Deportivo Central',
            'cupos_totales' => 16,
            'cupos_suplentes' => 4,
            'costo' => 220000.00,
            'costo_por_jugador' => 220000 / 16,
            'estado' => 'finalizado',
            'creador_id' => $admin->id,
        ]);

        // Crear inscripción para el jugador en el segundo partido finalizado
        if ($jugador) {
            Inscripcion::create([
                'partido_id' => $partidoFinalizado2->id,
                'jugador_id' => $jugador->id,
                'estado' => 'confirmado',
            ]);
        }

        $arbitro = User::where('rol', 'arbitro')->first();

        // Asignar árbitro al partido en marcha
        if ($arbitro) {
            $partidoEnMarcha->update(['arbitro_id' => $arbitro->id]);
        }

        // Asignar árbitro al primer partido finalizado
        if ($arbitro) {
            $partidoFinalizado1->update(['arbitro_id' => $arbitro->id]);
        }

        // Asignar árbitro al segundo partido finalizado
        if ($arbitro) {
            $partidoFinalizado2->update(['arbitro_id' => $arbitro->id]);
        }
    }
}
