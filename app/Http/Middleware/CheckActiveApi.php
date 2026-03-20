<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveApi
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->est_actif) {
            // Supprimer le token actuel pour forcer la deconnexion immediate
            $request->user()->currentAccessToken()->delete();
            
            return response()->json([
                'message' => 'Votre compte est suspendu. Vous avez été déconnecté.',
                'status' => 'inactive'
            ], 403);
        }

        return $next($request);
    }
}
