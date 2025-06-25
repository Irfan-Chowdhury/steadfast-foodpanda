<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class CheckSSOToken
{
    public function handle(Request $request, Closure $next): Response
    {

        $ssoTokenCookie = $request->cookie('sso_token');
        $ssoEmail  = $request->cookie('sso_email');

        if (!$ssoTokenCookie && Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }


        if (!Auth::check()) {
            $ssoTokenCookie = $request->cookie('sso_token');
            $ssoEmail  = $request->cookie('sso_email');

            if ($ssoTokenCookie && $ssoEmail) {
                $parts = explode('|', $ssoTokenCookie);
                $plainToken = $parts[1] ?? null;
                if ($plainToken) {
                    $hashedToken = hash('sha256', $plainToken);
                }

                $user = User::where('email', $ssoEmail)->first();

                if ($user) {
                    $exists = PersonalAccessToken::where('tokenable_id', $user->id)
                        ->where('token', $hashedToken)
                        ->exists();

                    if ($exists) {
                        Auth::login($user);
                    }
                }
            }
        }

        return $next($request);
    }
}
