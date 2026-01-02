<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;

// Authenticate Routs
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected rout (Need the token for have access)
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routs
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh-token', [AuthController::class, 'refreshToken']);
    Route::get('/auth/me', [AuthController::class, 'getUserInfo']);
    // Posts Routs
    Route::apiResource('posts', PostController::class);
    // Comments routs
    Route::apiResource('comments', CommentController::class);
});

// Ruta para obtener el usuario autenticado (alternativa)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
