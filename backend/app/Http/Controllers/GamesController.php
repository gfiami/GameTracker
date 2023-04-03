<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

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
    public function gameInfo(){
        $certPath = storage_path('rawg_io.pem');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CAINFO, $certPath);
        $response = Http::withOptions([
            'curl' => [
                CURLOPT_CAINFO => $certPath,
            ],
        ])->get("https://api.rawg.io/api/games?key=".env('RAWG_API_KEY')."&ordering=-rating&page_size=30");
        $games = $response->json();
        return compact('games');
    }
}
