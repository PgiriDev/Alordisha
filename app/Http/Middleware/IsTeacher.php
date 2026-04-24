<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class IsTeacher
{
    public function handle($request, Closure $next)
    {
        // Check if logged in
        if (!session()->has('user_id')) {
            return redirect('/login');
        }

        // Fetch user
        $user = User::find(session('user_id'));

        // Validate user and role
        if (!$user || $user->role !== 'teacher') {
            return redirect('/login');
        }

        return $next($request);
    }
}