<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JugadorController extends Controller
{
    public function dashboard()
    {
        return view('jugador.dashboard');
    }

    public function perfil()
    {
        return view('jugador.perfil');
    }
}
