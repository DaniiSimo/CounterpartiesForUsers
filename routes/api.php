<?php

use App\Http\Controllers\CounterpartyController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource(name: 'users', controller: UserController::class)
    ->only(methods: ['store']);

Route::prefix('tokens')->group(callback: function () {
    Route::post(uri: '/', action: [TokenController::class, 'store']);
    Route::delete(uri: '/', action: [TokenController::class, 'destroy'])->middleware('auth:sanctum');
});

Route::apiResource(name: 'counterparties', controller: CounterpartyController::class)
    ->only(methods: ['store', 'index'])
    ->middleware('auth:sanctum');
