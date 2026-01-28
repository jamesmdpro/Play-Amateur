<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Partido;
use App\Models\Inscripcion;
use App\Models\Estadistica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'calificado_id' => 'required|integer|exists:users,id',
            'partido_id' => 'required|integer|exists:partidos,id',
            'tipo' => 'required|in:jugador_jugador,arbitro_jugador,jugador_arbitro',
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'sometimes|string|max:500',
        ]);

        $calificador = Auth::user();
        $partido = Partido::findOrFail($request->partido_id);

        // Verificar permisos
        if ($request->tipo === 'jugador_arbitro') {
            // Solo jugadores del partido pueden calificar al árbitro
            $esJugadorDelPartido = Inscripcion::where('partido_id', $request->partido_id)
                ->where('jugador_id', $calificador->id)
                ->exists();
            if (!$esJugadorDelPartido) {
                return response()->json(['message' => 'No puedes calificar a este árbitro'], 403);
            }
        } elseif ($request->tipo === 'arbitro_jugador') {
            // Solo el árbitro del partido puede calificar
            if ($partido->arbitro_id !== $calificador->id) {
                return response()->json(['message' => 'No eres el árbitro de este partido'], 403);
            }
        } elseif ($request->tipo === 'jugador_jugador') {
            // Solo jugadores del partido pueden calificar
            // Cada jugador puede calificar a dos jugadores por partido
            $esJugadorDelPartido = Inscripcion::where('partido_id', $request->partido_id)
                ->where('jugador_id', $calificador->id)
                ->exists();
            if (!$esJugadorDelPartido) {
                return response()->json(['message' => 'No puedes calificar en este partido'], 403);
            }

            // Verificar límite de 2 calificaciones por jugador
            $calificacionesDadas = Rating::where('partido_id', $request->partido_id)
                ->where('calificador_id', $calificador->id)
                ->where('tipo', 'jugador_jugador')
                ->count();

            if ($calificacionesDadas >= 2) {
                return response()->json(['message' => 'Ya has alcanzado el límite de calificaciones para este partido (máximo 2)'], 403);
            }
        }

        $rating = Rating::updateOrCreate(
            [
                'calificador_id' => $calificador->id,
                'calificado_id' => $request->calificado_id,
                'partido_id' => $request->partido_id,
                'tipo' => $request->tipo,
            ],
            [
                'puntuacion' => $request->puntuacion,
                'comentario' => $request->comentario,
            ]
        );

        // Actualizar estadísticas si es calificación a jugador
        if (in_array($request->tipo, ['jugador_jugador', 'arbitro_jugador'])) {
            $estadistica = Estadistica::firstOrCreate(['jugador_id' => $request->calificado_id]);
            $tipoCalif = $this->mapTipoCalificacion($request->tipo);
            $estadistica->agregarCalificacion($tipoCalif, $request->puntuacion);
        }

        return response()->json(['message' => 'Calificación guardada', 'rating' => $rating]);
    }

    public function show($partidoId)
    {
        $partido = Partido::with(['jugadores', 'arbitro'])->findOrFail($partidoId);
        $jugador = Auth::user();

        // Verificar si puede calificar
        $puedeCalificar = Inscripcion::where('partido_id', $partidoId)
            ->where('jugador_id', $jugador->id)
            ->exists() || $partido->arbitro_id === $jugador->id;

        $ratings = Rating::where('partido_id', $partidoId)->get();

        return view('ratings.show', compact('partido', 'ratings', 'puedeCalificar'));
    }

    private function mapTipoCalificacion($tipo)
    {
        // Mapear tipos de rating a tipos de calificación en estadísticas
        $map = [
            'jugador_jugador' => 'comportamiento_competitivo',
            'arbitro_jugador' => 'eficacia',
        ];

        return $map[$tipo] ?? 'participacion_juego';
    }
}