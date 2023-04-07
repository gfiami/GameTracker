<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/games/{page?}/{search?}', [GamesController::class, 'games']);
Route::get('/game/{id}', [GamesController::class, 'specificGameInfo']);
Route::post('/signin', [UserController::class, 'signin']);//->middleware('throttle:5,1'); //isso serve para permitir apenas 5 tentativas de login por minuto e retorna erro de muitas tentativas 429
Route::post('/register', [UserController::class, 'register']);
Route::get('/userinfo', [UserController::class, 'userInfo']);
