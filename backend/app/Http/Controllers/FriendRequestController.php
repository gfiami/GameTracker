<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\FriendRequest;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Log;


class FriendRequestController extends Controller
{
    public function checkFriends(Request $request){
        try{
            $validateUserInfo = $request->validate([
                'user_id' => ['nullable', 'integer', 'min:1'],
                'profile_id' => ['nullable', 'integer', 'min:1'],
            ]);
            $user_id = $validateUserInfo['user_id'];
            $profile_id = $validateUserInfo['profile_id'];

            $check_friends = FriendRequest::where('user_id', $user_id)
            ->where('request_to', $profile_id)
            ->where('status', 1)
            ->first();
            if($check_friends){
                $friends = true;
            }else{
                $check_friends = FriendRequest::where('request_to', $user_id)
                ->where('user_id', $profile_id)
                ->where('status', 1)
                ->first();
                if($check_friends){
                    $friends = true;
                }else{
                    $friends = false;
                }
            }
            $response = [
                'friends' => $friends,
            ];
            return response()->json($response);
        }catch (\Exception $e) {
            return response()->json(['Erro ao checar amigo' => $e->getMessage()], 500);
        }
    }
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
                            return response()->json(['error' => 'Already requested'], 400);
                        }
                        $check_request = FriendRequest::where('request_to', $user_id)
                        ->where('user_id', $request_to)
                        ->first();
                        if($check_request){
                            return response()->json(['error' => 'Already requested'], 400);
                        }
                        $friend_request = FriendRequest::create([
                            'user_id' => $user_id,
                            'request_to' => $request_to,
                        ]);

                        $all_user_request = FriendRequest::where('user_id', $user_id)
                        ->where('status', 0)
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
    public function cancelRequest(Request $request){
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
                        //caso não haja request para ser deletado, remove
                        $check_request = FriendRequest::where('user_id', $user_id)
                        ->where('request_to', $request_to)
                        ->where('status', 0)
                        ->first();
                         if(!$check_request){
                             return response()->json(['error' => 'Not requested or already accepted'], 400);
                         }
                         $check_request->delete();

                        $all_user_request = FriendRequest::where('user_id', $user_id)
                        ->where('status', 0)
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
                return response()->json(['Erro ao cancelar requisição de amizade' => $e->getMessage()], 500);
        }
    }
    public function declineFriend(Request $request){
        try{
            $user_id = $request->input('user_id');
            $sender = $request->input('sender');
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        //caso não haja request para ser deletado, remove
                        $check_request = FriendRequest::where('user_id', $sender)
                        ->where('request_to', $user_id)
                        ->first();
                         if(!$check_request){
                             return response()->json(['error' => 'Current user not found in the request list of the other user'], 400);
                         }
                         $check_request->delete();

                         $requests_received = FriendRequest::where('request_to', $user_id)
                         ->where('status', 0)
                         ->pluck('user_id')
                         ->toArray();

                        return response()->json($requests_received);
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);

            } else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
                return response()->json(['Erro ao cancelar requisição de amizade' => $e->getMessage()], 500);
        }
    }
    public function acceptFriend(Request $request){
        try{
            $user_id = $request->input('user_id');
            $sender = $request->input('sender');
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];

                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        $check_friend = FriendRequest::where('user_id', $sender)
                        ->where('request_to', $user_id)
                        ->where('status', 1)
                        ->first();
                        if($check_friend){
                            return response()->json(['error' => 'Already friend'], 400);
                        }
                        $check_friend = FriendRequest::where('request_to', $sender)
                        ->where('user_id', $user_id)
                        ->where('status', 1)
                        ->first();
                        if($check_friend){
                            return response()->json(['error' => 'Already friend'], 400);
                        }
                        $add_friend = FriendRequest::where('user_id', $sender)
                        ->where('request_to', $user_id)
                        ->where('status', 0)
                        ->first();
                        $add_friend->status = 1;
                        $add_friend->save();

                        //get all friends of logged user
                        $friendsFirst = FriendRequest::where('user_id', $user_id)
                        ->where('status', 1)
                        ->pluck('request_to')
                        ->toArray();
                        $friendsSecond = FriendRequest::where('request_to', $user_id)
                        ->where('status', 1)
                        ->pluck('user_id')
                        ->toArray();
                        $friends = array_merge($friendsFirst, $friendsSecond);
                        $requests_received = FriendRequest::where('request_to', $user_id)
                        ->where('status', 0)
                        ->pluck('user_id')
                        ->toArray();

                        $response = [
                            'requestsReceived' => $requests_received,
                            'friends' => $friends,
                        ];
                        return response()->json($response);
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
    public function removeFriend(Request $request){
        try{
            $user_id = $request->input('user_id');
            $friend = $request->input('friend');
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        //caso não haja request para ser deletado
                        $check_friend_first = FriendRequest::where('user_id', $friend)
                        ->where('request_to', $user_id)
                        ->where('status', 1)
                        ->first();

                        $check_friend_second = FriendRequest::where('request_to', $friend)
                        ->where('user_id', $user_id)
                        ->where('status', 1)
                        ->first();
                        if(!$check_friend_first && !$check_friend_second){
                            return response()->json(['error' => 'Not a friend'], 400);
                        }else{
                            if($check_friend_first){
                                $check_friend = $check_friend_first;
                            }else{
                                $check_friend = $check_friend_second;
                            }
                        }
                        $check_friend->delete();

                        //get all friends of logged user
                        $friendsFirst = FriendRequest::where('user_id', $user_id)
                        ->where('status', 1)
                        ->pluck('request_to')
                        ->toArray();
                        $friendsSecond = FriendRequest::where('request_to', $user_id)
                        ->where('status', 1)
                        ->pluck('user_id')
                        ->toArray();
                        $friends = array_merge($friendsFirst, $friendsSecond);

                        $response = [
                            'friends' => $friends,
                        ];
                        return response()->json($response);
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);

            } else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }catch (\Exception $e) {
                return response()->json(['Erro ao remover amizade' => $e->getMessage()], 500);
        }
    }
}
