<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
class MovieController extends Controller
{
    // devuelve todas las peliculas
    
    public function getAllMovies(Request $request) {
        $movies = Movie::all();
        return response()
            ->json($movies);
    }
}
