<?php

use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post(uri: '/users', action: [UserController::class, 'store']);
Route::prefix('/tokens')->group(callback: function () {
    Route::post(uri: '/', action: [TokenController::class, 'store']);
    Route::delete(uri: '/', action: [TokenController::class, 'destroy'])->middleware('auth:sanctum');
});
