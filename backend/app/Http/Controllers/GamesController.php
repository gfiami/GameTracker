<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OwnedGame;
use App\Models\FavoritedGame;
use App\Models\WishlistGame;



//isso serve para usar logs no console com Log::info($teste);
use Illuminate\Support\Facades\Log;
class GamesController extends Controller
{
    public function index()
    {
       // $certPath = storage_path('rawg_io.pem');
       // $curl = curl_init();
        //curl_setopt($curl, CURLOPT_CAINFO, $certPath);
        $response = Http::withOptions([
            'verify' => false

        ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY')."&search=god&page_size=5");
        $games = $response->json();

        return view('games.index', compact('games'));
    }
    public function allGamesUserTracked($ids){
        //$certPath = storage_path('rawg_io.pem');
        //$curl = curl_init();
        //curl_setopt($curl, CURLOPT_CAINFO, $certPath);
         $response = Http::withOptions([
            /*'curl' => [
                CURLOPT_CAINFO => $certPath,
            ],*/
            'verify' => false

            //limitações da api de no máximo 40 jogos por página
        ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY') ."&ids={$ids}");
        $games = $response->json();
        return compact('games');
    }
    public function games($page = 1, $search = null){
        $search = $search !== null ? $search : '';
        //$certPath = storage_path('rawg_io.pem');
        //$curl = curl_init();
        //curl_setopt($curl, CURLOPT_CAINFO, $certPath);
         $response = Http::withOptions([
                'verify' => false

        ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY')."&page=".$page."&page_size=24&search=".$search);
        if ($response->failed()) {
            $exception = $response->toException();
            Log::error("Request to RAWG API failed: " . $exception->getMessage() . "\n" . $exception->getTraceAsString());
        }
        $games = $response->json();
        return compact('games');
    }
    public function specificGameInfo($gameId){
        //$certPath = storage_path('rawg_io.pem');
       // $curl = curl_init();
        //curl_setopt($curl, CURLOPT_CAINFO, $certPath);
        $response = Http::withOptions([
            /*'curl' => [
                CURLOPT_CAINFO => $certPath,
            ],*/
            'verify' => false

        ])->get("https://api.rawg.io/api/games/".$gameId."?key=".env('RAWG_API_KEY'));
        $game = $response->json();
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
