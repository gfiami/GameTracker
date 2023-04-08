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
        $certPath = storage_path('rawg_io.pem');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CAINFO, $certPath);
        $response = Http::withOptions([
            'curl' => [
                CURLOPT_CAINFO => $certPath,
            ],
        ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY')."&search=god&page_size=5");
        $games = $response->json();

        return view('games.index', compact('games'));
    }
    public function games($page = 1, $search = null){
       /* $teste = " TESTANDO TESTANDOTESTANDOTESTANDOTESTANDOTESTANDO TESTANDO";
        Log::info($teste); útil para depurar no console laravel.log*/
        $search = $search !== null ? $search : '';
        $certPath = storage_path('rawg_io.pem');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CAINFO, $certPath);
         $response = Http::withOptions([
            'curl' => [
                CURLOPT_CAINFO => $certPath,
            ],
        ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY')."&page=".$page."&page_size=24&search=".$search);
        $games = $response->json();
        return compact('games');
    }
    public function specificGameInfo($gameId){
        $certPath = storage_path('rawg_io.pem');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CAINFO, $certPath);
        $response = Http::withOptions([
            'curl' => [
                CURLOPT_CAINFO => $certPath,
            ],
        ])->get("https://api.rawg.io/api/games/".$gameId."?key=".env('RAWG_API_KEY'));
        $game = $response->json();
        return compact('game');
    }

    public function fetchOwned($owned_array){
        try {
        $certPath = storage_path('rawg_io.pem');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CAINFO, $certPath);
        $response = Http::withOptions([
            'curl' => [
                CURLOPT_CAINFO => $certPath,
            ],
        ])->get("https://api.rawg.io/api/games?ids=".$owned_array."&key=".env('RAWG_API_KEY'));
        $games = $response->json()['results'];
        return $games;
    }catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }}
}
