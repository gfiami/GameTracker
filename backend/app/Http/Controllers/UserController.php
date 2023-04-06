<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
//esse validation serve para pegar erros vindos da $request->validate
use Illuminate\Validation\ValidationException;


class UserController extends Controller
{

     public function login(Request $request)
    {

            $validateUserInfo = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);
            //autenticação com tabela de users, retorna true se achar
            //ele já funciona como um "try/catch"
            //se nao achar, o código continua e nossa response será um erro
            //esse attempt também já checa se a senha está criptografada e resolve isso
            //apesar vendo se é do tipo email e se foram enviadas sem ser vazio
            //ver mais na documentação sobre o ->validate
            if (Auth::attempt($validateUserInfo)){
                //aqui o ($user vira o usuario achado)
                $user = $request->user();
                //token é criado a cada login e podemos usar para
                //interagir com user sem usar dados sensiveis
                $token = $user->createToken('authToken')->plainTextToken;
                return response()->json([
                    'token'=> $token,
                    'user' => $user,
                ]);
            }
            return response()->json(['message' => 'Login failed, please check your credentials'], 401);
        //caso falhe, retorna erro de credenciais 401
    }

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
}
