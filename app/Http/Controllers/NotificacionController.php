<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notificaciones = $user->notificaciones()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notificaciones);
    }

    public function noLeidas(Request $request)
    {
        $user = $request->user();
        $notificaciones = $user->notificaciones()
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'count' => $notificaciones->count(),
            'notificaciones' => $notificaciones,
        ]);
    }

    public function marcarComoLeida(Request $request, $id)
    {
        $user = $request->user();
        $notificacion = Notificacion::findOrFail($id);

        if ($notificacion->user_id !== $user->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $notificacion->marcarComoLeida();

        return response()->json(['message' => 'Notificación marcada como leída']);
    }

    public function marcarTodasComoLeidas(Request $request)
    {
        $user = $request->user();
        $user->notificaciones()->where('leida', false)->update(['leida' => true]);

        return response()->json(['message' => 'Todas las notificaciones marcadas como leídas']);
    }
}
