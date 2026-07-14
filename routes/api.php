<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\CharacterController;
use App\Http\Controllers\Api\EnemyController;
use App\Http\Controllers\Api\SkillController;

    Route::prefix('v1')->group(function () {
        Route::post('/register', RegisterController::class);
        Route::post('/login', LoginController::class);
    
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [UserController::class, 'show']);
        Route::put('/me', [UserController::class, 'update']);
        Route::delete('/me', [UserController::class, 'destroy']);
        Route::post('/logout', LogoutController::class);  
        
        Route::get('/characters', [CharacterController::class, 'index']);
        Route::get('/characters/{character}', [CharacterController::class, 'show']);
    });

    Route::middleware(['auth:api', 'role:admin'])->group(function () {
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::get('/users/{user}', [AdminUserController::class, 'show']);
        Route::put('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);

        Route::post('/characters', [CharacterController::class, 'store']);
        Route::put('/characters/{character}', [CharacterController::class, 'update']);
        Route::delete('/characters/{character}', [CharacterController::class, 'destroy']);

        Route::get('/enemies', [EnemyController::class, 'index']);
        Route::get('/enemies/{enemy}', [EnemyController::class, 'show']);
        Route::post('/enemies', [EnemyController::class, 'store']);
        Route::put('/enemies/{enemy}', [EnemyController::class, 'update']);
        Route::delete('/enemies/{enemy}', [EnemyController::class, 'destroy']);

        Route::get('/skills', [SkillController::class, 'index']);
    });
});