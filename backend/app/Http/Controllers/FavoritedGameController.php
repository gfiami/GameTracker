<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FavoritedGame;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class FavoritedGameController extends Controller
{
    //FAVORITES
    //serve para pegar todos os favoritos e depois interagir com a api rawg
    public function fetchAllFavorite(Request $request){
        try{
        $user_id = $request->input('user_id');
        $favorite_games = FavoritedGame::where('user_id', $user_id)
        ->pluck('game_api_id')
        ->toArray();
        return response()->json($favorite_games);
         } catch (\Exception $e) {
            return response()->json(['Erro na requisição' => $e->getMessage()], 500);
        }
    }

    //serve para recursivamente checarmos todos os jogos favoritos
    public function checkFavoriteGames($user_id, $game_api_ids){
        return FavoritedGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }
    //usada no inicio do carregamento
    public function checkFavoriteGamesStarter(Request $request){
        $user_id = $request->input('user_id');
        $game_api_ids = $request->input('game_api_ids');
        return FavoritedGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }

    //adiciona jogo a lista de favoritos
    public function addFavorite(Request $request){
        try {
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');
            $game_api_ids = $request->input('game_api_ids');

            $favorite_game = FavoritedGame::create([
                'user_id' => $user_id,
                'game_api_id' => $game_api_id,
            ]);
            $favorite_game = $this->checkFavoriteGames($user_id, $game_api_ids);
            return response()->json($favorite_game);
        }catch (\Exception $e) {
            return response()->json(['Erro ao favoritar jogo' => $e->getMessage()], 500);
        }
    }

    public function removeFavorite (Request $request){
        try{
        $user_id = $request->input('user_id');
        $game_api_id = $request->input('game_api_id');
        $game_api_ids = $request->input('game_api_ids');
        FavoritedGame::where('user_id', $user_id)
        ->where('game_api_id', $game_api_id)
        ->first()
        ->delete();
        $favorite_games = $this->checkFavoriteGames($user_id, $game_api_ids);
        return response()->json($favorite_games);
    }catch (\Exception $e) {
        return response()->json(['Erro ao remover jogo dos favoritos' => $e->getMessage()], 500);
    }
    }

//specific game page
public function addSpecificFavorite(Request $request){
    try {
        $user_id = $request->input('user_id');
        $game_api_id = $request->input('game_api_id');

        $favorite_game = FavoritedGame::create([
            'user_id' => $user_id,
            'game_api_id' => $game_api_id,
        ]);
        return response()->json($favorite_game); //add aos fav
    }catch (\Exception $e) {
        return response()->json(['Erro ao favoritar jogo' => $e->getMessage()], 500);
    }
}

public function removeSpecificFavorite (Request $request){
    try{
    $user_id = $request->input('user_id');
    $game_api_id = $request->input('game_api_id');
    FavoritedGame::where('user_id', $user_id)
    ->where('game_api_id', $game_api_id)
    ->first()
    ->delete();
    return response()->json(null); //mostra que agora o jogo nao tá nos fav
}catch (\Exception $e) {
    return response()->json(['Erro ao remover jogo dos favoritos' => $e->getMessage()], 500);
}
}
}
