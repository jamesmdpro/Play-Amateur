<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use App\Models\Inscripcion;
use App\Models\Notificacion;
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

        if (!$user->isAdmin() && !$user->isCancha() && !$user->isJugador() && !$user->isArbitro()) {
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
            'con_arbitro' => 'sometimes|boolean',
        ]);

        $partido = Partido::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_hora' => $request->fecha_hora,
            'ubicacion' => $request->ubicacion,
            'cupos_totales' => $request->cupos_totales,
            'cupos_suplentes' => $request->cupos_suplentes ?? 0,
            'costo' => $request->costo ?? 0,
            'con_arbitro' => $request->con_arbitro ?? false,
            'creador_id' => $user->id,
        ]);

        $partido->calcularCostoPorJugador();

        Notificacion::notificarNuevoPartido($partido);

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
        $request->validate([
            'equipo' => 'required|integer|in:1,2',
        ]);

        $partido = Partido::findOrFail($id);
        $user = $request->user();

        if (!$user->isJugador()) {
            return response()->json(['success' => false, 'message' => 'Solo los jugadores pueden inscribirse'], 403);
        }

        if ($partido->estado !== 'abierto') {
            return response()->json(['success' => false, 'message' => 'El partido no está abierto para inscripciones'], 400);
        }

        if ($user->tieneSancionActiva()) {
            return response()->json(['success' => false, 'message' => 'Tienes una sanción activa'], 403);
        }

        $yaInscrito = Inscripcion::where('partido_id', $partido->id)
            ->where('jugador_id', $user->id)
            ->exists();

        if ($yaInscrito) {
            return response()->json(['success' => false, 'message' => 'Ya estás inscrito en este partido'], 400);
        }

        if ($user->wallet < $partido->costo_por_jugador) {
            return response()->json(['success' => false, 'message' => 'Saldo insuficiente'], 400);
        }

        $cuposDisponibles = $partido->cuposDisponibles();
        $cuposSuplentesDisponibles = $partido->cuposSuplentesDisponibles();

        $cuposPorEquipo = $partido->cupos_totales / 2;
        $inscritosEnEquipo = Inscripcion::where('partido_id', $partido->id)
            ->where('equipo', $request->equipo)
            ->where('es_suplente', false)
            ->count();

        $esSuplente = false;

        if ($inscritosEnEquipo >= $cuposPorEquipo) {
            if ($cuposSuplentesDisponibles <= 0) {
                return response()->json(['success' => false, 'message' => 'No hay cupos disponibles'], 400);
            }
            $esSuplente = true;
        }

        if (!$esSuplente && $cuposDisponibles <= 0) {
            if ($cuposSuplentesDisponibles <= 0) {
                return response()->json(['success' => false, 'message' => 'No hay cupos disponibles'], 400);
            }
            $esSuplente = true;
        }

        $saldo_anterior = $user->wallet;
        $user->wallet -= $partido->costo_por_jugador;
        $user->save();

        \App\Models\WalletTransaction::create([
            'user_id' => $user->id,
            'tipo' => 'pago_partido',
            'monto' => $partido->costo_por_jugador,
            'saldo_anterior' => $saldo_anterior,
            'saldo_nuevo' => $user->wallet,
            'partido_id' => $partido->id,
            'estado' => 'aprobado',
            'notas' => 'Inscripción al partido: ' . $partido->nombre,
        ]);

        $inscripcion = Inscripcion::create([
            'partido_id' => $partido->id,
            'jugador_id' => $user->id,
            'equipo' => $request->equipo,
            'es_suplente' => $esSuplente,
            'estado' => 'inscrito',
        ]);

        return response()->json([
            'success' => true,
            'message' => $esSuplente ? 'Inscrito como suplente exitosamente' : 'Inscrito exitosamente',
            'inscripcion' => $inscripcion,
            'saldo_restante' => $user->wallet,
        ], 201);
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

    public function disponibles()
    {
        $partidos = Partido::where('estado', 'abierto')
            ->whereDate('fecha_hora', '>=', now())
            ->with(['creador'])
            ->orderBy('fecha_hora', 'asc')
            ->get()
            ->map(function ($partido) {
                return [
                    'id' => $partido->id,
                    'nombre' => $partido->nombre,
                    'fecha' => $partido->fecha_hora->format('d/m/Y H:i'),
                    'cancha' => $partido->ubicacion,
                    'costo' => $partido->costo,
                    'cupos_disponibles' => $partido->cuposDisponibles(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $partidos
        ]);
    }

    public function storeFromJugador(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string',
            'fecha' => 'required|date|after:today',
            'hora' => 'required',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'jugadores_por_equipo' => 'required|integer|min:5',
            'max_jugadores' => 'required|integer|min:10',
            'costo' => 'required|numeric|min:0',
            'estado' => 'nullable|string',
            'nivel' => 'nullable|string',
        ]);

        $fechaHora = $request->fecha . ' ' . $request->hora;

        $partido = Partido::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'fecha_hora' => $fechaHora,
            'ubicacion' => $request->ubicacion . ', ' . $request->ciudad,
            'cupos_totales' => $request->max_jugadores,
            'cupos_suplentes' => 0,
            'costo' => $request->costo,
            'creador_id' => $user->id,
            'estado' => $request->estado ?? 'abierto',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Partido creado exitosamente',
            'partido' => $partido,
        ], 201);
    }

    public function edit($id)
    {
        $partido = Partido::findOrFail($id);
        $user = request()->user();

        if ($partido->creador_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'No autorizado');
        }

        return view('jugador.editar-partido', compact('partido'));
    }

    public function partidosCreados()
    {
        $user = request()->user();

        $partidos = Partido::where('creador_id', $user->id)
            ->where('estado', '!=', 'finalizado')
            ->where('estado', '!=', 'en_curso')
            ->with(['inscripciones'])
            ->orderBy('fecha_hora', 'desc')
            ->get()
            ->map(function ($partido) use ($user) {
                return [
                    'id' => $partido->id,
                    'nombre' => $partido->nombre,
                    'fecha_formateada' => $partido->fecha_hora->format('d/m/Y'),
                    'hora' => $partido->fecha_hora->format('H:i'),
                    'ubicacion' => $partido->ubicacion,
                    'costo' => number_format($partido->costo, 0),
                    'max_jugadores' => $partido->cupos_totales,
                    'inscritos' => $partido->inscripciones->count(),
                    'estado' => $partido->estado,
                    'estado_texto' => ucfirst($partido->estado),
                    'puede_editar' => $partido->creador_id === $user->id,
                ];
            });

        return response()->json([
            'success' => true,
            'partidos' => $partidos
        ]);
    }

    public function partidosEnMarcha()
    {
        $user = request()->user();

        $partidos = Partido::where('creador_id', $user->id)
            ->where('estado', 'en_curso')
            ->with(['inscripciones'])
            ->orderBy('fecha_hora', 'desc')
            ->get()
            ->map(function ($partido) use ($user) {
                return [
                    'id' => $partido->id,
                    'nombre' => $partido->nombre,
                    'fecha_formateada' => $partido->fecha_hora->format('d/m/Y'),
                    'hora' => $partido->fecha_hora->format('H:i'),
                    'ubicacion' => $partido->ubicacion,
                    'costo' => number_format($partido->costo, 0),
                    'max_jugadores' => $partido->cupos_totales,
                    'inscritos' => $partido->inscripciones->count(),
                    'estado' => $partido->estado,
                    'estado_texto' => 'En Marcha',
                    'puede_editar' => false,
                ];
            });

        return response()->json([
            'success' => true,
            'partidos' => $partidos
        ]);
    }

    public function partidosFinalizados()
    {
        $user = request()->user();

        $partidos = Partido::where('creador_id', $user->id)
            ->where('estado', 'finalizado')
            ->with(['inscripciones'])
            ->orderBy('fecha_hora', 'desc')
            ->get()
            ->map(function ($partido) use ($user) {
                return [
                    'id' => $partido->id,
                    'nombre' => $partido->nombre,
                    'fecha_formateada' => $partido->fecha_hora->format('d/m/Y'),
                    'hora' => $partido->fecha_hora->format('H:i'),
                    'ubicacion' => $partido->ubicacion,
                    'costo' => number_format($partido->costo, 0),
                    'max_jugadores' => $partido->cupos_totales,
                    'inscritos' => $partido->inscripciones->count(),
                    'estado' => $partido->estado,
                    'estado_texto' => 'Finalizado',
                    'puede_editar' => false,
                ];
            });

        return response()->json([
            'success' => true,
            'partidos' => $partidos
        ]);
    }

    public function cancelarPartido($id)
    {
        $partido = Partido::findOrFail($id);
        $user = request()->user();

        if ($partido->creador_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        $partido->update(['estado' => 'cancelado']);

        return response()->json([
            'success' => true,
            'message' => 'Partido cancelado exitosamente'
        ]);
    }

    public function partidosDisponibles()
    {
        $partidos = Partido::where('estado', 'abierto')
            ->where('fecha_hora', '>', now())
            ->with(['creador', 'inscripciones'])
            ->orderBy('fecha_hora', 'asc')
            ->get()
            ->map(function ($partido) {
                return [
                    'id' => $partido->id,
                    'nombre' => $partido->nombre,
                    'descripcion' => $partido->descripcion,
                    'fecha_hora' => $partido->fecha_hora,
                    'ubicacion' => $partido->ubicacion,
                    'cupos_totales' => $partido->cupos_totales,
                    'cupos_disponibles' => $partido->cuposDisponibles(),
                    'costo_por_jugador' => $partido->costo_por_jugador,
                    'con_arbitro' => $partido->con_arbitro,
                    'estado' => $partido->estado,
                ];
            });

        return response()->json($partidos);
    }

    public function partidosRequierenArbitro()
    {
        $partidos = Partido::where('con_arbitro', true)
            ->where('estado', 'abierto')
            ->where('fecha_hora', '>', now())
            ->whereNull('arbitro_id')
            ->with(['creador', 'inscripciones'])
            ->orderBy('fecha_hora', 'asc')
            ->get()
            ->map(function ($partido) {
                return [
                    'id' => $partido->id,
                    'nombre' => $partido->nombre,
                    'descripcion' => $partido->descripcion,
                    'fecha_hora' => $partido->fecha_hora,
                    'ubicacion' => $partido->ubicacion,
                    'cupos_totales' => $partido->cupos_totales,
                ];
            });

        return response()->json($partidos);
    }

    public function aplicarArbitro(Request $request, $id)
    {
        $partido = Partido::findOrFail($id);
        $user = $request->user();

        if (!$user->isArbitro()) {
            return response()->json(['message' => 'Solo los árbitros pueden aplicar'], 403);
        }

        if (!$partido->con_arbitro) {
            return response()->json(['message' => 'Este partido no requiere árbitro'], 400);
        }

        if ($partido->arbitro_id) {
            return response()->json(['message' => 'Este partido ya tiene un árbitro asignado'], 400);
        }

        $partido->update(['arbitro_id' => $user->id]);

        return response()->json([
            'message' => 'Aplicación exitosa',
            'partido' => $partido,
        ]);
    }

    public function misPartidos()
    {
        $user = request()->user();

        $partidos = Partido::where('creador_id', $user->id)
            ->with(['inscripciones.jugador', 'arbitro'])
            ->orderBy('fecha_hora', 'desc')
            ->get()
            ->map(function ($partido) {
                $equipo1 = $partido->inscripciones->where('equipo', 1)->map(function ($inscripcion) {
                    return [
                        'id' => $inscripcion->jugador->id,
                        'name' => $inscripcion->jugador->name,
                        'posicion' => $inscripcion->jugador->posicion,
                    ];
                })->values();

                $equipo2 = $partido->inscripciones->where('equipo', 2)->map(function ($inscripcion) {
                    return [
                        'id' => $inscripcion->jugador->id,
                        'name' => $inscripcion->jugador->name,
                        'posicion' => $inscripcion->jugador->posicion,
                    ];
                })->values();

                $suplentes = $partido->inscripciones->where('es_suplente', true)->map(function ($inscripcion) {
                    return [
                        'id' => $inscripcion->jugador->id,
                        'name' => $inscripcion->jugador->name,
                        'posicion' => $inscripcion->jugador->posicion,
                    ];
                })->values();

                return [
                    'id' => $partido->id,
                    'nombre' => $partido->nombre,
                    'descripcion' => $partido->descripcion,
                    'fecha_hora' => $partido->fecha_hora,
                    'ubicacion' => $partido->ubicacion,
                    'cupos_totales' => $partido->cupos_totales,
                    'cupos_disponibles' => $partido->cuposDisponibles(),
                    'inscritos_count' => $partido->inscripciones->count(),
                    'con_arbitro' => $partido->con_arbitro,
                    'arbitro' => $partido->arbitro,
                    'equipos_generados' => $equipo1->count() > 0 || $equipo2->count() > 0,
                    'equipo1' => $equipo1,
                    'equipo2' => $equipo2,
                    'suplentes' => $suplentes,
                ];
            });

        return response()->json($partidos);
    }

    public function generarEquipos($id)
    {
        $partido = Partido::findOrFail($id);
        $user = request()->user();

        if ($partido->creador_id !== $user->id && !$user->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($partido->generarEquipos()) {
            return response()->json([
                'message' => 'Equipos generados exitosamente',
                'partido' => $partido->load(['inscripciones.jugador']),
            ]);
        }

        return response()->json(['message' => 'No hay suficientes jugadores para generar equipos'], 400);
    }
}
