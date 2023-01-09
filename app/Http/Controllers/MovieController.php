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
    private $generos = ['comedia', 'drama', 'terror', 'infantil', 'documentales'];
    // devuelve todas las peliculas
    public function getAllMovies(Request $request)
    {
        $movies = Movie::select('id', 'title', 'gender', 'created_at')->orderBy('created_at', 'DESC')->get();
        return response()
            ->json($movies);
    }

    // borra una pelicula por id
    public function remove(Request $request, $id_movie)
    {
        Movie::where('id', $id_movie)->delete(); // soft delete
        return response()->json(['msg' => 'pelicula borrada correctamente']);
    }

    // agrega una nueva pelicula
    public function uploadMovie(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'string|max:400|',
            'gender' => ['required', Rule::in($this->generos)]
        ]);


        if ($validator->fails()) {
            // personalizar las respuestas
            return response()->json(['error' => $validator->messages()], 400);
        }

        // genera una nueva instancia de movie
        $movie = new Movie;
        $movie->title = $request->title;
        $movie->description = $request->description;
        $movie->gender = $request->gender;
        $movie->id_user = $user->id;
        $movie->save();

        return response()
            ->json($movie);
    }

    public function updateMovie(Request $request, $id_movie)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'string|max:400|',
            'gender' => ['required', Rule::in($this->generos)]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }


        $movie = Movie::find($id_movie);

        if (!$movie) {
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
        $request->path(); // existe un endpoint privado que entrega mas informacion

        if (!$alldetails) {
            // si una pelicula con ese id
            return response()->json(['not_found' => 'no existe ninguna pelicula con el id ingresado']);
        }

        $detallesRating = Rating::where('id_movie', $id_movie)->where('id_user', $user->id)->first();
        $alldetails->rating = $detallesRating;

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

    public function getFavorites()
    {
        $favorites = Movie::whereHas('ratings', function( $q ) {
            $user = Auth::user();
            $q->where(['favorite' => true])->where(['id_user' => $user->id]);
        }) ->get();

        return response()->json($favorites);
    }


    public function setFavorite(Request $request, $id_movie)
    {
        $user = Auth::user();

        $movie = Movie::find($id_movie);
        if (!$movie) {
            return response()->json(['msg' => 'no existe pelicula con el id ingresado']);
        }

        // verifica si ya se dejo un comentario o un puntaje
        $valoracion = Rating::where([['id_user', '=', $user->id], ['id_movie', '=', $id_movie]])->get();
        if ( count($valoracion) === 0) {

            $rating = new Rating;
            $rating->id_movie = (int)$id_movie;
            $rating->id_user = (int)$user->id;
            $rating->favorite = (boolean)$request->favorite;
            $rating->save();

        } else {

            $toUpdate = Rating::find($valoracion[0]->id);
            $toUpdate->favorite = $request->favorite;
            $toUpdate->save();

        }
        return response()->json(['msg' => 'actualizada correctamente']);
    }
}
