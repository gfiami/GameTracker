<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FriendRequest;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Log;


class FriendRequestController extends Controller
{
    public function addFriend(Request $request){
        try{
        $user_id = $request->input('user_id');
        $request_to = $request->input('request_to');
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
        if ($personalAccessTokens) {
            $token_value = explode('|', $token)[1];
            foreach ($personalAccessTokens as $personalAccessToken) {
                if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                    $check_request = FriendRequest::where('user_id', $user_id)
                    ->where('request_to', $request_to)
                    ->first();
                    if($check_request){
                        return response()->json(['error' => 'Already own'], 400);
                    }
                    $friend_request = FriendRequest::create([
                        'user_id' => $user_id,
                        'request_to' => $request_to,
                    ]);

                    $all_user_request = FriendRequest::where('user_id', $user_id)
                    ->pluck('request_to')
                    ->toArray();

                    return response()->json($all_user_request);
                }
            }
            return response()->json(['error' => 'Unauthorized'], 401);

        } else{
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        }catch (\Exception $e) {
            return response()->json(['Erro ao adicionar amigo' => $e->getMessage()], 500);
        }
    }
}
