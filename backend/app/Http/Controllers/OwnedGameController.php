<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OwnedGame;
use App\Models\FavoritedGame;
use App\Models\WishlistGame;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Models\PersonalAccessToken;


class OwnedGameController extends Controller
{
    //OWN
    //Pega a lista de todos os owned mas aqui vamos interagir mais com a api rawg despois(praticamente igual a de baixo, rever...)
    public function fetchAllOwned(Request $request){
        try{
        $user_id = $request->input('user_id');
        $owned_games = OwnedGame::where('user_id', $user_id)
        ->pluck('game_api_id')
        ->toArray();
        return response()->json($owned_games);
         } catch (\Exception $e) {
            return response()->json(['Erro na requisição' => $e->getMessage()], 500);
        }
    }
    //essa é utilizada para checar os OwnedGames
    public function checkOwnedGames($user_id, $game_api_ids){
        return OwnedGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }
    //essa é utilizada para checar os wishlist
    public function checkWishlist($user_id, $game_api_ids){
        return WishlistGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }
    public function checkFavoriteGames($user_id, $game_api_ids){
        return FavoritedGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }

    //aqui é usada mais nos gamelayouts para no inicio carregar os dados necessários
    public function checkOwnedGamesStarter(Request $request){
        $user_id = $request->input('user_id');
        $game_api_ids = $request->input('game_api_ids');
        return OwnedGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }

    //adiciona o jogo a lista de owned e retorna o novo conjunto do owned games
    public function addOwned(Request $request){
        try {
            $token = $request->bearerToken();
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');
            $game_api_ids = $request->input('game_api_ids');

            //requisição nao enviou token junto
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            //$tokenId = explode('|', $token)[0]; isso era qndo só tinha uma sessão permitida
            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();

            //se o token existir, entra
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        $owned_game = OwnedGame::create([
                            'user_id' => $user_id,
                            'game_api_id' => $game_api_id,
                        ]);
                        //caso exista o jogo na lista de desejos, remove
                        $remove_wishlist = WishlistGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if($remove_wishlist){
                            $remove_wishlist->delete();
                        }
                        $owned_games = $this->checkOwnedGames($user_id, $game_api_ids);
                        $wishlisted_games = $this->checkWishlist($user_id, $game_api_ids);
                        $response = [
                        'owned_games' => $owned_games,
                        'wishlisted_games' => $wishlisted_games
                        ];
                        return response()->json($response);
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);

            } else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
            return response()->json(['Erro ao adicionar jogo' => $e->getMessage()], 500);
        }
    }

    //deleta o jodo da lista de owned e retorna o novo conjunto do owned games
    public function removeOwned (Request $request){
        try{

            $token = $request->bearerToken();
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');
            $game_api_ids = $request->input('game_api_ids');

            //requisição nao enviou token junto
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            //$tokenId = explode('|', $token)[0]; isso era qndo só tinha uma sessão permitida
            //$personalAccessToken = PersonalAccessToken::where('id', $tokenId)->first(); isso tbm
            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();

            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];

                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        Log::info("Entrou no remove owned autenticado!");
                        OwnedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->delete();
                        $remove_favorite = FavoritedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        //caso exista o jogo nos favoritos, deleta ele de lá
                        if($remove_favorite){
                            $remove_favorite->delete();
                        }
                        $owned_games = $this->checkOwnedGames($user_id, $game_api_ids);
                        $favorite_games = $this->checkFavoriteGames($user_id, $game_api_ids);
                        $response = [
                            'owned_games' => $owned_games,
                            'favorite_games' => $favorite_games
                        ];
                        return response()->json($response);
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            }else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
            return response()->json(['Erro ao deletar jogo' => $e->getMessage()], 500);
        }
    }

    //specific game page
    //specific owned
    public function addSpecificOwned(Request $request){
        try {
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');
            $game_api_ids = $request->input('game_api_ids');

            $owned_game = OwnedGame::create([
                'user_id' => $user_id,
                'game_api_id' => $game_api_id,
            ]);
            //caso exista o jogo na lista de desejos, remove
        $remove_wishlist = WishlistGame::where('user_id', $user_id)
        ->where('game_api_id', $game_api_id)
        ->first();
        if($remove_wishlist){
            $remove_wishlist->delete();
        }

        $response = [
            'owned_game' => $owned_game,
            'wishlisted_game' => null,
        ];
        return response()->json($response);

        }catch (\Exception $e) {
            return response()->json(['Erro ao adicionar jogo' => $e->getMessage()], 500);
        }
    }

    //deleta o jodo da lista de owned e retorna o novo conjunto do owned games
    public function removeSpecificOwned (Request $request){
        try{
        $user_id = $request->input('user_id');
        $game_api_id = $request->input('game_api_id');

        OwnedGame::where('user_id', $user_id)
        ->where('game_api_id', $game_api_id)
        ->delete();

        $remove_favorite = FavoritedGame::where('user_id', $user_id)
        ->where('game_api_id', $game_api_id)
        ->first();

        //caso exista o jogo nos favoritos, deleta ele de lá
        if($remove_favorite){
            $remove_favorite->delete();
        }
    $response = [
        'owned_game' => null,
        'favorite_game' => null,
    ];
    return response()->json($response);

    }catch (\Exception $e) {
        return response()->json(['Erro ao deletar jogo' => $e->getMessage()], 500);
    }
    }
}
