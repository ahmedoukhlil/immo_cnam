<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanAccessTickets
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->canAccessTickets()) {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé à la gestion des tickets.');
        }

        return $next($request);
    }
}
