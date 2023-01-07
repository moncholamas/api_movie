<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\MovieController;
use App\Models\Movie;

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

Route::group(['middleware' => ['auth:sanctum']], function(){

    // cerrar sesion
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::post('/movies/rate/{id_movie}',[MovieController::class, 'rateMovie']);

    Route::get('/movies/rating', [MovieController::class, 'rating']);
});