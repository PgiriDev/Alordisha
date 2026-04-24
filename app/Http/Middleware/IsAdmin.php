<?php

namespace App\Http\Middleware;

use Closure;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        $user = app('currentUser');
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
