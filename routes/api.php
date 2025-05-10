<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('profile', [AuthController::class, 'profile']);
//     Route::post('logout', [AuthController::class, 'logout']);
// });

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::get('refresh', [AuthController::class, 'refreshToken']);
    Route::post('logout', [AuthController::class, 'logout']);
});
