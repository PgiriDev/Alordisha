<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

class AuthSession
{
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('user_id')) {
            $rememberCookie = $request->cookie('remember_login');

            if (is_string($rememberCookie) && str_contains($rememberCookie, '|')) {
                [$rememberUserId, $rememberToken] = explode('|', $rememberCookie, 2);
                $rememberedUser = User::find($rememberUserId);

                if (
                    $rememberedUser
                    && $rememberedUser->status === 'active'
                    && is_string($rememberedUser->remember_token)
                    && hash_equals($rememberedUser->remember_token, (string) $rememberToken)
                ) {
                    $request->session()->put([
                        'user_id' => $rememberedUser->id,
                        'role' => $rememberedUser->role,
                        'currentUser' => $rememberedUser,
                    ]);
                }
            }
        }

        if (!$request->session()->has('user_id')) {
            return redirect()->route('login');
        }
        // Load user and share globally
        $user = User::find($request->session()->get('user_id'));
        if (!$user) {
            $request->session()->forget('user_id');
            return redirect()->route('login');
        }
        // attach current user to request
        app()->instance('currentUser', $user);
        view()->share('currentUser', $user);
        return $next($request);
    }
}
