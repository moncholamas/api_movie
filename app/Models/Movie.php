<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Rating;
use Illuminate\Database\Eloquent\SoftDeletes;
class Movie extends Model
{
    use HasFactory, SoftDeletes;
    
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
