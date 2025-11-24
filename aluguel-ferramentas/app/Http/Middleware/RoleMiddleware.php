<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        // Admin tem acesso total
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Se o usuário não tiver um dos papéis permitidos
        if (!in_array($user->role, $roles)) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
