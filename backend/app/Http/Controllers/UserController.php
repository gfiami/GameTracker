<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OwnedGame;
use App\Models\FavoritedGame;
use App\Models\WishlistGame;
use App\Models\PersonalAccessToken;
//pra deletar images
use Illuminate\Support\Facades\Storage;

//esse validation serve para pegar erros vindos da $request->validate
use Illuminate\Validation\ValidationException;
//esse Auth serve para auxiliar no login e realizar a autenticação do usuario
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

//esse personalacess serve para acessar a tabela de tokens do banco de dados, em conjunto com a auth do sanctum

class UserController extends Controller
{
    //LOGIN
     public function signin(Request $request)
    {
            $validateUserInfo = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6|max:10'
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
                //$user->tokens()->delete(); desabilitado para permitir multiplas sessões
                $token = $user->createToken('MyAppToken')->plainTextToken;

                /*token é criado a cada login e podemos usar para
                interagir com user sem usar dados sensiveis*/
                //criando um cookie que deve expirar em 15 dias
                return response()->json([
                    'user' => $user,
                    'personal_token' => $token,
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

    //logout
    public function logout(Request $request){
        try{
            $token = $request->bearerToken();
            $user_id = $request->input('user_id');

            //requisição nao enviou token junto
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();

            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        $personalAccessToken->delete();
                        $response = [
                            'message' => "Logout realizado com sucesso",
                        ];
                        return response()->json($response);
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            }else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
            return response()->json(['Erro ao realizar logout' => $e->getMessage()], 400);
        }
    }

    //get user info
    public function userInfo($id){
        $user = User::find($id);
        if ($user) {
            return response()->json([
                'user' => $user
            ]);
        } else {
            return response()->json([
                'error' => 'Usuário não encontrado'
            ], 404);
        }
    }
    //get all users for community tab
    public function getUsers(Request $request){
        $validatedData = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'order' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ]);


        $searchTerm = $validatedData['search'] ?? '';
        $page = $validatedData['page'] ?? 1;
        $order = $validatedData['order'];
        $pageSize = 10;

        $allUsers = User::select('id', 'name', 'image');

        $searchingUsers = $allUsers->where('name', 'like', '%' . $searchTerm . '%');
        $searchingUsers = $searchingUsers->orderBy('name', $order);
        $users = $searchingUsers->paginate($pageSize, ['*'], 'page', $page);

        return response()->json($users);

    }


    //Editar username
    public function editUsername(Request $request){
        try{
            $validateUserInfo = $request->validate([
                'username' =>'required|min:4|max:12',
            ]);

            $name = $validateUserInfo['username'];
            $token = $request->bearerToken();
            $user_id = $request->input('user_id');
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
             //se o token existir, entra
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        $user = User::find($user_id);
                        if($user){
                            $user->name = $name;
                            $user->save();

                            //mandar de volta o user atualizado
                            return response()->json([
                                'user' => $user
                            ]);
                        }else {
                            return response()->json([
                                'error' => 'Usuário não encontrado'
                            ], 404);
                        }
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            } else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'validation' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    }

    //Editar imagem do usuario
    public function editImage(Request $request){
        try{
            Log::info($request);
            if ($request->has('image')) {
                $request->validate([
                    'image' => 'required|image|max:2048',
                    ]);
                $image = $request->file('image');}
            else {
                return response()->json(['error' => 'No image file found']);
            }

            $image = $request->file('image');
            $token = $request->bearerToken();
            $user_id = $request->input('user_id');
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
             //se o token existir, entra
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        $user = User::find($user_id);
                        if($user){
                            $previousImage = $user->image;
                            if($previousImage){
                                $previousImagePath = public_path('images/' . $previousImage);
                                if(file_exists($previousImagePath)){
                                    unlink($previousImagePath);
                                }
                            }
                            $imageName = $image->hashName();
                            $user->image = $imageName;
                            $user->save();
                            $image->move(public_path('images'), $imageName);

                            return response()->json([
                                'user' => $user,
                            ]);
                        }else {
                            return response()->json([
                                'error' => 'Usuário não encontrado'
                            ], 404);
                        }
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            } else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'validation' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    }




}
