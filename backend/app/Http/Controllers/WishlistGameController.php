<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WishlistGame;
use App\Models\OwnedGame;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\PersonalAccessToken;


class WishlistGameController extends Controller
{
    //essa é utilizada para checar quais jogos estão na wishlist
    public function checkWishlist($user_id, $game_api_ids){
        return WishlistGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }

    //usado para checar na página atual quais jogos o usuário wishlistou
    public function checkWishlistStarter(Request $request){
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'game_api_ids' => 'required|array',
                'game_api_ids.*' => 'integer',
            ]);
            $user_id = $request->input('user_id');
            $game_api_ids = $request->input('game_api_ids');
            return WishlistGame::where('user_id', $user_id)
            ->whereIn('game_api_id', $game_api_ids)
            ->pluck('game_api_id')
            ->toArray();
        }catch (\Exception $e) {
            return response()->json(['Erro ao checar favoritos' => $e->getMessage()], 500);
        }
    }

    //adiciona o jogo a lista de wishlisted e retorna o novo conjunto de wishlisted
     public function addWishlist(Request $request){
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
                        //caso exista o jogo na wishlist, retorna erro
                        $check_wishlist = WishlistGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if($check_wishlist){
                            return response()->json(['error' => 'Already wishlisted'], 400);
                        }
                        $check_owned = OwnedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if($check_owned){
                            return response()->json(['error' => 'You cannot wishlist an owned game'], 400);
                        }
                        $wishlist_games = WishlistGame::create([
                            'user_id' => $user_id,
                            'game_api_id' => $game_api_id,
                        ]);
                        $wishlist_games = $this->checkWishlist($user_id, $game_api_ids);
                        return response()->json($wishlist_games);
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            } else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
            return response()->json(['Erro ao wishlistar jogo' => $e->getMessage()], 500);
        }
    }
    //deleta o jogo da wishlist e retorna o novo conjunto do wishlist
     public function removeWishlist (Request $request){
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
                        $check_wishlist = WishlistGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if(!$check_wishlist){
                            return response()->json(['error' => 'Not wishlisted'], 400);
                        }
                        $check_wishlist->delete();
                        $wishlist_games = $this->checkWishList($user_id, $game_api_ids);
                        return response()->json($wishlist_games);

                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            }else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
            return response()->json(['Erro ao deletar jogo da lista de desejos' => $e->getMessage()], 500);
        }
        }

    //adiciona jogo a wishlist na página específica do jogo
     public function addSpecificWishlist(Request $request){
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
                        //caso exista o jogo na wishlist, retorna erro
                        $check_wishlist = WishlistGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if($check_wishlist){
                            return response()->json(['error' => 'Already wishlisted'], 400);
                        }
                        $check_owned = OwnedGame::where('user_id', $user_id)
                        ->where('game_api_id', $game_api_id)
                        ->first();
                        if($check_owned){
                            return response()->json(['error' => 'You cannot wishlist an owned game'], 400);
                        }
                        $wishlist_games = WishlistGame::create([
                            'user_id' => $user_id,
                            'game_api_id' => $game_api_id,
                        ]);
                        return response()->json($wishlist_games);//indicando q foi add
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            }else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
            return response()->json(['Erro ao wishlistar jogo' => $e->getMessage()], 500);
        }
    }

    //remove jogo da lista de owned na página específica do jogo
     public function removeSpecificWishlist (Request $request){
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
                    $check_wishlist = WishlistGame::where('user_id', $user_id)
                    ->where('game_api_id', $game_api_id)
                    ->first();
                    if(!$check_wishlist){
                        return response()->json(['error' => 'Not wishlisted'], 400);
                    }
                    $check_wishlist->delete();
                    return response()->json(null); //mostra que agora o jogo nao tá nos wishlisted
                }
            }
            return response()->json(['error' => 'Unauthorized'], 401);
        }else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }catch (\Exception $e) {
        return response()->json(['Erro ao deletar jogo da lista de desejos' => $e->getMessage()], 500);
    }
    }


}
