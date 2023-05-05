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

    //essa é utilizada para checar os jogos na lista de owned
    public function checkOwnedGames($user_id, $game_api_ids){
        return OwnedGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }

    //essa é utilizada para checar quais jogos estão na wishlist
    public function checkWishlist($user_id, $game_api_ids){
        return WishlistGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }
    //essa é utilizada para checar quais jogos estão na favorite list
    public function checkFavoriteGames($user_id, $game_api_ids){
        return FavoritedGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }

    //usado para checar na página atual quais jogos o usuário possui
    public function checkOwnedGamesStarter(Request $request){
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'game_api_ids' => 'required|array',
                'game_api_ids.*' => 'integer',
            ]);
            $user_id = $request->input('user_id');
            $game_api_ids = $request->input('game_api_ids');
            return OwnedGame::where('user_id', $user_id)
            ->whereIn('game_api_id', $game_api_ids)
            ->pluck('game_api_id')
            ->toArray();
        }catch (\Exception $e) {
            return response()->json(['Erro ao checar jogos owned' => $e->getMessage()], 500);
        }
    }

    //adiciona o jogo a lista de owned e retorna o novo conjunto do owned games (também remove da wishlist se precisar)
    public function addOwned(Request $request){
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

            //$tokenId = explode('|', $token)[0]; isso era qndo só tinha uma sessão permitida
            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();

            //se o token existir, entra
            if ($personalAccessTokens->isNotEmpty()) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        //caso exista o jogo nos owned, retorna erro
                        $check_owned = OwnedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if($check_owned){
                            return response()->json(['error' => 'Already own'], 400);
                        }
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

    //deleta o jogo da lista de owned e retorna o novo conjunto do owned games (também remove dos favoritos se precisar)
    public function removeOwned (Request $request){
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

            //$tokenId = explode('|', $token)[0]; isso era qndo só tinha uma sessão permitida
            //$personalAccessToken = PersonalAccessToken::where('id', $tokenId)->first(); isso tbm
            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();

            if ($personalAccessTokens->isNotEmpty()) {
                $token_value = explode('|', $token)[1];

                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {

                        //caso o jogo não exista nos owned, retorna erro
                        $check_owned = OwnedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if(!$check_owned){
                            return response()->json(['error' => 'Not owned'], 400);
                        }
                        $check_owned->delete();

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

    //adiciona jogo a lista de owned na página específica do jogo
    public function addSpecificOwned(Request $request){
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


            //$tokenId = explode('|', $token)[0]; isso era qndo só tinha uma sessão permitida
            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
            //se o token existir, entra
            if ($personalAccessTokens->isNotEmpty()) {

                $token_value = explode('|', $token)[1];

                foreach ($personalAccessTokens as $personalAccessToken) {

                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        //caso exista o jogo nos owned, retorna erro
                        $check_owned = OwnedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if($check_owned){
                            return response()->json(['error' => 'Already own'], 400);
                        }
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
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            }else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
            return response()->json(['Erro ao adicionar jogo' => $e->getMessage()], 500);
        }
    }

    //remove jogo da lista de owned na página específica do jogo
    public function removeSpecificOwned (Request $request){
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


        //$tokenId = explode('|', $token)[0]; isso era qndo só tinha uma sessão permitida
        //$personalAccessToken = PersonalAccessToken::where('id', $tokenId)->first(); isso tbm
        $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();

        if ($personalAccessTokens->isNotEmpty()) {
            $token_value = explode('|', $token)[1];

            foreach ($personalAccessTokens as $personalAccessToken) {
                if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                     //caso o jogo não exista nos owned, retorna erro
                     $check_owned = OwnedGame::where('user_id', $user_id)
                     ->where('game_api_id', $game_api_id)
                     ->first();
                     if(!$check_owned){
                         return response()->json(['error' => 'Not owned'], 400);
                     }
                     $check_owned->delete();

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
}
