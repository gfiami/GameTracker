<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OwnedGame;
use App\Models\FavoritedGame;
use App\Models\WishlistGame;
use Illuminate\Support\Facades\Cache;



//isso serve para usar logs no console com Log::info($teste);
use Illuminate\Support\Facades\Log;
class GamesController extends Controller
{
    //pega o id de todos os jogos que o usuario tem para cada categoria
    public function getIdsGamesTracked(Request $request){
        try{
        $user_id = $request->input('user_id');
        $owned_games = OwnedGame::where('user_id', $user_id)
        ->pluck('game_api_id')
        ->toArray();
        $favorite_games = FavoritedGame::where('user_id', $user_id)
        ->pluck('game_api_id')
        ->toArray();
        $wishlist_games = WishlistGame::where('user_id', $user_id)
        ->pluck('game_api_id')
        ->toArray();
        return response()->json([
            'owned' => $owned_games,
            'favorite' => $favorite_games,
            'wished' => $wishlist_games,
        ]);

        } catch (\Exception $e) {
                return response()->json(['Erro na requisição' => $e->getMessage()], 500);
        }
    }

    //Para cada página de own|favorite|wishlist, apenas de determinada categoria
    public function trackedGameCategory(Request $request){
        $game_ids = $request->input('game_ids');
        $page = $request->input('page');
        Log::info($page);
        if($game_ids == null){
            $games = [];
        } else{
            sort($game_ids);
            $ids = implode(",", $game_ids);
            $cacheKey = 'all_user_games_owned' . implode('_', $game_ids) . '_page_' . $page;
            $games = Cache::get($cacheKey);
            if(!$games){
                Log::info("Requisição do usuário feita a API Rawg");
                $response = Http::withOptions([
                    'verify' => false
                    ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY') ."&ids={$ids}&page_size=18&page={$page}");
                    if ($response->failed()) {
                    $exception = $response->toException();
                    Log::error("Request to RAWG API failed: " . $exception->getMessage() . "\n" . $exception->getTraceAsString());
                }
                $games = $response->json();
                //salvando no cache por 2 meses
                Cache::put($cacheKey, $games, 86400);
            }else{
                Log::info("Dados do usuário resgatados do cache");
            }
        }
        return compact('games');
    }

    //Usado no perfil para mostrar até 10 jogos de cada categoria, pegando na api de acordo com os jogos que o user rastreou
    public function allGamesUserTracked(Request $request){
        $ownedArray = $request->input('owned_games');
        $favoriteArray = $request->input('favorite_games');
        $wishedArray = $request->input('wished_games');

         //vamos mandar de volta apenas 10 de cada jogo;
         if($ownedArray !== null){
            $ownedArray = array_slice($ownedArray, 0, 10);
            sort($ownedArray);
            $owned = implode(",", $ownedArray);
            $cacheKeyOwned = 'user_games_owned' . implode('_', $ownedArray);
            $ownedGames = Cache::get($cacheKeyOwned);
            if (!$ownedGames) {
                Log::info("Requisição do usuário feita a API Rawg");
                $response = Http::withOptions([
                    'verify' => false
                ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY') ."&ids={$owned}");
                if ($response->failed()) {
                    $exception = $response->toException();
                    Log::error("Request to RAWG API failed: " . $exception->getMessage() . "\n" . $exception->getTraceAsString());
                }
                $ownedGames = $response->json();
                //salvando no cache por 2 meses
                Cache::put($cacheKeyOwned, $ownedGames, 86400);
            }else{
                Log::info("Dados do usuário resgatados do cache");
            }
         } else{
            $ownedGames = [];
         }
         if($favoriteArray !== null){
            $favoriteArray = array_slice($favoriteArray, 0, 10);
            sort($favoriteArray);
            $favorite = implode(",", $favoriteArray);
            $cacheKeyFavorite = 'user_games_favorite' . implode('_', $favoriteArray);
            $favoriteGames = Cache::get($cacheKeyFavorite);
            if (!$favoriteGames) {
                Log::info("Requisição do usuário feita a API Rawg");
                $response = Http::withOptions([
                    'verify' => false
                ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY') ."&ids={$favorite}");
                if ($response->failed()) {
                    $exception = $response->toException();
                    Log::error("Request to RAWG API failed: " . $exception->getMessage() . "\n" . $exception->getTraceAsString());
                }
                $favoriteGames = $response->json();
                //salvando no cache por 2 meses
                Cache::put($cacheKeyFavorite, $favoriteGames, 86400);
            }else{
                Log::info("Dados do usuário resgatados do cache");
            }
         } else{
            $favoriteGames = [];
         }
         if($wishedArray !== null){
            $wishedArray = array_slice($wishedArray, 0, 10);
            sort($wishedArray);
            $wished = implode(",", $wishedArray);
            $cacheKeyWished = 'user_games_wished' . implode('_', $wishedArray);
            $wishedGames = Cache::get($cacheKeyWished);

            if (!$wishedGames) {
                Log::info("Requisição do usuário feita a API Rawg");
                $response = Http::withOptions([
                    'verify' => false
                ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY') ."&ids={$wished}");
                if ($response->failed()) {
                    $exception = $response->toException();
                    Log::error("Request to RAWG API failed: " . $exception->getMessage() . "\n" . $exception->getTraceAsString());
                }
                $wishedGames = $response->json();
                //salvando no cache por 2 meses
                Cache::put($cacheKeyWished, $wishedGames, 86400);
            }else{
                Log::info("Dados do usuário resgatados do cache");
            }
         } else{
            $wishedGames = [];
         }
        return compact('ownedGames', 'favoriteGames', 'wishedGames');
    }

    //pega diversos jogos da api para a página de jogos
    public function games($page = 1, $search = null, $order = '-added'){
        //order: -name / released / added / rating
        $search = $search !== null ? $search : '';
        $cacheKey = 'games_page_' . $page . '_search_' . $search . '_order_' . $order;
        $games = Cache::get($cacheKey);

        if (!$games) {
            Log::info("Requisição feita a API Rawg");
            $response = Http::withOptions([
                'verify' => false
            ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY')."&page=".$page."&page_size=24&search=".$search ."&ordering=".$order);
            if ($response->failed()) {
                $exception = $response->toException();
                Log::error("Request to RAWG API failed: " . $exception->getMessage() . "\n" . $exception->getTraceAsString());
            }
            $games = $response->json();
            //salvando no cache por 2 meses
            Cache::put($cacheKey, $games, 86400);
        }else{
            Log::info("Dados resgatados do cache");
        }
        return compact('games');
    }

    //pega informações de determinado jogo na api para a página dele
    public function specificGameInfo($gameId){
        $cacheKey = 'games_id_' . $gameId;
        $game = Cache::get($cacheKey);

        if (!$game) {
            Log::info("Requisição específica feita a API Rawg");
            $response = Http::withOptions([
                'verify' => false
            ])->get("https://api.rawg.io/api/games/".$gameId."?key=".env('RAWG_API_KEY'));
            if ($response->failed()) {
                $exception = $response->toException();
                Log::error("Request to RAWG API failed: " . $exception->getMessage() . "\n" . $exception->getTraceAsString());
            }
            $game = $response->json();
            //salvando no cache por 2 meses
            Cache::put($cacheKey, $game, 86400);
        }else{
            Log::info("Dado do jogo específico pegos no cache");
        }
        return compact('game');
    }

    //pega dados específicos em relação ao usuario na página de determinado jogo
    public function fetchTrackedSpecific(Request $request){
        try {
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');
            $owned_game = OwnedGame::where('user_id', $user_id)
            ->where('game_api_id', $game_api_id)
            ->first();
            $favorited_game = FavoritedGame::where('user_id', $user_id)
            ->where('game_api_id', $game_api_id)
            ->first();
            $wishlisted_game = WishlistGame::where('user_id', $user_id)
            ->where('game_api_id', $game_api_id)
            ->first();


            $response = [
                'owned_game' => $owned_game,
                'favorited_game' => $favorited_game,
                'wishlisted_game' => $wishlisted_game,
            ];
            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json(['Erro na requisição' => $e->getMessage()], 500);
        }
    }
}
