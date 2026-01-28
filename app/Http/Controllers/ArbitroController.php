<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Resultado;
use App\Models\EventoPartido;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArbitroController extends Controller
{
    public function dashboard()
    {
        $arbitro = Auth::user();

        $partidosAsignados = Partido::where('arbitro_id', $arbitro->id)
            ->where('estado', '!=', 'finalizado')
            ->count();

        $partidosCompletados = Partido::where('arbitro_id', $arbitro->id)
            ->where('estado', 'finalizado')
            ->count();

        $ingresosMes = 0; // Calcular basado en partidos arbitrados

        return view('arbitro.dashboard', compact('partidosAsignados', 'partidosCompletados', 'ingresosMes'));
    }

    public function partidos()
    {
        $arbitro = Auth::user();

        $partidos = Partido::with(['creador', 'inscripciones.jugador'])
            ->where('arbitro_id', $arbitro->id)
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return view('arbitro.partidos', compact('partidos'));
    }

    public function confirmarPresencia(Request $request, $partidoId)
    {
        $arbitro = Auth::user();
        $partido = Partido::where('id', $partidoId)
            ->where('arbitro_id', $arbitro->id)
            ->firstOrFail();

        // Lógica para confirmar presencia
        // Por ahora, solo devolver éxito

        return response()->json(['message' => 'Presencia confirmada']);
    }

    public function registrarResultado(Request $request, $partidoId)
    {
        $arbitro = Auth::user();
        $partido = Partido::where('id', $partidoId)
            ->where('arbitro_id', $arbitro->id)
            ->firstOrFail();

        $request->validate([
            'marcador_equipo1' => 'required|integer|min:0',
            'marcador_equipo2' => 'required|integer|min:0',
            'eventos' => 'sometimes|array',
            'eventos.*.tipo' => 'required|string',
            'eventos.*.jugador_id' => 'required|integer',
            'eventos.*.minuto' => 'required|integer|min:0|max:120',
            'eventos.*.descripcion' => 'sometimes|string',
        ]);

        // Crear o actualizar resultado
        $resultado = Resultado::updateOrCreate(
            ['partido_id' => $partidoId],
            [
                'arbitro_id' => $arbitro->id,
                'marcador_equipo1' => $request->marcador_equipo1,
                'marcador_equipo2' => $request->marcador_equipo2,
                'estado' => 'pendiente_validacion',
            ]
        );

        // Registrar eventos
        if ($request->has('eventos')) {
            foreach ($request->eventos as $eventoData) {
                EventoPartido::create([
                    'partido_id' => $partidoId,
                    'tipo' => $eventoData['tipo'],
                    'jugador_id' => $eventoData['jugador_id'],
                    'minuto' => $eventoData['minuto'],
                    'descripcion' => $eventoData['descripcion'] ?? null,
                ]);
            }
        }

        // Notificar a jugadores para validación
        $this->notificarValidacionResultado($partido, $resultado);

        return response()->json(['message' => 'Resultado registrado exitosamente']);
    }

    private function notificarValidacionResultado($partido, $resultado)
    {
        $equipo1 = $partido->jugadoresTitulares()->wherePivot('equipo', 1)->take(2)->get();
        $equipo2 = $partido->jugadoresTitulares()->wherePivot('equipo', 2)->take(2)->get();

        foreach ($equipo1 as $jugador) {
            Notificacion::create([
                'usuario_id' => $jugador->id,
                'tipo' => 'validacion_resultado',
                'titulo' => 'Validar Resultado del Partido',
                'mensaje' => "El árbitro ha enviado el resultado del partido {$partido->nombre}. Por favor valida el marcador.",
                'data' => ['partido_id' => $partido->id, 'resultado_id' => $resultado->id, 'equipo' => 1],
            ]);
        }

        foreach ($equipo2 as $jugador) {
            Notificacion::create([
                'usuario_id' => $jugador->id,
                'tipo' => 'validacion_resultado',
                'titulo' => 'Validar Resultado del Partido',
                'mensaje' => "El árbitro ha enviado el resultado del partido {$partido->nombre}. Por favor valida el marcador.",
                'data' => ['partido_id' => $partido->id, 'resultado_id' => $resultado->id, 'equipo' => 2],
            ]);
        }
    }

    public function historial()
    {
        $arbitro = Auth::user();

        $partidos = Partido::with('resultado')
            ->where('arbitro_id', $arbitro->id)
            ->where('estado', 'finalizado')
            ->orderBy('fecha_hora', 'desc')
            ->get();

        return view('arbitro.historial', compact('partidos'));
    }
}