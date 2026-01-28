<?php

namespace App\Http\Controllers;

use App\Models\Estadistica;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstadisticaController extends Controller
{
    public function show($jugadorId = null)
    {
        $jugadorId = $jugadorId ?? Auth::id();
        $jugador = User::findOrFail($jugadorId);

        $estadistica = Estadistica::where('jugador_id', $jugadorId)->first();

        if (!$estadistica) {
            $estadistica = Estadistica::create(['jugador_id' => $jugadorId]);
        }

        // Calcular promedios de calificaciones
        $promedios = [];
        $tiposCalificaciones = [
            'comportamiento_competitivo',
            'toma_decisiones',
            'impacto_resultado',
            'capacidad_defensiva',
            'eficacia',
            'participacion_juego'
        ];

        foreach ($tiposCalificaciones as $tipo) {
            $promedios[$tipo] = $estadistica->getPromedioCalificacion($tipo);
        }

        return view('estadisticas.show', compact('jugador', 'estadistica', 'promedios'));
    }

    public function apiShow($jugadorId)
    {
        $estadistica = Estadistica::where('jugador_id', $jugadorId)->first();

        if (!$estadistica) {
            return response()->json(['message' => 'Estadísticas no encontradas'], 404);
        }

        return response()->json($estadistica);
    }
}