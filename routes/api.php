<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', RegisterController::class);
});