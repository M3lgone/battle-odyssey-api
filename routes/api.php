<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\LoginController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', RegisterController::class);
    Route::post('/auth/login', LoginController::class);
});