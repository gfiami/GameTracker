<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WishlistGame;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class WishlistGameController extends Controller
{
     //WISHLIST
    //usado na parte de perfil para pegar dados junto de uma função ligada a api rawg
    public function fetchAllWished(Request $request){
        try{
        $user_id = $request->input('user_id');
        $wishlist_games = WishlistGame::where('user_id', $user_id)
        ->pluck('game_api_id')
        ->toArray();
        return response()->json($wishlist_games);
     } catch (\Exception $e) {
            return response()->json(['Erro na requisição' => $e->getMessage()], 500);
        }
    }
    public function checkWishlist($user_id, $game_api_ids){
        return WishlistGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }
    //usada no inicio do carregamento
    public function checkWishlistStarter(Request $request){
        $user_id = $request->input('user_id');
        $game_api_ids = $request->input('game_api_ids');
        return WishlistGame::where('user_id', $user_id)
                ->whereIn('game_api_id', $game_api_ids)
                ->pluck('game_api_id')
                ->toArray();
    }

     public function addWishlist(Request $request){
        try {
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');
            $game_api_ids = $request->input('game_api_ids');

            $wishlist_games = WishlistGame::create([
                'user_id' => $user_id,
                'game_api_id' => $game_api_id,
            ]);
            $wishlist_games = $this->checkWishlist($user_id, $game_api_ids);
            return response()->json($wishlist_games);
        }catch (\Exception $e) {
            return response()->json(['Erro ao wishlistar jogo' => $e->getMessage()], 500);
        }
    }

     public function removeWishlist (Request $request){
        try{
        $user_id = $request->input('user_id');
        $game_api_id = $request->input('game_api_id');
        $game_api_ids = $request->input('game_api_ids');
        WishlistGame::where('user_id', $user_id)
        ->where('game_api_id', $game_api_id)
        ->first()
        ->delete();
        $wishlist_games = $this->checkWishList($user_id, $game_api_ids);
        return response()->json($wishlist_games);
    }catch (\Exception $e) {
        return response()->json(['Erro ao deletar jogo da lista de desejos' => $e->getMessage()], 500);
    }
    }

    //specific page
     public function addSpecificWishlist(Request $request){
        try {
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');

            $wishlist_games = WishlistGame::create([
                'user_id' => $user_id,
                'game_api_id' => $game_api_id,
            ]);
            return response()->json($wishlist_games);//indicando q foi add
        }catch (\Exception $e) {
            return response()->json(['Erro ao wishlistar jogo' => $e->getMessage()], 500);
        }
    }

     public function removeSpecificWishlist (Request $request){
        try{
        $user_id = $request->input('user_id');
        $game_api_id = $request->input('game_api_id');

        WishlistGame::where('user_id', $user_id)
        ->where('game_api_id', $game_api_id)
        ->first()
        ->delete();
        return response()->json(null); //mostra que agora o jogo nao tá nos wishlisted
    }catch (\Exception $e) {
        return response()->json(['Erro ao deletar jogo da lista de desejos' => $e->getMessage()], 500);
    }
    }


}
