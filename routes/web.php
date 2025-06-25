<?php

require __DIR__.'/auth.php';


use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth','sso.auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/sso-foodpanda-login', function (Request $request) {

    $token = $request->query('token');
    $email = $request->query('email');
    $redirect_back = $request->query('redirect_back');

    // return response()->json([
    //     'token' => $token,
    //     'email' => $email,
    //     'redirect_back' => $redirect_back,
    // ]);

    // Validate input
    if (!$token || !$email) {
        return redirect('/login')->withErrors(['SSO failed: missing data.']);
    }

    $user = User::where('email', $email)->first();

    if (!$user) {
        return redirect('/login')->withErrors(['User not found in foodpanda.']);
    }

    // $fullToken = $request->bearerToken();
    $parts = explode('|', $token);
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


    Auth::login($user);

    return redirect()->away("$redirect_back");
});
