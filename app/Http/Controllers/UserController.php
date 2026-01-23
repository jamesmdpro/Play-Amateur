<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'telefono' => 'sometimes|string|max:20',
            'fecha_nacimiento' => 'sometimes|date',
            'direccion' => 'sometimes|string|max:255',
            'documento' => 'sometimes|string|max:50',
            'posicion' => 'sometimes|in:arquero,defensa,medio,ataque',
            'nivel' => 'sometimes|integer|min:1|max:10',
            'ciudad' => 'sometimes|string|max:255',
        ], [
            'name.string' => 'El nombre debe ser un texto válido',
            'name.max' => 'El nombre no puede exceder 255 caracteres',
            'email.email' => 'El correo electrónico debe ser válido',
            'email.unique' => 'Este correo electrónico ya está en uso',
            'telefono.string' => 'El teléfono debe ser un texto válido',
            'telefono.max' => 'El teléfono no puede exceder 20 caracteres',
            'fecha_nacimiento.date' => 'La fecha de nacimiento debe ser una fecha válida (formato: YYYY-MM-DD)',
            'direccion.string' => 'La dirección debe ser un texto válido',
            'direccion.max' => 'La dirección no puede exceder 255 caracteres',
            'documento.string' => 'El documento debe ser un texto válido',
            'documento.max' => 'El documento no puede exceder 50 caracteres',
            'posicion.in' => 'La posición debe ser: arquero, defensa, medio o ataque',
            'nivel.integer' => 'El nivel debe ser un número entero',
            'nivel.min' => 'El nivel debe ser al menos 1',
            'nivel.max' => 'El nivel no puede ser mayor a 10',
            'ciudad.string' => 'La ciudad debe ser un texto válido',
            'ciudad.max' => 'La ciudad no puede exceder 255 caracteres',
        ]);

        $user->update($request->only([
            'name', 'email', 'telefono', 'fecha_nacimiento',
            'direccion', 'documento', 'posicion', 'nivel', 'ciudad'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado exitosamente',
            'user' => $user,
        ]);
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'foto.required' => 'Debe seleccionar una foto',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'La foto debe ser de tipo: jpeg, png, jpg o gif',
            'foto.max' => 'La foto no puede exceder 2MB',
        ]);

        $user = $request->user();

        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }

        $path = $request->file('foto')->store('fotos', 'public');

        $user->update(['foto' => Storage::url($path)]);

        return response()->json([
            'success' => true,
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

    public function estadisticasJugador(Request $request)
    {
        $user = $request->user();

        $partidosJugados = \DB::table('inscripciones')
            ->join('partidos', 'inscripciones.partido_id', '=', 'partidos.id')
            ->where('inscripciones.jugador_id', $user->id)
            ->where('partidos.estado', 'finalizado')
            ->count();

        $partidosDisponibles = \DB::table('partidos')
            ->where('estado', 'abierto')
            ->whereDate('fecha_hora', '>=', now())
            ->count();

        $tarjetasAmarillas = \DB::table('sanciones')
            ->where('user_id', $user->id)
            ->where('tipo', 'tarjeta_amarilla')
            ->count();

        $tarjetasRojas = \DB::table('sanciones')
            ->where('user_id', $user->id)
            ->where('tipo', 'tarjeta_roja')
            ->count();

        $tarjetasNaranjas = \DB::table('sanciones')
            ->where('user_id', $user->id)
            ->where('tipo', 'tarjeta_naranja')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'partidos_jugados' => $partidosJugados,
                'partidos_disponibles' => $partidosDisponibles,
                'tarjetas_amarillas' => $tarjetasAmarillas,
                'tarjetas_rojas' => $tarjetasRojas,
                'tarjetas_naranjas' => $tarjetasNaranjas,
            ]
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'La contraseña actual es requerida',
            'password.required' => 'La nueva contraseña es requerida',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        $user = $request->user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta'
            ], 400);
        }

        $user->update([
            'password' => \Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }
}
