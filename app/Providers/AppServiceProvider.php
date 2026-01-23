<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Inscripcion;
use App\Models\Partido;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Evento para descontar de wallet al inscribirse
        Inscripcion::created(function ($inscripcion) {
            $user = $inscripcion->user;
            $partido = $inscripcion->partido;
            $costo = $partido->costo_por_jugador;

            // Validar si tiene saldo suficiente
            if ($user->wallet < $costo) {
                // Eliminar la inscripción si no tiene saldo
                $inscripcion->delete();
                throw new \Exception('Saldo insuficiente en la wallet');
            }

            // Realizar el descuento
            DB::transaction(function () use ($user, $costo, $inscripcion, $partido) {
                // Crear transacción de pago
                WalletTransaction::create([
                    'user_id' => $user->id,
                    'tipo' => 'pago_partido',
                    'monto' => -$costo, // Negativo porque es un descuento
                    'saldo_anterior' => $user->wallet,
                    'saldo_nuevo' => $user->wallet - $costo,
                    'estado' => 'aprobado',
                    'partido_id' => $partido->id,
                    'notas' => 'Pago por inscripción al partido #' . $partido->id,
                ]);

                // Actualizar saldo del usuario
                $user->wallet -= $costo;
                $user->save();
            });
        });
    }
}