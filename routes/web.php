<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/posts', [AuthController::class, 'index']);

Route::get('/posts/{id}', [AuthController::class, 'show']);

Route::post('/posts', [AuthController::class, 'store']);

Route::put('/posts/{id}', [AuthController::class, 'update']);

Route::delete('/posts/{id}', [AuthController::class, 'destroy']);
Route::get('/', function () {
    return view('welcome');
});
