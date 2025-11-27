<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'posicion' => 'sometimes|in:arquero,defensa,medio,ataque',
            'nivel' => 'sometimes|integer|min:1|max:10',
            'ciudad' => 'sometimes|string|max:255',
        ]);

        $user->update($request->only(['name', 'posicion', 'nivel', 'ciudad']));

        return response()->json([
            'message' => 'Perfil actualizado exitosamente',
            'user' => $user,
        ]);
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        $path = $request->file('foto')->store('fotos', 'public');

        $user->update(['foto' => $path]);

        return response()->json([
            'message' => 'Foto subida exitosamente',
            'foto_url' => Storage::url($path),
            'user' => $user,
        ]);
    }

    public function updateWallet(Request $request)
    {
        $user = $request->user();

        if (!$user->isAdmin() && $user->id != $request->user_id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'monto' => 'required|numeric',
            'operacion' => 'required|in:agregar,restar,establecer',
        ]);

        $targetUser = User::findOrFail($request->user_id);

        switch ($request->operacion) {
            case 'agregar':
                $targetUser->wallet += $request->monto;
                break;
            case 'restar':
                $targetUser->wallet -= $request->monto;
                break;
            case 'establecer':
                $targetUser->wallet = $request->monto;
                break;
        }

        $targetUser->save();

        return response()->json([
            'message' => 'Wallet actualizado exitosamente',
            'user' => $targetUser,
        ]);
    }
}
