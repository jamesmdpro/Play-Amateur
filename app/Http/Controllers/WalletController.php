<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $transacciones = $user->transacciones()
            ->with(['partido', 'aprobador'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'saldo' => $user->wallet,
            'transacciones' => $transacciones,
        ]);
    }

    public function solicitarRecarga(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:10000',
            'comprobante' => 'required|image|max:5120',
        ]);

        $user = $request->user();

        $path = $request->file('comprobante')->store('comprobantes', 'public');

        $transaccion = WalletTransaction::create([
            'user_id' => $user->id,
            'tipo' => 'recarga',
            'monto' => $request->monto,
            'saldo_anterior' => $user->wallet,
            'saldo_nuevo' => $user->wallet,
            'comprobante' => $path,
            'estado' => 'pendiente',
        ]);

        return response()->json([
            'message' => 'Solicitud de recarga enviada. Espera la aprobación del administrador.',
            'transaccion' => $transaccion,
        ], 201);
    }

    public function aprobarRecarga(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $transaccion = WalletTransaction::findOrFail($id);

        if ($transaccion->estado !== 'pendiente') {
            return response()->json(['message' => 'Esta transacción ya fue procesada'], 400);
        }

        $jugador = $transaccion->user;
        $saldoAnterior = $jugador->wallet;
        $jugador->wallet += $transaccion->monto;
        $jugador->save();

        $transaccion->update([
            'estado' => 'aprobado',
            'saldo_nuevo' => $jugador->wallet,
            'aprobado_por' => $user->id,
            'aprobado_en' => now(),
        ]);

        $jugador->notificaciones()->create([
            'tipo' => 'recarga_aprobada',
            'titulo' => 'Recarga Aprobada',
            'mensaje' => "Tu recarga de $" . number_format($transaccion->monto, 0) . " ha sido aprobada.",
            'data' => ['transaccion_id' => $transaccion->id],
        ]);

        return response()->json([
            'message' => 'Recarga aprobada exitosamente',
            'transaccion' => $transaccion->fresh(),
        ]);
    }

    public function rechazarRecarga(Request $request, $id)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'notas' => 'required|string',
        ]);

        $transaccion = WalletTransaction::findOrFail($id);

        if ($transaccion->estado !== 'pendiente') {
            return response()->json(['message' => 'Esta transacción ya fue procesada'], 400);
        }

        $transaccion->update([
            'estado' => 'rechazado',
            'aprobado_por' => $user->id,
            'aprobado_en' => now(),
            'notas' => $request->notas,
        ]);

        $transaccion->user->notificaciones()->create([
            'tipo' => 'recarga_rechazada',
            'titulo' => 'Recarga Rechazada',
            'mensaje' => "Tu recarga de $" . number_format($transaccion->monto, 0) . " fue rechazada. Motivo: " . $request->notas,
            'data' => ['transaccion_id' => $transaccion->id],
        ]);

        return response()->json([
            'message' => 'Recarga rechazada',
            'transaccion' => $transaccion,
        ]);
    }

    public function recargasPendientes(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $recargas = WalletTransaction::where('tipo', 'recarga')
            ->where('estado', 'pendiente')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($recargas);
    }
}
