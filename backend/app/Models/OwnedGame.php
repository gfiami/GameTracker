<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnedGame extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'game_api_id'];

}
