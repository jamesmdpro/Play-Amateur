<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        \Log::info('CheckRole middleware', [
            'authenticated' => $request->user() !== null,
            'user_id' => $request->user()?->id,
            'user_rol' => $request->user()?->rol,
            'required_role' => $role,
            'session_id' => $request->session()->getId()
        ]);

        if (!$request->user()) {
            \Log::warning('CheckRole: No authenticated user, redirecting to login');
            return redirect()->route('login');
        }

        if ($request->user()->rol !== $role) {
            \Log::warning('CheckRole: Role mismatch', [
                'user_rol' => $request->user()->rol,
                'required_role' => $role
            ]);
            return redirect()->route('login')->withErrors(['error' => 'No tienes permiso para acceder a esta página.']);
        }

        \Log::info('CheckRole: Access granted');
        return $next($request);
    }
}
