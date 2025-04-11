<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/users/{id}',[AuthController::class,'getUser']);

// Route::get('/test-api', function () {
//     return response()->json(['message' => 'API is working']);
// });