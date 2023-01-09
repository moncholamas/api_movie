<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class MovieController extends Controller
{
    // devuelve todas las peliculas

    public function getAllMovies(Request $request)
    {
        $movies = Movie::select('id', 'title', 'gender', 'created_at')->orderBy('created_at', 'DESC')->get();
        return response()
            ->json($movies);
    }


    public function remove(Request $request, $id_movie)
    {
        Movie::where('id', $id_movie)->delete(); // soft delete
        return response()->json(['msg' => 'pelicula borrada correctamente']);
    }

    public function uploadMovie(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'string|max:400|',
            'gender' => ['required', Rule::in(['comedia', 'drama', 'terror', 'infantil', 'documentales'])]
        ]);


        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }

        $user = Auth::user();

        $movie = new Movie;
        $movie->title = $request->title;
        $movie->description = $request->description;
        $movie->gender = $request->gender;
        $movie->id_user = $user->id;
        $movie->save();

        return response()
            ->json($movie);
    }

    public function updateMovie(Request $request, $id_movie){
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'string|max:400|',
            'gender' => ['required', Rule::in(['comedia', 'drama', 'terror', 'infantil', 'documentales'])]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }


        $movie = Movie::find($id_movie);
        
        if ( ! $movie ) {
            return response()->json(['not_found' => 'no existe ninguna pelicula con el id ingresado']);
        }

        $movie->title = $request->title;
        $movie->description = $request->description;
        $movie->gender = $request->gender;
        $movie->id_user = $user->id;
        $movie->save();

        return response()->json(['msg' => 'pelicula actualizada correctamente']);
    }

    public function getDetails(Request $request, $id_movie)
    {
        $user = Auth::user();
        $alldetails = Movie::find($id_movie);
        $request->path();

        if (!$alldetails) {
            // si una pelicula con ese id
            return response()->json(['not_found' => 'no existe ninguna pelicula con el id ingresado']);
        }

        $detallesRating =Rating::where('id_movie', $id_movie)->where('id_user', $user->id);        
        $alldetails->rating= $detallesRating;

        // verifica si es propietario de la pelicula 
        if (!str_contains($request->path(), 'public')) {
            if ($user->id === $alldetails->id_user) {
                $alldetails->owner = true;
                return response()->json($alldetails);
            }
        }

        // si no es el propietario busca los datos
        $owner = User::where('id', $alldetails->id_user)->first();
        $alldetails->user = $owner;

        return response()->json($alldetails);
    }

    public function getFavorites(){
        $user = Auth::user();
        $favorites = Rating::where([['id_user', '=',$user->id],['favorite','=',true]]);
        return response()->json($favorites);
    }


    public function setFavorite(Request $request, $id_movie)
    {
        $user = Auth::user();
        $favorite = true; 

        $movie = Movie::where('id', $id_movie)->first();
        if (!$movie) {
            return response()->json(['msg' => 'no existe pelicula con el id ingresado']);
        }
    

        if ($request->has('remove')) {
            $favorite = false;
        } 

        // verifica si ya se dejo un comentario o un puntaje
        $valoracion = Rating::where([ ['id_user','=', $user->id] , ['id_movie','=', $id_movie] ])->get();

        if ( ! $valoracion ) {
            $rating = new Rating;
            $rating->id_movie = $id_movie;
            $rating->id_user = $user->id;
            $rating->favorite = $favorite;
            $rating->save();
            return response()->json(['msg' => 'actualizada correctamente']);
        } else {
            $valoracion->favorite = $favorite;
            return response()->json(['msg' => 'actualizada correctamente']);
        }
    }
}
