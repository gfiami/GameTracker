<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\User;


class ReviewController extends Controller
{
    public function fetchReviews(Request $request){
        try {
            $game_api_id = $request->input('game_api_id');
            $reviews = Review::where('game_api_id', $game_api_id)
            ->where('approved', 1)
            ->with('user')
            ->get();
            $reviewsData = $reviews->map(function($review) {
                return [
                    'user_id' => $review->user_id,
                    'username' => $review->user->name,
                    'review' => $review->review,
                    //depois lembrar de por coisa para imagem que o user terá! para mostrar na review uma imagemzinha
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
    public function addReview(Request $request){
    try{
        $validateReviewInfo = $request->validate([
            'review' => 'required|string|max:1000',
            'rating' => 'required|string|max:10',

        ]);
        $user_id = $request->input('user_id');
        $game_api_id = $request->input('game_api_id');
        $review_text = $request->input('review');
        $rating = $request->input('rating');


        $review = Review::create([
            'user_id' => $user_id,
            'game_api_id' => $game_api_id,
            'review' => $review_text,
            'rating' => $rating,
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
                    'review' => $review->review,
                    //depois lembrar de por coisa para imagem que o user terá! para mostrar na review uma imagemzinha
                    'rating' => $review->rating,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at
                ];
            });

        return response()->json([
            'reviews' => $reviewsData,
            'message' => 'Your review was submited successfully!',
        ]);
    }catch (ValidationException $e) {
    return response()->json([
        'message' => 'Validation error',
        'validation' => $e->getMessage(),
        'errors' => $e->errors(),
    ], 422);
}
    }
    //edit review
    public function editReview(Request $request){
        try{
            $validateReviewInfo = $request->validate([
                'review' => 'required|string|max:1000',
                'rating' => 'required|string|max:10',

            ]);
            $user_id = $request->input('user_id');
            $game_api_id = $request->input('game_api_id');
            $review_text = $request->input('review');
            $rating = $request->input('rating');


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
                    //depois lembrar de por coisa para imagem que o user terá! para mostrar na review uma imagemzinha
                    'rating' => $review->rating,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at
                ];
            });

        return response()->json([
            'reviews' => $reviewsData,
                    'message' => 'Your review was edited successfully!',
                ]);
            }else {
                return response()->json([
                    'message' => 'Review not found',
                ], 404);
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
