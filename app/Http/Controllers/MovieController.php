<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    // devuelve todas las peliculas

    public function getAllMovies(Request $request)
    {
        $movies = Movie::all();
        return response()
            ->json($movies);
    }

    public function rateMovie(Request $request, $id_movie)
    {
        // validar campos
        
        if ($id_movie) {
            $movie = Movie::where('id', $id_movie)->firstOrFail();
            if( ! $movie ) {
                return response()->json(['msg' => 'no existe la pelicula seleccionada']);
            }

            $user = Auth::user();
            // rate + commentary 
            $rating = new Rating;
            $rating->id_movie = $id_movie;
            $rating->id_user = $user->id;
            $rating->commentary = $request->commentary;
            $rating->rating = $request->rating;
            $rating->save();
            return response()->json($rating);
        } else {
            return response()->json(['msg' => 'debe seleccionar una pelicula']);
        }
    }

    public function rating (Request $request){
        // busca las 5 mas populares
        if($request->has('most_popular')){
            $maspopulares = Movie::where();
            return $maspopulares;
        }

        $maspopulares = Rating::select('title', 'gender')
            ->selectRaw('AVG(rating) as rate')
            ->join('movies','ratings_user_movie.id_movie','=','movies.id')
            ->groupBy('ratings_user_movie.id_movie')
            ->limit(5)
            ->orderBy('rate', 'DESC')
            ->get()
        ;
        return $maspopulares;
    }
}
