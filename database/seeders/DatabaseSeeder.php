<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PartidoSeeder::class,
            EstadisticaSeeder::class,
            ResultadoSeeder::class,
            EventoPartidoSeeder::class,
            RatingSeeder::class,
        ]);
    }
}
