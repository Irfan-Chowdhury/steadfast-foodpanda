<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SSOController extends Controller
{
    // public function redirectToFoodpanda(Request $request)
    // {
    //     $user = Auth::user();

    //     $token = $user->createToken('SSO-Token')->plainTextToken;
    //     $redirectToUrl = config('app.sso_redirect_url').'/sso-foodpanda-login';
    //     $redirectBackUrl = config('app.url').'/dashboard';
    //     $response = Http::withToken($token)->post('http://127.0.0.1:8001/api/cross-login', [
    //         'email' => $user->email
    //     ]);

    //     return redirect()->away("$redirectToUrl?token={$token}&email={$user->email}&redirect_back={$redirectBackUrl}");
    // }


    public function crossLogin(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $fullToken = $request->bearerToken();
        $parts = explode('|', $fullToken);
        $plainToken = $parts[1] ?? null;
        if ($plainToken) {
            $hashedToken = hash('sha256', $plainToken);
        }

        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => get_class($user),
            'tokenable_id'   => $user->id,
            'name'           => 'SSO-Token',
            'token'          => $hashedToken,
            'abilities'      => json_encode(['*']),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    public function ssoLogout(Request $request)
    {
        $email = $request->input('email');
        $fullToken = $request->input('token');

        $parts = explode('|', $fullToken);
        $plainToken = $parts[1] ?? null;
        if ($plainToken) {
            $hashedToken = hash('sha256', $plainToken);
        }

        // return response()->json([
        //     'message' => 'Logout request received',
        //     'email' => $email,
        //     'token' => $hashedToken,
        // ]);

        $user = User::where('email', $email)->first();
        if ($user && $hashedToken) {
            // // Delete matching token
            $user->tokens()->where('token', $hashedToken)->delete();
        }

        return response()->json(['message' => 'Token deleted successfully'], 200);
    }
}
