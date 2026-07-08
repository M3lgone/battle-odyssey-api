<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\CharacterController;

    Route::prefix('v1')->group(function () {
        Route::post('/auth/register', RegisterController::class);
        Route::post('/auth/login', LoginController::class);
    
    Route::middleware('auth:api')->group(function () {
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::patch('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::post('/auth/logout', LogoutController::class);  
        
        Route::get('/characters', [CharacterController::class, 'index']);
        Route::get('/characters/{character}', [CharacterController::class, 'show']);
    });

    Route::middleware(['auth:api', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::patch('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
    });

    Route::middleware(['auth:api', 'role:admin'])->group(function () {
        Route::post('/characters', [CharacterController::class, 'store']);
    });
});