<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

class PartidoController extends Controller
{
    public function index()
    {
        $partidos = Partido::with(['creador', 'inscripciones.jugador'])
            ->orderBy('fecha_hora', 'desc')
            ->get()
            ->map(function ($partido) {
                return [
                    'id' => $partido->id,
                    'nombre' => $partido->nombre,
                    'descripcion' => $partido->descripcion,
                    'fecha_hora' => $partido->fecha_hora,
                    'ubicacion' => $partido->ubicacion,
                    'cupos_totales' => $partido->cupos_totales,
                    'cupos_suplentes' => $partido->cupos_suplentes,
                    'cupos_disponibles' => $partido->cuposDisponibles(),
                    'cupos_suplentes_disponibles' => $partido->cuposSuplentesDisponibles(),
                    'costo' => $partido->costo,
                    'estado' => $partido->estado,
                    'creador' => $partido->creador,
                    'inscritos' => $partido->inscripciones->count(),
                ];
            });

        return response()->json($partidos);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin() && !$user->isCancha()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'sometimes|string',
            'fecha_hora' => 'required|date|after:now',
            'ubicacion' => 'required|string|max:255',
            'cupos_totales' => 'required|integer|min:2',
            'cupos_suplentes' => 'sometimes|integer|min:0',
            'costo' => 'sometimes|numeric|min:0',
        ]);

        $partido = Partido::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_hora' => $request->fecha_hora,
            'ubicacion' => $request->ubicacion,
            'cupos_totales' => $request->cupos_totales,
            'cupos_suplentes' => $request->cupos_suplentes ?? 0,
            'costo' => $request->costo ?? 0,
            'creador_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Partido creado exitosamente',
            'partido' => $partido,
        ], 201);
    }

    public function show($id)
    {
        $partido = Partido::with([
            'creador',
            'inscripciones.jugador',
            'jugadoresTitulares',
            'jugadoresSuplentes'
        ])->findOrFail($id);

        $equipo1 = $partido->inscripciones->where('equipo', 1)->map(function ($inscripcion) {
            return [
                'id' => $inscripcion->jugador->id,
                'name' => $inscripcion->jugador->name,
                'posicion' => $inscripcion->jugador->posicion,
                'nivel' => $inscripcion->jugador->nivel,
            ];
        });

        $equipo2 = $partido->inscripciones->where('equipo', 2)->map(function ($inscripcion) {
            return [
                'id' => $inscripcion->jugador->id,
                'name' => $inscripcion->jugador->name,
                'posicion' => $inscripcion->jugador->posicion,
                'nivel' => $inscripcion->jugador->nivel,
            ];
        });

        return response()->json([
            'partido' => $partido,
            'cupos_disponibles' => $partido->cuposDisponibles(),
            'cupos_suplentes_disponibles' => $partido->cuposSuplentesDisponibles(),
            'equipo1' => $equipo1,
            'equipo2' => $equipo2,
            'suplentes' => $partido->jugadoresSuplentes,
        ]);
    }

    public function inscribirse(Request $request, $id)
    {
        $partido = Partido::findOrFail($id);
        $user = $request->user();

        if (!$user->isJugador()) {
            return response()->json(['message' => 'Solo los jugadores pueden inscribirse'], 403);
        }

        if ($partido->estado !== 'abierto') {
            return response()->json(['message' => 'El partido no está abierto para inscripciones'], 400);
        }

        $yaInscrito = Inscripcion::where('partido_id', $partido->id)
            ->where('jugador_id', $user->id)
            ->exists();

        if ($yaInscrito) {
            return response()->json(['message' => 'Ya estás inscrito en este partido'], 400);
        }

        $cuposDisponibles = $partido->cuposDisponibles();
        $cuposSuplentesDisponibles = $partido->cuposSuplentesDisponibles();

        $esSuplente = false;

        if ($cuposDisponibles <= 0) {
            if ($cuposSuplentesDisponibles <= 0) {
                return response()->json(['message' => 'No hay cupos disponibles'], 400);
            }
            $esSuplente = true;
        }

        $inscripcion = Inscripcion::create([
            'partido_id' => $partido->id,
            'jugador_id' => $user->id,
            'es_suplente' => $esSuplente,
        ]);

        return response()->json([
            'message' => $esSuplente ? 'Inscrito como suplente exitosamente' : 'Inscrito exitosamente',
            'inscripcion' => $inscripcion,
        ], 201);
    }

    public function generarEquipos($id)
    {
        $partido = Partido::findOrFail($id);
        $user = request()->user();

        if (!$user->isAdmin() && !$user->isCancha() && $partido->creador_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $resultado = $partido->generarEquipos();

        if (!$resultado) {
            return response()->json(['message' => 'No hay suficientes jugadores para generar equipos'], 400);
        }

        return response()->json([
            'message' => 'Equipos generados exitosamente',
            'partido' => $partido->load('inscripciones.jugador'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $partido = Partido::findOrFail($id);
        $user = $request->user();

        if (!$user->isAdmin() && !$user->isCancha() && $partido->creador_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'fecha_hora' => 'sometimes|date',
            'ubicacion' => 'sometimes|string|max:255',
            'cupos_totales' => 'sometimes|integer|min:2',
            'cupos_suplentes' => 'sometimes|integer|min:0',
            'costo' => 'sometimes|numeric|min:0',
            'estado' => 'sometimes|in:abierto,cerrado,en_curso,finalizado',
        ]);

        $partido->update($request->all());

        return response()->json([
            'message' => 'Partido actualizado exitosamente',
            'partido' => $partido,
        ]);
    }

    public function destroy($id)
    {
        $partido = Partido::findOrFail($id);
        $user = request()->user();

        if (!$user->isAdmin() && $partido->creador_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $partido->delete();

        return response()->json([
            'message' => 'Partido eliminado exitosamente',
        ]);
    }
}
