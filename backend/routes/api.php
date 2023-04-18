<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OwnedGameController;
use App\Http\Controllers\FavoritedGameController;
use App\Http\Controllers\WishlistGameController;

Route::post('/signin', [UserController::class, 'signin']);//->middleware('throttle:5,1'); //isso serve para permitir apenas 5 tentativas de login por minuto e retorna erro de muitas tentativas 429
Route::post('/register', [UserController::class, 'register']);
Route::delete('/logout', [UserController::class, 'logout']); //AUTH
Route::get('/userinfo/{id}',[UserController::class, 'userInfo']);

//games da api
Route::get('/games/{page?}/{search?}', [GamesController::class, 'games']);
Route::get('/game/{id}', [GamesController::class, 'specificGameInfo']);

//review
Route::post('/add-review', [ReviewController::class, 'addReview']); // AUTH
Route::put('/edit-review',[ReviewController::class, 'editReview']); //AUTH
Route::get('/fetch-game-reviews', [ReviewController::class, 'fetchReviews']);
Route::delete('/delete-review', [ReviewController::class, 'deleteReview']); // AUTH

//owned relations
Route::post('/owned', [OwnedGameController::class, 'addOwned']); // AUTH
Route::get('/check-owned', [OwnedGameController::class, 'checkOwnedGames']); //checa os jogos que ele tem na página atual
Route::get('/check-owned-starter', [OwnedGameController::class, 'checkOwnedGamesStarter']); //checa os jogos que ele tem na página atual no loading
Route::get('/fetch-owned', [OwnedGameController::class, 'fetchAllOwned']); //pegar todos os jogos que o user tem (voltado pro perfil - array)
Route::delete('/remove-owned', [OwnedGameController::class, 'removeOwned']); //remove o jogo de owned - AUTH

//favorite relations
Route::post('/favorite', [FavoritedGameController::class, 'addFavorite']);   //AUTH
Route::get('/check-favorite', [FavoritedGameController::class, 'checkFavoriteGames']); //checa os jogos que estão na página atual se são favoritos
Route::get('/check-favorite-starter', [FavoritedGameController::class, 'checkFavoriteGamesStarter']); // checa no loading
Route::get('/fetch-favorite', [FavoritedGameController::class, 'fetchAllFavorite']); //pegar todos os jogos que o user favoritou
Route::delete('/remove-favorite', [FavoritedGameController::class, 'removeFavorite']); //AUTH

//wishlist relations
Route::post('/wishlist', [WishlistGameController::class, 'addWishlist']); // AUTH
Route::get('/check-wishlist', [WishlistGameController::class, 'checkWishlist']); //checa todos os jogos na página atual que estão na wishlist
Route::get('/check-wishlist-starter', [WishlistGameController::class, 'checkWishlistStarter']); //checa os loading
Route::get('/fetch-wished', [WishlistGameController::class, 'fetchAllWished']); //pegar todos os jogos que o user botou na wishlist
Route::delete('/remove-wishlist', [WishlistGameController::class, 'removeWishlist']); //AUTH

//pegar TODOS os jogos da api QUE O USUARIO TEM
Route::get('/all-tracked-games/{ids}', [GamesController::class, 'allGamesUserTracked']);

//start of specific page relations
Route::get('/specific-tracked-game', [GamesController::class, 'fetchTrackedSpecific']); //adiciona a owned

Route::post('/specific-owned', [OwnedGameController::class, 'addSpecificOwned']); //AUTH
Route::delete('/remove-specific-owned', [OwnedGameController::class, 'removeSpecificOwned']); // AUTH

Route::post('/specific-favorite', [FavoritedGameController::class, 'addSpecificFavorite']);   // AUTH
Route::delete('/remove-specific-favorite', [FavoritedGameController::class, 'removeSpecificFavorite']); // AUTH

Route::post('/specific-wishlist', [WishlistGameController::class, 'addSpecificWishlist']); //AUTH
Route::delete('/remove-specific-wishlist', [WishlistGameController::class, 'removeSpecificWishlist']); //AUTH
//end of specific page relations
