<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FavoritedGame;
use App\Models\OwnedGame;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\PersonalAccessToken;

class FavoritedGameController extends Controller
{

    //serve para recursivamente checarmos todos os jogos favoritos(feito aqui dentro mesmo)
    public function checkFavoriteGames($user_id, $game_api_ids){
        return FavoritedGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }
    //usada no inicio do carregamento
    public function checkFavoriteGamesStarter(Request $request){
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'game_api_ids' => 'required|array',
                'game_api_ids.*' => 'integer',
            ]);
            $user_id = $request->input('user_id');
            $game_api_ids = $request->input('game_api_ids');
            return FavoritedGame::where('user_id', $user_id)
            ->whereIn('game_api_id', $game_api_ids)
            ->pluck('game_api_id')
            ->toArray();

        }catch (\Exception $e) {
            return response()->json(['Erro ao checar favoritos' => $e->getMessage()], 500);
        }
    }

    //adiciona jogo a lista de favoritos
    public function addFavorite(Request $request){
        try {
            $token = $request->bearerToken();
            //requisição nao enviou token junto
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $request->validate([
                'user_id' => 'required|integer',
                'game_api_id' => 'required|integer',
                'game_api_ids' => 'required|array',
                'game_api_ids.*' => 'integer',
            ]);
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');
            $game_api_ids = $request->input('game_api_ids');

            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
            //se o token existir, entra
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        //caso exista o jogo nos favorites, retorna erro
                        $check_favorite = FavoritedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if($check_favorite){
                            return response()->json(['error' => 'Already favorite'], 400);
                        }
                        $check_owned = OwnedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if(!$check_owned){
                            return response()->json(['error' => 'Not owned'], 400);
                        }
                        $favorite_game = FavoritedGame::create([
                            'user_id' => $user_id,
                            'game_api_id' => $game_api_id,
                        ]);
                        $favorite_game = $this->checkFavoriteGames($user_id, $game_api_ids);
                        return response()->json($favorite_game);
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);

            } else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
            return response()->json(['Erro ao favoritar jogo' => $e->getMessage()], 500);
        }
    }

    //remove jogo da lista de favoritos
    public function removeFavorite (Request $request){
        try{
            $token = $request->bearerToken();
            //requisição nao enviou token junto
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $request->validate([
                'user_id' => 'required|integer',
                'game_api_id' => 'required|integer',
                'game_api_ids' => 'required|array',
                'game_api_ids.*' => 'integer',
            ]);
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');
            $game_api_ids = $request->input('game_api_ids');

            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];

                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        //caso o jogo não exista nos favorites, retorna erro
                        $check_favorite = FavoritedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if(!$check_favorite){
                            return response()->json(['error' => 'Not favorited'], 400);
                        }
                        $check_favorite->delete();
                        $favorite_games = $this->checkFavoriteGames($user_id, $game_api_ids);
                        return response()->json($favorite_games);
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);

            }else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
            return response()->json(['Erro ao remover jogo dos favoritos' => $e->getMessage()], 500);
        }
    }

    //adiciona jogo a lista de favoritos na página específica do jogo
    public function addSpecificFavorite(Request $request){
        try {
            $token = $request->bearerToken();
            //requisição nao enviou token junto
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $request->validate([
                'user_id' => 'required|integer',
                'game_api_id' => 'required|integer',
            ]);
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');

            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();

            //se o token existir, entra
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                         //caso exista o jogo nos favorites, retorna erro
                        $check_favorite = FavoritedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if($check_favorite){
                            return response()->json(['error' => 'Already favorite'], 400);
                        }
                        $check_owned = OwnedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if(!$check_owned){
                            return response()->json(['error' => 'Not owned'], 400);
                        }
                        $favorite_game = FavoritedGame::create([
                            'user_id' => $user_id,
                            'game_api_id' => $game_api_id,
                        ]);
                        return response()->json($favorite_game); //add aos fav
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            }else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
            return response()->json(['Erro ao favoritar jogo' => $e->getMessage()], 500);
        }
    }

    //remove jogo da lista de favoritos na página específica do jogo
    public function removeSpecificFavorite (Request $request){
        try{
        $token = $request->bearerToken();
        //requisição nao enviou token junto
        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $request->validate([
            'user_id' => 'required|integer',
            'game_api_id' => 'required|integer',
        ]);
        $user_id = $request->input('user_id');
        $game_api_id = $request->input('game_api_id');


        $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
        if ($personalAccessTokens) {
            $token_value = explode('|', $token)[1];
            foreach ($personalAccessTokens as $personalAccessToken) {
                if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                    //caso o jogo não exista nos favorites, retorna erro
                    $check_favorite = FavoritedGame::where('user_id', $user_id)
                    ->where('game_api_id', $game_api_id)
                    ->first();
                    if(!$check_favorite){
                        return response()->json(['error' => 'Not favorited'], 400);
                    }
                    $check_favorite->delete();
                    return response()->json(null); //mostra que agora o jogo nao tá nos fav
                }
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }catch (\Exception $e) {
        return response()->json(['Erro ao remover jogo dos favoritos' => $e->getMessage()], 500);
    }
    }
}
