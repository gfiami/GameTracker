<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'game_api_id',
        'review',
        'rating',
        'game_name',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
