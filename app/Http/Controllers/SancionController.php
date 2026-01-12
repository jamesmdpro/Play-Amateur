<?php

namespace App\Http\Controllers;

use App\Models\Sancion;
use Illuminate\Http\Request;

class SancionController extends Controller
{
    public function misSanciones(Request $request)
    {
        $user = $request->user();
        $sanciones = $user->sanciones()
            ->with('partido')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($sanciones);
    }

    public function pagarReactivacion(Request $request, $sancionId)
    {
        $user = $request->user();
        $sancion = Sancion::findOrFail($sancionId);

        if ($sancion->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($sancion->pagada) {
            return response()->json(['message' => 'Esta sanción ya fue pagada'], 400);
        }

        if (!$user->tieneSaldo($sancion->monto_reactivacion)) {
            return response()->json([
                'message' => 'Saldo insuficiente',
                'saldo_actual' => $user->wallet,
                'monto_requerido' => $sancion->monto_reactivacion,
                'faltante' => $sancion->monto_reactivacion - $user->wallet,
            ], 400);
        }

        $user->descontarSaldo(
            $sancion->monto_reactivacion,
            'sancion',
            $sancion->partido_id,
            'Pago de reactivación por sanción'
        );

        $sancion->update([
            'pagada' => true,
            'activa' => false,
        ]);

        $user->notificaciones()->create([
            'tipo' => 'sancion_pagada',
            'titulo' => 'Sanción Pagada',
            'mensaje' => 'Has pagado tu sanción y tu cuenta ha sido reactivada.',
            'data' => ['sancion_id' => $sancion->id],
        ]);

        return response()->json([
            'message' => 'Sanción pagada exitosamente. Tu cuenta ha sido reactivada.',
            'sancion' => $sancion->fresh(),
            'saldo_restante' => $user->fresh()->wallet,
        ]);
    }

    public function listadoSanciones(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $sanciones = Sancion::with(['user', 'partido'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($sanciones);
    }
}
