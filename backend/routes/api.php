<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OwnedGameController;
use App\Http\Controllers\FavoritedGameController;
use App\Http\Controllers\WishlistGameController;
use App\Http\Controllers\FriendRequestController;


//interações voltadas para definições do usuário login|register|logout
Route::post('/signin', [UserController::class, 'signin']);//->middleware('throttle:5,1'); //isso serve para permitir apenas 5 tentativas de login por minuto e retorna erro de muitas tentativas 429
Route::post('/register', [UserController::class, 'register']);
Route::delete('/logout', [UserController::class, 'logout']); // AUTH
Route::put('/edit-username', [UserController::class, 'editUsername']); // AUTH
Route::post('/edit-image', [UserController::class, 'editImage']); // AUTH
//pega informações do usuário que podem ser usadas em diversas páginas
Route::get('/userinfo/{id}',[UserController::class, 'userInfo']);
//pega todos os usuarios para a community view
Route::get('/users', [UserController::class, 'getUsers']);

//friendlist
Route::post('/add-friend', [FriendRequestController::class, 'addFriend']);

//games da api na página de games
Route::get('/games/{page?}/{search?}/{order?}', [GamesController::class, 'games']);

//review relations add|edit|remove
Route::post('/add-review', [ReviewController::class, 'addReview']); // AUTH
Route::put('/edit-review',[ReviewController::class, 'editReview']); //AUTH
//pega as reviews na página específica de determinado jogo
Route::get('/fetch-game-reviews', [ReviewController::class, 'fetchReviews']);
//pega todas as reviews de determinado usuário
Route::get('/fetch-user-reviews', [ReviewController::class, 'fetchUserReviews' ]);
Route::delete('/delete-review', [ReviewController::class, 'deleteReview']); // AUTH

//owned relations add/check/remove
Route::post('/owned', [OwnedGameController::class, 'addOwned']); // AUTH
//checa os jogos que ele possui na página atual
Route::get('/check-owned-starter', [OwnedGameController::class, 'checkOwnedGamesStarter']);
Route::delete('/remove-owned', [OwnedGameController::class, 'removeOwned']); //remove o jogo de owned - AUTH

//favorite relations add/check/remove
Route::post('/favorite', [FavoritedGameController::class, 'addFavorite']);   //AUTH
// checa os jogos que ele favoritou na página atual
Route::get('/check-favorite-starter', [FavoritedGameController::class, 'checkFavoriteGamesStarter']);
Route::delete('/remove-favorite', [FavoritedGameController::class, 'removeFavorite']); //AUTH

//wishlist relations add/check/remove
Route::post('/wishlist', [WishlistGameController::class, 'addWishlist']); // AUTH
Route::get('/check-wishlist-starter', [WishlistGameController::class, 'checkWishlistStarter']);
Route::delete('/remove-wishlist', [WishlistGameController::class, 'removeWishlist']); //AUTH


//IDS dos jogos que o user tem em cada categoria
Route::get('/game-ids-user-tracked', [GamesController::class, 'getIdsGamesTracked']);
//obtem da api 10 jogos de cada categoria que o usuario tem
Route::get('/all-tracked-games', [GamesController::class, 'allGamesUserTracked']);
//Todos os jogos de determinada categoria, dividido em páginas de 18 jogos;
Route::get('/category-tracked-games', [GamesController::class, 'trackedGameCategory']);

/* Página específica do jogo */
//Checa o status do jogo na página dele em relação as categorias de tracking
Route::get('/specific-tracked-game', [GamesController::class, 'fetchTrackedSpecific']);
//Pega informações específicas na api para determinado jogo
Route::get('/game/{id}', [GamesController::class, 'specificGameInfo']);
//Interação de track na página do jogo (owned/favorte/wished) add|remove
Route::post('/specific-owned', [OwnedGameController::class, 'addSpecificOwned']); //AUTH
Route::delete('/remove-specific-owned', [OwnedGameController::class, 'removeSpecificOwned']); // AUTH
Route::post('/specific-favorite', [FavoritedGameController::class, 'addSpecificFavorite']);   // AUTH
Route::delete('/remove-specific-favorite', [FavoritedGameController::class, 'removeSpecificFavorite']); // AUTH
Route::post('/specific-wishlist', [WishlistGameController::class, 'addSpecificWishlist']); //AUTH
Route::delete('/remove-specific-wishlist', [WishlistGameController::class, 'removeSpecificWishlist']); //AUTH
