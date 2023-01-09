<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class RatingController extends Controller
{
    public function rateMovie(Request $request, $id_movie)
    {
        $user = Auth::user();
        // validar campos

        if ($id_movie) {

            $movie = Movie::where('id', $id_movie)->firstOrFail();
            if (!$movie) {
                return response()->json(['msg' => 'no existe la pelicula seleccionada']);
            }

            $rating = Rating::where('id_user', $user->id)->where('id_movie', $id_movie)->first();

            if (!$rating) {
                // rate + commentary 
                $rating = new Rating;
                $rating->id_movie = $id_movie;
                $rating->id_user = $user->id;
                if ($request->has('rate')) {
                    $rating->rating = $request->rating;
                }
                if ($request->has('commentary')) {
                    $rating->commentary = $request->commentary;
                }
                $rating->save();
                return response()->json($rating);
            } else {

                // si la valoracion ya existe, solo actualiza los datos
                if ($request->has('rate')) {
                    $rating->rating = $request->rating;
                }
                if ($request->has('commentary')) {
                    $rating->commentary = $request->commentary;
                }

                $rating->save();
                return response()->json($rating);
            }
        } else {
            return response()->json(['msg' => 'debe seleccionar una pelicula']);
        }
    }

    public function rating(Request $request)
    {
        // busca las 5 mas populares
        if ($request->has('most_popular')) {
            $maspopulares = Movie::where();
            return $maspopulares;
        }

        $maspopulares = Rating::select('title', 'gender', 'id_movie')
            ->selectRaw('AVG(rating) as rate')
            ->join('movies', 'ratings_user_movie.id_movie', '=', 'movies.id')
            ->groupBy('ratings_user_movie.id_movie')
            ->where('movies.deleted_at')
            ->limit(5)
            ->orderBy('rate', 'DESC')
            ->get();
        return $maspopulares;
    }

    public function getCommentaries(Request $request, $id_movie)
    {
        $commentaries = Rating::where('id_movie', $id_movie)->get();
        return response()->json($commentaries);
    }


    
}
