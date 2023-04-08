<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OwnedGame;
use App\Models\FavoritedGame;
use App\Models\WishlistGame;




//esse validation serve para pegar erros vindos da $request->validate
use Illuminate\Validation\ValidationException;
//esse Auth serve para auxiliar no login e realizar a autenticação do usuario
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

//esse personalacess serve para acessar a tabela de tokens do banco de dados, em conjunto com a auth do sanctum

class UserController extends Controller
{

    //LOGIN
     public function signin(Request $request)
    {

            $validateUserInfo = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
            /*
            -autenticação com tabela de users, retorna true se achar
            -ele já funciona como um "try/catch"
            -se nao achar, o código continua e nossa response será um erro
            -esse attempt também já checa se a senha está criptografada e resolve isso
            -apesar vendo se é do tipo email e se foram enviadas sem ser vazio
            -ver mais na documentação sobre o ->validate
            */
            if (Auth::attempt($validateUserInfo)){
                //aqui o ($user vira o usuario achado)
                $user = $request->user();
                /*token é criado a cada login e podemos usar para
                interagir com user sem usar dados sensiveis*/
                //criando um cookie que deve expirar em 15 dias
                return response()->json([
                    'user' => $user,
                    'message' => "Login successeful!",
                ]);
            }
            return response()->json(['message' => 'Login failed, please check your credentials'], 401);
        //caso falhe, retorna erro de credenciais 401
    }
    //REGISTER
    public function register(Request $request){
        //aqui depois posso ajustar alguams validações! checar na documentação para o ->validade
        //usando try catch para encontrar possiveis erros!
        try {
            $validateUserInfo = $request->validate([
                'username' =>'required|min:4|max:12',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|max:10|confirmed'
            ]);
                //aqui há a criação do user pegando o array anterior e colocando nos campos correspondentes na DB
            $user = User::create([
                'name' => $validateUserInfo['username'],
                'email'=> $validateUserInfo['email'],
                'password' => bcrypt($validateUserInfo['password']),
            ]);
                //por fim aqui vamos mandar a resposta de volta para o VUE
            return response()->json([
                'message' => 'Your GameTracker account was created successfully!',
                'user' => $user
            ]);
        // catch (\Throwable $th) serve para pegar TODOS os erros, enquando que o usado(ValidationException $e) foca na validação

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'validation' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
            //retornar junto esse erro 422 significa que "deu tudo certo", mas não passou na validação
            //basicamente o user fez besteirinhas na digitação que não passou na minha função validate
        }
    }


    //OWN
    public function addOwned(Request $request){
        try {
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');

            $owned_game = OwnedGame::create([
                'user_id' => $user_id,
                'game_api_id' => $game_api_id,
            ]);
            return response()->json(['message' => 'Game add to owned games!']);

        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkOwned(Request $request){
    try {
        $user_id = $request->input('user_id');
        $game_api_ids = $request->input('game_api_ids');

        $owned_games = OwnedGame::where('user_id', $user_id)
        ->whereIn('game_api_id', $game_api_ids)
        ->pluck('game_api_id')
        ->toArray();
        return response()->json($owned_games);

    }catch (\Exception $e) {
        return response()->json(['Erro na requisição' => $e->getMessage()], 500);
    }
    }

    public function fetchAllOwned(Request $request){
        try{
        $user_id = $request->input('user_id');
       // Log::info($user_id);

        $owned_games = OwnedGame::where('user_id', $user_id)
        ->pluck('game_api_id')
        ->toArray();
        return response()->json($owned_games);
         } catch (\Exception $e) {
            return response()->json(['Erro na requisição' => $e->getMessage()], 500);
        }

    }

    public function removeOwned (Request $request){
        try{
        $user_id = $request->input('user_id');
        $game_api_id = $request->input('game_api_id');
        OwnedGame::where('user_id', $user_id)
        ->where('game_api_id', $game_api_id)
        ->delete();
        return response()->json(['message' => 'Jogo removido com sucesso!']);
    }catch (\Exception $e) {
        return response()->json(['Erro ao deletar jogo' => $e->getMessage()], 500);
    }
    }
    //FAVORITES
    public function addFavorite(Request $request){
        try {
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');

            $favorite_game = FavoritedGame::create([
                'user_id' => $user_id,
                'game_api_id' => $game_api_id,
            ]);
            return response()->json(['message' => 'Game add to favorite games!']);

        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkFavorite(Request $request){
    try {
        $user_id = $request->input('user_id');
        $game_api_ids = $request->input('game_api_ids');
        //Log::info($user_id);
        //Log::info($game_api_ids);
        $favorite_games = FavoritedGame::where('user_id', $user_id)
        ->whereIn('game_api_id', $game_api_ids)
        ->pluck('game_api_id')
        ->toArray();
        return response()->json($favorite_games);

    }catch (\Exception $e) {
        return response()->json(['Erro na requisição' => $e->getMessage()], 500);
    }

    }
    public function fetchAllFavorite(Request $request){
        try{
        $user_id = $request->input('user_id');
        //Log::info($user_id);

        $favorite_games = FavoritedGame::where('user_id', $user_id)
        ->pluck('game_api_id')
        ->toArray();
        return response()->json($favorite_games);
         } catch (\Exception $e) {
            return response()->json(['Erro na requisição' => $e->getMessage()], 500);
        }

    }
    public function removeFavorite (Request $request){
        try{
        $user_id = $request->input('user_id');
        $game_api_id = $request->input('game_api_id');
        FavoritedGame::where('user_id', $user_id)
        ->where('game_api_id', $game_api_id)
        ->delete();
        return response()->json(['message' => 'Jogo removido com sucesso dos favoritos!']);
    }catch (\Exception $e) {
        return response()->json(['Erro ao deletar jogo' => $e->getMessage()], 500);
    }
    }

    //WISHLIST
    public function addWishlist(Request $request){
        try {
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');

            $wishlist_game = WishlistGame::create([
                'user_id' => $user_id,
                'game_api_id' => $game_api_id,
            ]);
            return response()->json(['message' => 'Game add to wishlist games!']);

        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkWishlist(Request $request){
    try {
        $user_id = $request->input('user_id');
        $game_api_ids = $request->input('game_api_ids');
        //Log::info($user_id);
        //Log::info($game_api_ids);
        $wishlist_games = WishlistGame::where('user_id', $user_id)
        ->whereIn('game_api_id', $game_api_ids)
        ->pluck('game_api_id')
        ->toArray();
        return response()->json($wishlist_games);

    }catch (\Exception $e) {
        return response()->json(['Erro na requisição' => $e->getMessage()], 500);
    }}
    public function fetchAllWished(Request $request){
        try{
        $user_id = $request->input('user_id');
        //Log::info($user_id);
        $wishlist_games = WishlistGame::where('user_id', $user_id)
        ->pluck('game_api_id')
        ->toArray();
        return response()->json($wishlist_games);
     } catch (\Exception $e) {
            return response()->json(['Erro na requisição' => $e->getMessage()], 500);
        }

    }
    public function removeWishlist (Request $request){
        try{
        $user_id = $request->input('user_id');
        $game_api_id = $request->input('game_api_id');
        WishlistGame::where('user_id', $user_id)
        ->where('game_api_id', $game_api_id)
        ->delete();
        return response()->json(['message' => 'Jogo removido com sucesso da wishlist!']);
    }catch (\Exception $e) {
        return response()->json(['Erro ao deletar jogo' => $e->getMessage()], 500);
    }
    }
}
