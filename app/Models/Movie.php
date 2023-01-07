<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Rating;
class Movie extends Model
{
    use HasFactory;
    
    public function users(){
        return $this->belongsTo(User::class);
    }

    public function ratings(){
        return $this->hasMany(Rating::class);
    }
    
    // devuelve el listado de usuarios que puntuaron
    public function ratings_users(){
        return $this->ratings->belongsTo(User::class);
    }
}
