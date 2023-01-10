<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\RatingController;
use App\Models\Movie;
use App\Models\Rating;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// endpoints para registro y login
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// endpoints publicos para peliculas
// obtiene todas las peliculas
Route::get('/movies', [MovieController::class, 'getAllMovies']);
// lista las 5 mas populares
Route::get('/movies/rating', [RatingController::class, 'rating']);


// obtiene los comentarios por pelicula
Route::get('/movies/commentaries/{id_movie}', [RatingController::class, 'getCommentaries']);


// valida que los usuarios esten logeados
Route::group(['middleware' => ['auth:sanctum']], function(){

    // cerrar sesion
    Route::get('/logout', [AuthController::class, 'logout']);

    // carga una pelicula nueva 
    Route::post('/movies' , [MovieController::class, 'uploadMovie'] );
    // actualiza una pelicula por id
    Route::put('/movies/{id_movie}', [MovieController::class, 'updateMovie']);
    // borra una pelicula 
    Route::delete('/movies/{id_movie}',[MovieController::class, 'remove']);
    // muestra todos los detalles por pelicula
    Route::get('/movies/details/{id_movie}', [MovieController::class, 'getDetails'] );
    // devuelve las peliculas favoritas por usuario
    Route::get('/movies/favorites', [MovieController::class, 'getFavorites']);
    // setea la pelicula elegida como favorita
    Route::post('/movies/favorite/{id_movie}', [MovieController::class, 'setFavorite'] );

    // puntuaciones por pelicula
    Route::post('/movies/rate/{id_movie}',[RatingController::class, 'rateMovie']);

});