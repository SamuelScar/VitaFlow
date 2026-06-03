<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante que apenas usuários com perfil `doador` acessem a rota. Usuários não-doador (ex: admins) recebem resposta 403.
 */
class EnsureUserIsDoador
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isDoador()) {
            abort(403);
        }

        return $next($request);
    }
}
