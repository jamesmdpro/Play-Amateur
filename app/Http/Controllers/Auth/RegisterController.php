<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'rol' => ['required', 'in:jugador,cancha,arbitro,admin'],
            'ciudad' => ['required', 'string', 'max:255'],
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        if ($request->rol === 'jugador' || $request->rol === 'arbitro') {
            $rules['genero'] = ['required', 'in:masculino,femenino'];
        }

        if ($request->rol === 'jugador') {
            $rules['posicion'] = ['required', 'in:portero,defensa,mediocampista,delantero'];
            $rules['nivel'] = ['required', 'in:principiante,intermedio,avanzado'];
        }

        $validated = $request->validate($rules);

        $fotoPath = $request->file('foto')->store('fotos', 'public');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rol' => $validated['rol'],
            'genero' => $validated['genero'] ?? null,
            'posicion' => $validated['posicion'] ?? null,
            'nivel' => $validated['nivel'] ?? null,
            'ciudad' => $validated['ciudad'],
            'foto' => $fotoPath,
            'wallet' => 0.00,
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }
}