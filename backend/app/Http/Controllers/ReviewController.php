<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use App\Models\PersonalAccessToken;
use Illuminate\Validation\Rule;



class ReviewController extends Controller
{
    //pega as reviews de determinado usuario
    public function fetchUserReviews(Request $request){
    try {
        $user_id = $request->input('user_id');
        $reviews = Review::where('user_id', $user_id)
        ->where('approved', 1)
        ->get();
        $reviewsData = $reviews->map(function($review) {
            return [
                'username' => $review->user->name,
                'user_id' => $review->user_id,
                'review' => $review->review,
                'game_name' => $review->game_name,
                'game_api_id'=> $review->game_api_id,
                'image' => $review->user->image,
                'rating' => $review->rating,
                'created_at' => $review->created_at,
                'updated_at' => $review->updated_at
            ];
        });

        return response()->json($reviewsData);

    } catch (\Exception $e) {
        return response()->json(['Erro na requisição' => $e->getMessage()], 500);
    }
    }

    //pega as reviews de determinado jogo
    public function fetchReviews(Request $request){
        try {
            $validatedData = $request->validate([
                'filter_reviews' => ['nullable', 'string', Rule::in(['positive', 'negative'])],
            ]);

            $game_api_id = $request->input('game_api_id');
            $filter = $validatedData['filter_reviews'];
            $user_id = $request->input('user_id');
            $reviews = Review::where('game_api_id', $game_api_id)
            ->where('approved', 1)
            ->get();

            if(!empty($filter)){
                $reviews = Review::where('game_api_id', $game_api_id)
                ->where('approved', 1)
                ->where('rating', $filter)
                ->with('user')
                ->get();
            } else{
                $reviews = Review::where('game_api_id', $game_api_id)
                ->where('approved', 1)
                ->with('user')
                ->get();
            }
            $reviewsData = $reviews->map(function($review) {
                return [
                    'user_id' => $review->user_id,
                    'username' => $review->user->name,
                    'image' => $review->user->image,
                    'review' => $review->review,
                    'rating' => $review->rating,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at
                ];
            });

            //logado
            if($user_id){
                $userReview = Review::where('user_id', $user_id)
                ->where('game_api_id', $game_api_id)
                ->where('approved', 1)
                ->get();
                //user has review
                if(count($userReview) > 0){
                    $userReviewData = $userReview->map(function($review) {
                        return [
                            'user_id' => $review->user_id,
                            'username' => $review->user->name,
                            'image' => $review->user->image,
                            'review' => $review->review,
                            'rating' => $review->rating,
                            'created_at' => $review->created_at,
                            'updated_at' => $review->updated_at
                        ];
                    });
                    $responseData = [
                        'userReviewData' => $userReviewData,
                        'reviewsData' => $reviewsData,
                    ];
                    return response()->json($responseData);

                //sem review
                }else{
                    $responseData = [
                        'reviewsData' => $reviewsData,
                    ];
                    return response()->json($responseData);
                }
            //deslogado
            }else{
                $responseData = [
                    'reviewsData' => $reviewsData,
                ];
                return response()->json($responseData);
            }



        } catch (\Exception $e) {
            return response()->json(['Erro na requisição' => $e->getMessage()], 500);
        }
    }

