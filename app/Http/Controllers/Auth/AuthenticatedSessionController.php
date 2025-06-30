<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }


    public function destroy(Request $request): RedirectResponse
    {
        try {
            self::tokenDestroyFromOtherSite($request);
        } catch (Exception $e) {
            Log::info(["Error: " => $e->getMessage()]);
        }

        //Clear the SSO cookies
        Cookie::queue(Cookie::forget('sso_token'));
        Cookie::queue(Cookie::forget('sso_email'));

        // Clear session
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();


        return redirect('/login');
    }

    private function tokenDestroyFromOtherSite($request): void
    {
        $user = Auth::user();

        $parts = explode('|', $request->cookie('sso_token'));
        $plainToken = $parts[1] ?? null;
        $hashedToken = null;
        if ($plainToken) {
            $hashedToken = hash('sha256', $plainToken);
        }

        $user->tokens()->where('token', $hashedToken)->delete();

        $redirectToUrl = config('app.sso_redirect_url');

        Http::post("$redirectToUrl/api/sso-logout", [
            'email' => $request->user()->email,
            'token' => $request->cookie('sso_token'),
        ]);
    }
}
