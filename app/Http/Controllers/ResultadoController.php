<?php

namespace App\Http\Controllers;

use App\Models\Resultado;
use App\Models\Partido;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultadoController extends Controller
{
    public function validarResultado(Request $request, $resultadoId)
    {
        $jugador = Auth::user();
        $resultado = Resultado::findOrFail($resultadoId);
        $partido = $resultado->partido;

        // Verificar que el jugador esté inscrito en el partido
        $inscripcion = Inscripcion::where('partido_id', $partido->id)
            ->where('jugador_id', $jugador->id)
            ->first();

        if (!$inscripcion) {
            return response()->json(['message' => 'No estás inscrito en este partido'], 403);
        }

        $request->validate([
            'aceptar' => 'required|boolean',
            'notas' => 'sometimes|string|max:500',
        ]);

        $resultado->validarPorJugador(
            $jugador->id,
            $inscripcion->equipo,
            $request->aceptar,
            $request->notas
        );

        return response()->json(['message' => 'Validación registrada']);
    }

    public function show($partidoId)
    {
        $partido = Partido::with(['resultado.eventos', 'arbitro'])->findOrFail($partidoId);
        $jugador = Auth::user();

        // Verificar permisos
        $puedeValidar = Inscripcion::where('partido_id', $partidoId)
            ->where('jugador_id', $jugador->id)
            ->exists();

        return view('resultados.show', compact('partido', 'puedeValidar'));
    }

    public function apiShow($partidoId)
    {
        $partido = Partido::with(['resultado.eventos.jugador', 'arbitro'])->findOrFail($partidoId);

        return response()->json($partido);
    }
}