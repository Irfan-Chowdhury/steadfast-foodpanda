<?php

use App\Http\Controllers\SSOController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\DB;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/cross-login', [SSOController::class, 'crossLogin']);
Route::post('/sso-logout', [SSOController::class, 'ssoLogout']);

