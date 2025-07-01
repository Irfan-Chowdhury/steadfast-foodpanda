<?php

require __DIR__.'/auth.php';


use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


Route::get('/check', function () {
    return 'ok';
});

Route::get('/', function () {
    return Auth::check()
        ? redirect()->back()   // if logged in
        : redirect()->route('login');      // if guest
});

Route::middleware('sso.auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.pages.dashboard');
    })->middleware(['auth'])->name('dashboard');
});