    //adicionar uma review
    public function addReview(Request $request){
        try{

            $validateReviewInfo = $request->validate([
                'review' => 'required|string|max:1000',
                'rating' => 'required|string|max:10',

            ]);
            $token = $request->bearerToken();
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');
            $review_text = $request->input('review');
            $rating = $request->input('rating');
            $game_name = $request->input('game_name');
            //requisição nao enviou token junto
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
             //se o token existir, entra
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        $review = Review::create([
                            'user_id' => $user_id,
                            'game_api_id' => $game_api_id,
                            'review' => $review_text,
                            'rating' => $rating,
                            'game_name' => $game_name,
                        ]);
                        //mandar de volta as reviews atualizadas

                            $reviews = Review::where('game_api_id', $game_api_id)
                            ->where('approved', 1)
                            ->with('user')
                            ->get();

                        $reviewsData = $reviews->map(function($review) {
                            return [
                                'user_id' => $review->user_id,
                                'username' => $review->user->name,
                                'image' => $review->user->image,
                                'review' => $review->review,
                                'game_name' => $review->game_name,
                                'rating' => $review->rating,
                                'created_at' => $review->created_at,
                                'updated_at' => $review->updated_at
                            ];
                        });
                        return response()->json([
                            'reviews' => $reviewsData,
                            'message' => 'Your review was submited successfully!',
                        ]);
                    }
                }
                return response()->json(['error' => 'Unauthorized'], 401);
            }else{
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

    //edit a review
    public function editReview(Request $request){
        try{
            $validateReviewInfo = $request->validate([
                'review' => 'required|string|max:1000',
                'rating' => 'required|string|max:10',

            ]);
            $token = $request->bearerToken();
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');
            $review_text = $request->input('review');
            $rating = $request->input('rating');
            if (!$token) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
             //se o token existir, entra
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        $review = Review::where('user_id', $user_id)
                                ->where('game_api_id', $game_api_id)
                                ->first();
                        if($review){
                            $review->review = $review_text;
                            $review->rating = $rating;
                            $review->save();
                            //mandar de volta as reviews atualizadas
                                $reviews = Review::where('game_api_id', $game_api_id)
                                ->where('approved', 1)
                                ->with('user')
                                ->get();

                            $reviewsData = $reviews->map(function($review) {
                                return [
                                    'user_id' => $review->user_id,
                                    'username' => $review->user->name,
                                    'review' => $review->review,
                                    'image' => $review->user->image,
                                    'rating' => $review->rating,
                                    'created_at' => $review->created_at,
                                    'updated_at' => $review->updated_at,
                                ];
                            });

                            $reviews = Review::where('user_id', $user_id)
                            ->where('approved', 1)
                            ->get();
                            $profileReviews = $reviews->map(function($review) {
                                return [
                                    'username' => $review->user->name,
                                    'user_id' => $review->user_id,
                                    'review' => $review->review,
                                    'image' => $review->user->image,

                                    'game_name' => $review->game_name,
                                    'game_api_id'=> $review->game_api_id,
                                    'rating' => $review->rating,
                                    'created_at' => $review->created_at,
                                    'updated_at' => $review->updated_at
                                    ];
                                });
                            return response()->json([
                                'profileReviews' => $profileReviews,
                                'reviews' => $reviewsData,
                                'message' => 'Your review was edited successfully!',
                            ]);
                        }else {
                            return response()->json([
                                'message' => 'Review not found',
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

    //deleta a review
    public function deleteReview(Request $request){
        try{

            $token = $request->bearerToken();
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');

            $personalAccessTokens = PersonalAccessToken::where('tokenable_id', $user_id)->get();
             //se o token existir, entra
            if ($personalAccessTokens) {
                $token_value = explode('|', $token)[1];
                foreach ($personalAccessTokens as $personalAccessToken) {
                    if (hash_equals($personalAccessToken->token, hash('sha256', $token_value))) {
                        $review = Review::where('user_id', $user_id)
                                ->where('game_api_id', $game_api_id)
                                ->first();
                        if($review){
                            $review->delete();

                            $reviews = Review::where('game_api_id', $game_api_id)
                            ->where('approved', 1)
                            ->with('user')
                            ->get();

                            $reviewsData = $reviews->map(function($review) {
                                return [
                                    'user_id' => $review->user_id,
                                    'username' => $review->user->name,
                                    'review' => $review->review,
                                    'image' => $review->user->image,
                                    'rating' => $review->rating,
                                    'created_at' => $review->created_at,
                                    'updated_at' => $review->updated_at
                                ];
                            });
                            $reviews = Review::where('user_id', $user_id)
                            ->where('approved', 1)
                            ->get();
                            $profileReviews = $reviews->map(function($review) {
                                return [
                                    'username' => $review->user->name,
                                    'user_id' => $review->user_id,
                                    'review' => $review->review,
                                    'game_name' => $review->game_name,
                                    'game_api_id'=> $review->game_api_id,
                                    'image' => $review->user->image,
                                    'rating' => $review->rating,
                                    'created_at' => $review->created_at,
                                    'updated_at' => $review->updated_at
                                    ];
                                });
                            return response()->json([
                                'reviews' => $reviewsData,
                                'profileReviews' => $profileReviews,
                                'message' => 'Your review was deleted successfully!',
                            ]);
                        }else {
                            return response()->json([
                                'message' => 'Review not found',
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
