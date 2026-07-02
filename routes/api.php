<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\UserController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', RegisterController::class);
    Route::post('/auth/login', LoginController::class);
    
    Route::middleware('auth:api')->group(function () {
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::post('/auth/logout', LogoutController::class);    
    });
});