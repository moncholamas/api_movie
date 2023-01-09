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


Route::get('/test', [UserController::class, 'index']);

// endpoints para registro y login
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// endpoints movies
Route::get('/movies', [MovieController::class, 'getAllMovies']);
// lista las 5 mas populares
Route::get('/movies/rating', [RatingController::class, 'rating']);
// detalles por pelicula
Route::get('/movies/details/public/{id_movie}', [MovieController::class, 'getDetails'] );

// obtiene los comentarios por pelicula
Route::get('/movies/commentaries/{id_movie}', [RatingController::class, 'getCommentaries']);

Route::group(['middleware' => ['auth:sanctum']], function(){

    // cerrar sesion
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::post('/movies/rate/{id_movie}',[RatingController::class, 'rateMovie']);

    Route::delete('/movies/{id_movie}',[MovieController::class, 'remove']);

    Route::post('/movies' , [MovieController::class, 'uploadMovie'] );

    Route::get('/movies/details/{id_movie}', [MovieController::class, 'getDetails'] );

    Route::post('/movies/favorite/{id_movie}', [MovieController::class, 'setFavorite'] );

    Route::get('/movies/favorites', [MovieController::class, 'getFavorites']);

    Route::put('/movies/{id_movie}', [MovieController::class, 'updateMovie']);
});