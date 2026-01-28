<?php

namespace App\Console\Commands;

use App\Models\Partido;
use Illuminate\Console\Command;

class UpdatePartidoStates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'partidos:update-states';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza automáticamente los estados de los partidos según el horario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Actualizando estados de partidos...');

        // Iniciar partidos que ya pasaron su hora de inicio
        $partidosAIniciar = Partido::where('estado', 'programado')
            ->where('fecha_hora', '<=', now())
            ->get();

        foreach ($partidosAIniciar as $partido) {
            $partido->iniciarPartido();
            $this->info("Partido {$partido->id} iniciado: {$partido->nombre}");
        }

        // Finalizar partidos que ya pasaron 1 hora de su inicio
        $partidosAFinalizar = Partido::where('estado', 'en_curso')
            ->whereRaw('DATE_ADD(fecha_hora, INTERVAL 1 HOUR) <= ?', [now()])
            ->get();

        foreach ($partidosAFinalizar as $partido) {
            $partido->finalizarPartido();
            $this->info("Partido {$partido->id} finalizado: {$partido->nombre}");
        }

        $this->info('Estados de partidos actualizados correctamente.');
    }
}
