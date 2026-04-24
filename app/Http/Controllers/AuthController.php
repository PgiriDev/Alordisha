<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use App\Mail\LoginAlertMail;

class AuthController extends Controller
{
    public function showLogin(Request $request)
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

        // If already logged in → redirect by role
        if (session()->has('user_id')) {

            $user = User::find(session('user_id'));

            if ($user) {
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('teacher.dashboard');
            }
        }

        return view('auth.login');
    }

    public function login(Request $r)
    {
        $r->validate([
            'login' => 'required', // phone or email
            'password' => 'required',
            'remember' => 'nullable|boolean',
        ]);

        $login = $r->input('login');
        $password = $r->input('password');

        // Find user by phone or email
        $user = User::where('phone', $login)
            ->orWhere('email', $login)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()
                ->withErrors(['login' => 'Invalid phone/email or password'])
                ->withInput();
        }

        if ($user->status !== 'active') {
            return back()
                ->withErrors(['login' => 'Your account is inactive'])
                ->withInput();
        }

        $this->establishUserSession($r, $user, $r->boolean('remember'));
        $this->queueLoginAlert($user, $r);

        return $this->redirectByRole($user);
    }

    public function redirectToGoogle(Request $request)
    {
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect()->route('login')
                ->withErrors(['login' => 'Google login is not configured yet. Please contact admin.']);
        }

        return $this->googleProvider($request)->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = $this->googleProvider($request)->user();
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('login')
                ->withErrors(['login' => 'Google login failed. Please try again.']);
        }

        $email = strtolower((string) $googleUser->getEmail());

        if ($email === '') {
            return redirect()->route('login')
                ->withErrors(['login' => 'Google account email was not found. Use your password login.']);
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['login' => 'This Google account is not authorized. Login is allowed for existing users only.']);
        }

        if ($user->status !== 'active') {
            return redirect()->route('login')
                ->withErrors(['login' => 'Your account is inactive']);
        }

        $this->establishUserSession($request, $user, false);
        $this->queueLoginAlert($user, $request);

        return $this->redirectByRole($user);
    }

    public function logout(Request $r)
    {
        $userId = $r->session()->get('user_id');

        if ($userId) {
            User::whereKey($userId)->update(['remember_token' => null]);
        }

        Cookie::queue(Cookie::forget('remember_login'));

        // Clear entire session
        $r->session()->flush();

        // Redirect to welcome page
        return redirect('/');   // <- FIXED (was login)
    }

    private function establishUserSession(Request $request, User $user, bool $remember): void
    {
        $request->session()->put([
            'user_id' => $user->id,
            'role' => $user->role,
            'currentUser' => $user,
        ]);

        if ($remember) {
            $rememberToken = Str::random(64);
            $user->remember_token = $rememberToken;
            $user->save();

            Cookie::queue('remember_login', $user->id . '|' . $rememberToken, 60 * 24 * 30);
            return;
        }

        if (!empty($user->remember_token)) {
            $user->remember_token = null;
            $user->save();
        }

        Cookie::queue(Cookie::forget('remember_login'));
    }

    private function queueLoginAlert(User $user, Request $request): void
    {
        if (empty($user->email)) {
            return;
        }

        $loginMeta = [
            'login_at' => now()->format('d M Y, h:i A'),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ];

        $userId = (int) $user->id;
        $email = (string) $user->email;

        dispatch(function () use ($userId, $email, $loginMeta) {
            try {
                $freshUser = User::find($userId);

                if (!$freshUser || $email === '') {
                    return;
                }

                Mail::to($email)->send(new LoginAlertMail($freshUser, $loginMeta));
            } catch (\Throwable $e) {
                report($e);
            }
        })->afterResponse();
    }

    private function redirectByRole(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('teacher.dashboard');
    }

    private function resolveGoogleCallbackUrl(Request $request): string
    {
        $configured = trim((string) config('services.google.redirect'));

        if ($configured !== '') {
            return $configured;
        }

        return $request->getSchemeAndHttpHost() . route('auth.google.callback', [], false);
    }

    private function googleProvider(Request $request): GoogleProvider
    {
        return Socialite::buildProvider(GoogleProvider::class, [
            'client_id' => (string) config('services.google.client_id'),
            'client_secret' => (string) config('services.google.client_secret'),
            'redirect' => $this->resolveGoogleCallbackUrl($request),
        ]);
    }
}
