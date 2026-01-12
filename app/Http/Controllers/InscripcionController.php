<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use App\Models\Partido;
use App\Models\Sancion;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InscripcionController extends Controller
{
    public function inscribirse(Request $request, $partidoId)
    {
        $request->validate([
            'equipo' => 'required|in:A,B',
        ]);

        $user = $request->user();
        $partido = Partido::findOrFail($partidoId);

        if ($user->tieneSancionActiva()) {
            $sancion = $user->sancionActiva();
            return response()->json([
                'message' => 'Tienes una sanción activa hasta ' . $sancion->fecha_fin->format('d/m/Y'),
                'sancion' => $sancion,
            ], 403);
        }

        $inscripcionExistente = Inscripcion::where('partido_id', $partidoId)
            ->where('jugador_id', $user->id)
            ->first();

        if ($inscripcionExistente) {
            return response()->json(['message' => 'Ya estás inscrito en este partido'], 400);
        }

        $equipo = $request->equipo;
        $jugadoresEquipo = Inscripcion::where('partido_id', $partidoId)
            ->where('equipo', $equipo)
            ->where('es_suplente', false)
            ->count();

        $esSuplente = $jugadoresEquipo >= ($partido->jugadores_por_equipo ?? 7);

        $inscripcion = Inscripcion::create([
            'partido_id' => $partidoId,
            'jugador_id' => $user->id,
            'equipo' => $equipo,
            'es_suplente' => $esSuplente,
            'estado' => 'inscrito',
        ]);

        return response()->json([
            'message' => $esSuplente ? 'Te has inscrito como suplente' : 'Te has inscrito exitosamente',
            'inscripcion' => $inscripcion,
        ], 201);
    }

    public function confirmarPago(Request $request, $inscripcionId)
    {
        $user = $request->user();
        $inscripcion = Inscripcion::findOrFail($inscripcionId);

        if ($inscripcion->jugador_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($inscripcion->estado === 'confirmado') {
            return response()->json(['message' => 'Ya has confirmado tu participación'], 400);
        }

        $partido = $inscripcion->partido;
        $costo = $partido->costo_por_jugador ?? 20000;

        if (!$user->tieneSaldo($costo)) {
            return response()->json([
                'message' => 'Saldo insuficiente. Tu saldo actual es: $' . number_format($user->wallet, 0),
                'saldo_actual' => $user->wallet,
                'costo_partido' => $costo,
                'faltante' => $costo - $user->wallet,
            ], 400);
        }

        $user->descontarSaldo($costo, 'pago_partido', $partido->id, 'Pago confirmación partido');

        $inscripcion->update([
            'estado' => 'confirmado',
            'pago_realizado' => true,
            'confirmado_en' => now(),
        ]);

        $user->notificaciones()->create([
            'tipo' => 'confirmacion_partido',
            'titulo' => 'Confirmación Exitosa',
            'mensaje' => "Has confirmado tu participación en el partido del " . $partido->fecha->format('d/m/Y'),
            'data' => ['partido_id' => $partido->id, 'inscripcion_id' => $inscripcion->id],
        ]);

        return response()->json([
            'message' => 'Pago confirmado exitosamente',
            'inscripcion' => $inscripcion->fresh(),
            'saldo_restante' => $user->fresh()->wallet,
        ]);
    }

    public function cancelarInscripcion(Request $request, $inscripcionId)
    {
        $user = $request->user();
        $inscripcion = Inscripcion::findOrFail($inscripcionId);

        if ($inscripcion->jugador_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $partido = $inscripcion->partido;

        if ($inscripcion->estado === 'confirmado' && $inscripcion->pago_realizado) {
            $numeroSancion = $user->sanciones()->count() + 1;
            $diasSuspension = Sancion::calcularDiasSuspension($numeroSancion);

            $sancion = Sancion::create([
                'user_id' => $user->id,
                'partido_id' => $partido->id,
                'numero_sancion' => $numeroSancion,
                'dias_suspension' => $diasSuspension,
                'fecha_inicio' => now(),
                'fecha_fin' => now()->addDays($diasSuspension),
                'monto_reactivacion' => 15000,
                'motivo' => 'Cancelación después de confirmar pago',
            ]);

            $user->notificaciones()->create([
                'tipo' => 'sancion',
                'titulo' => 'Sanción Aplicada',
                'mensaje' => "Has sido sancionado por {$diasSuspension} días por cancelar después de confirmar. Debes pagar $15,000 para reactivar tu cuenta.",
                'data' => ['sancion_id' => $sancion->id],
            ]);

            $this->asignarSuplente($inscripcion);
        }

        $inscripcion->update(['estado' => 'cancelado']);

        return response()->json([
            'message' => 'Inscripción cancelada',
            'sancion' => $sancion ?? null,
        ]);
    }

    private function asignarSuplente(Inscripcion $inscripcionCancelada)
    {
        $suplente = Inscripcion::where('partido_id', $inscripcionCancelada->partido_id)
            ->where('equipo', $inscripcionCancelada->equipo)
            ->where('es_suplente', true)
            ->where('estado', 'inscrito')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($suplente) {
            $suplente->update([
                'es_suplente' => false,
            ]);

            $suplente->jugador->notificaciones()->create([
                'tipo' => 'asignacion_suplente',
                'titulo' => 'Asignado como Titular',
                'mensaje' => 'Has sido asignado como titular en el partido. Confirma tu pago para asegurar tu cupo.',
                'data' => ['partido_id' => $inscripcionCancelada->partido_id, 'inscripcion_id' => $suplente->id],
            ]);

            return $suplente;
        }

        return null;
    }

    public function misInscripciones(Request $request)
    {
        $user = $request->user();
        $inscripciones = $user->inscripciones()
            ->with('partido')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($inscripciones);
    }
}
