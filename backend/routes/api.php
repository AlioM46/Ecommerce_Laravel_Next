<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/test', function() {
    return response()->json(['message' => 'API is working']);
});

Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);
Route::post('/refresh/{userId}', [AuthController::class,'refresh']);
Route::post('/logout', [AuthController::class,'logout']);
