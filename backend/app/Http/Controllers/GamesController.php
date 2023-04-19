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
    /* jeito antigo, mudei para cache e tirei cert checker
        //$certPath = storage_path('rawg_io.pem');
        //$curl = curl_init();
        //curl_setopt($curl, CURLOPT_CAINFO, $certPath);
         $response = Http::withOptions([
            'curl' => [
                CURLOPT_CAINFO => $certPath,
            ],
            'verify' => false

            //limitações da api de no máximo 40 jogos por página
        ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY') ."&ids={$ids}");
        $games = $response->json();
        return compact('games'); */
    public function allGamesUserTracked(Request $request){
        $ownedArray = $request->input('owned_games');
        $favoriteArray = $request->input('favorite_games');
        $wishedArray = $request->input('wished_games');

         //vamos mandar de volta apenas 10 de cada jogo;
         $ownedArray = array_slice($ownedArray, 0, 10);
         $favoriteArray = array_slice($favoriteArray, 0, 10);
         $wishedArray = array_slice($wishedArray, 0, 10);

        sort($ownedArray);
        sort($favoriteArray);
        sort($wishedArray);


        $owned = implode(",", $ownedArray);
        $favorite = implode(",", $favoriteArray);
        $wished = implode(",", $wishedArray);

        $cacheKeyOwned = 'user_games_owned' . implode('_', $ownedArray);
        $cacheKeyFavorite = 'user_games_favorite' . implode('_', $favoriteArray);
        $cacheKeyWished = 'user_games_wished' . implode('_', $wishedArray);

        $ownedGames = Cache::get($cacheKeyOwned);
        $favoriteGames = Cache::get($cacheKeyFavorite);
        $wishedGames = Cache::get($cacheKeyWished);

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
        Log::info($ownedGames);
        Log::info($favoriteGames);

        return compact('ownedGames', 'favoriteGames', 'wishedGames');
    }

    public function games($page = 1, $search = null){
        $search = $search !== null ? $search : '';
        //implementando cache para reduzir requisições a api (e também não ter ataque cardíaco dependendo dela)

        $cacheKey = 'games_page_' . $page . '_search_' . $search;
        $games = Cache::get($cacheKey);

        if (!$games) {
            Log::info("Requisição feita a API Rawg");
            $response = Http::withOptions([
                'verify' => false
            ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY')."&page=".$page."&page_size=24&search=".$search);
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
    public function specificGameInfo($gameId){
       /*
        jeito sem cache e que antes usava tbm certpath, agora vou usar cache e tirar esse certpath q tava dando ruim
       //$certPath = storage_path('rawg_io.pem');
       // $curl = curl_init();
        //curl_setopt($curl, CURLOPT_CAINFO, $certPath);
        $response = Http::withOptions([
            'curl' => [
                CURLOPT_CAINFO => $certPath,
            ],
            'verify' => false

        ])->get("https://api.rawg.io/api/games/".$gameId."?key=".env('RAWG_API_KEY'));
        $game = $response->json();
        return compact('game'); */
        //implementando cache para reduzir requisições a api (e também não ter ataque cardíaco dependendo dela)

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
    //fetch asll specific tracker Interações com jogos específicos na specific game page
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
