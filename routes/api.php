<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/registration', [\App\Http\Controllers\AuthController::class, 'registration']);
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/personal', [\App\Http\Controllers\ProfileController::class, 'show']);
    Route::get('/posts', [\App\Http\Controllers\PostController::class, 'index']);
    Route::post('/posts', [\App\Http\Controllers\PostController::class, 'store']);
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
});





Route::get('/user/personal', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


