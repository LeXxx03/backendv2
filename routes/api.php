<?php

use Illuminate\Support\Facades\Route;

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

// Api útvonalak regisztrálása

use App\Http\Controllers\AuthController;

//Regisztrációs útvonal
Route::post('/register', [AuthController::class, 'register']);

//Bejelentkezési útvonal
Route::post('/login', [AuthController::class, 'login']);
