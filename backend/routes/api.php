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
Route::get('/userinfo/{id}',[UserController::class, 'userInfo']);

//owned relations
Route::post('/owned', [UserController::class, 'addOwned']); //adiciona a owned
Route::get('/check-owned', [UserController::class, 'checkOwnedGames']); //checa os jogos que ele tem na página atual
Route::get('/check-owned-starter', [UserController::class, 'checkOwnedGamesStarter']); //checa os jogos que ele tem na página atual no loading
Route::get('/fetch-owned', [UserController::class, 'fetchAllOwned']); //pegar todos os jogos que o user tem
Route::delete('/remove-owned', [UserController::class, 'removeOwned']); //remove o jogo de owned

//favorite relations
Route::post('/favorite', [UserController::class, 'addFavorite']);   //favorita o jogo
Route::get('/check-favorite', [UserController::class, 'checkFavoriteGames']); //checa os jogos que estão na página atual se são favoritos
Route::get('/check-favorite-starter', [UserController::class, 'checkFavoriteGamesStarter']); // checa no loading
Route::get('/fetch-favorite', [UserController::class, 'fetchAllFavorite']); //pegar todos os jogos que o user favoritou
Route::delete('/remove-favorite', [UserController::class, 'removeFavorite']); //deleta o jogo dos favoritos

//wishlist relations
Route::post('/wishlist', [UserController::class, 'addWishlist']); //adiciona a wishlist
Route::get('/check-wishlist', [UserController::class, 'checkWishlist']); //checa todos os jogos na página atual que estão na wishlist
Route::get('/check-wishlist-starter', [UserController::class, 'checkWishlistStarter']); //checa os loading
Route::get('/fetch-wished', [UserController::class, 'fetchAllWished']); //pegar todos os jogos que o user botou na wishlist
Route::delete('/remove-wishlist', [UserController::class, 'removeWishlist']); //deleta o jogo da wishlist

//check all if any update
Route::get('/check-all', [UserController::class, 'checkAllGames']); //pegar todos os jogos que o user botou na wishlist


//owned api fetch (for profile)
Route::get('/game-api-owned/{ids}', [GamesController::class, 'fetchOwned']);

//favorite api fetch (for profile)
Route::get('/game-api-favorite/{ids}', [GamesController::class, 'fetchFavorite']);

//wished api fetch (for profile)
Route::get('/game-api-wished/{ids}', [GamesController::class, 'fetchWished']);


//pegar TODOS os jogos da api QUE O USUARIO TEM
Route::get('/all-rawg-games/{ids}', [GamesController::class, 'allGamesUserTracked']);
