<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'rol' => 'admin',
            'posicion' => null,
            'nivel' => 10,
            'ciudad' => 'Buenos Aires',
            'wallet' => 1000.00,
        ]);

        User::create([
            'name' => 'Cancha Central',
            'email' => 'cancha@test.com',
            'password' => Hash::make('password'),
            'rol' => 'cancha',
            'posicion' => null,
            'nivel' => 1,
            'ciudad' => 'Buenos Aires',
            'wallet' => 0.00,
        ]);

        User::create([
            'name' => 'Árbitro Juan',
            'email' => 'arbitro@test.com',
            'password' => Hash::make('password'),
            'rol' => 'arbitro',
            'posicion' => null,
            'nivel' => 8,
            'ciudad' => 'Buenos Aires',
            'wallet' => 500.00,
        ]);

        $jugadores = [
            ['name' => 'Carlos Pérez', 'posicion' => 'arquero', 'nivel' => 7],
            ['name' => 'Juan García', 'posicion' => 'defensa', 'nivel' => 6],
            ['name' => 'Pedro López', 'posicion' => 'defensa', 'nivel' => 7],
            ['name' => 'Luis Martínez', 'posicion' => 'defensa', 'nivel' => 5],
            ['name' => 'Diego Rodríguez', 'posicion' => 'medio', 'nivel' => 8],
            ['name' => 'Fernando González', 'posicion' => 'medio', 'nivel' => 7],
            ['name' => 'Roberto Sánchez', 'posicion' => 'medio', 'nivel' => 6],
            ['name' => 'Miguel Torres', 'posicion' => 'ataque', 'nivel' => 9],
            ['name' => 'Andrés Ramírez', 'posicion' => 'ataque', 'nivel' => 8],
            ['name' => 'Jorge Flores', 'posicion' => 'ataque', 'nivel' => 7],
            ['name' => 'Martín Silva', 'posicion' => 'arquero', 'nivel' => 6],
            ['name' => 'Pablo Castro', 'posicion' => 'defensa', 'nivel' => 8],
            ['name' => 'Sebastián Morales', 'posicion' => 'medio', 'nivel' => 7],
            ['name' => 'Javier Ortiz', 'posicion' => 'ataque', 'nivel' => 8],
            ['name' => 'Ricardo Vargas', 'posicion' => 'defensa', 'nivel' => 6],
        ];

        foreach ($jugadores as $index => $jugador) {
            User::create([
                'name' => $jugador['name'],
                'email' => 'jugador' . ($index + 1) . '@test.com',
                'password' => Hash::make('password'),
                'rol' => 'jugador',
                'posicion' => $jugador['posicion'],
                'nivel' => $jugador['nivel'],
                'ciudad' => 'Buenos Aires',
                'wallet' => rand(100, 500),
            ]);
        }
    }
}
