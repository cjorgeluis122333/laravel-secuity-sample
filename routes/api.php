<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas de autenticacion (sin proteccion)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Rutas protegidas por autenticacion
Route::middleware('auth:sanctum')->group(function () {
    // Rutas de autenticacion
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh-token', [AuthController::class, 'refreshToken']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Rutas de posts
    Route::apiResource('posts', PostController::class);

    // Rutas de comentarios
    Route::apiResource('comments', CommentController::class);
});

// Ruta para obtener el usuario autenticado (alternativa)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
