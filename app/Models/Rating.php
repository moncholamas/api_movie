<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Movie;

class Rating extends Model
{
    use HasFactory;
    
    public function usesrs(){
        return $this->belongsTo(User::class);
    }

    public function movies(){
        return $this->belongsTo(Movie::class);
    }

    protected $table = 'ratings_user_movie';
}
