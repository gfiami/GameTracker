<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OwnedGameController;
use App\Http\Controllers\FavoritedGameController;
use App\Http\Controllers\WishlistGameController;



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
Route::post('/signin', [UserController::class, 'signin']);//->middleware('throttle:5,1'); //isso serve para permitir apenas 5 tentativas de login por minuto e retorna erro de muitas tentativas 429
Route::post('/register', [UserController::class, 'register']);
Route::delete('/logout', [UserController::class, 'logout']);
Route::get('/userinfo/{id}',[UserController::class, 'userInfo']);

//games da api
Route::get('/games/{page?}/{search?}', [GamesController::class, 'games']);
Route::get('/game/{id}', [GamesController::class, 'specificGameInfo']);

//review
Route::post('/add-review', [ReviewController::class, 'addReview']);
Route::put('/edit-review',[ReviewController::class, 'editReview']);
Route::get('/fetch-game-reviews', [ReviewController::class, 'fetchReviews']);

//owned relations
Route::post('/owned', [OwnedGameController::class, 'addOwned']); //adiciona a owned - AUTH
Route::get('/check-owned', [OwnedGameController::class, 'checkOwnedGames']); //checa os jogos que ele tem na página atual
Route::get('/check-owned-starter', [OwnedGameController::class, 'checkOwnedGamesStarter']); //checa os jogos que ele tem na página atual no loading
Route::get('/fetch-owned', [OwnedGameController::class, 'fetchAllOwned']); //pegar todos os jogos que o user tem
Route::delete('/remove-owned', [OwnedGameController::class, 'removeOwned']); //remove o jogo de owned - AUTH

//favorite relations
Route::post('/favorite', [FavoritedGameController::class, 'addFavorite']);   //favorita o jogo - AUTH
Route::get('/check-favorite', [FavoritedGameController::class, 'checkFavoriteGames']); //checa os jogos que estão na página atual se são favoritos
Route::get('/check-favorite-starter', [FavoritedGameController::class, 'checkFavoriteGamesStarter']); // checa no loading
Route::get('/fetch-favorite', [FavoritedGameController::class, 'fetchAllFavorite']); //pegar todos os jogos que o user favoritou
Route::delete('/remove-favorite', [FavoritedGameController::class, 'removeFavorite']); //deleta o jogo dos favoritos - AUTH

//wishlist relations
Route::post('/wishlist', [WishlistGameController::class, 'addWishlist']); //adiciona a wishlist -AUTH
Route::get('/check-wishlist', [WishlistGameController::class, 'checkWishlist']); //checa todos os jogos na página atual que estão na wishlist
Route::get('/check-wishlist-starter', [WishlistGameController::class, 'checkWishlistStarter']); //checa os loading
Route::get('/fetch-wished', [WishlistGameController::class, 'fetchAllWished']); //pegar todos os jogos que o user botou na wishlist
Route::delete('/remove-wishlist', [WishlistGameController::class, 'removeWishlist']); //deleta o jogo da wishlist - AUTH

//check all if any update

//pegar TODOS os jogos da api QUE O USUARIO TEM
Route::get('/all-tracked-games/{ids}', [GamesController::class, 'allGamesUserTracked']);

//start of specific page relations
Route::get('/specific-tracked-game', [GamesController::class, 'fetchTrackedSpecific']); //adiciona a owned

Route::post('/specific-owned', [OwnedGameController::class, 'addSpecificOwned']); //adiciona a owned - AUTH
Route::delete('/remove-specific-owned', [OwnedGameController::class, 'removeSpecificOwned']); //remove o jogo de owned - AUTH

Route::post('/specific-favorite', [FavoritedGameController::class, 'addSpecificFavorite']);   //favorita o jogo - AUTH
Route::delete('/remove-specific-favorite', [FavoritedGameController::class, 'removeSpecificFavorite']); //deleta o jogo dos favoritos - AUTH

Route::post('/specific-wishlist', [WishlistGameController::class, 'addSpecificWishlist']); //adiciona a wishlist
Route::delete('/remove-specific-wishlist', [WishlistGameController::class, 'removeSpecificWishlist']); //deleta o jogo da wishlist
//end of specific page relations
