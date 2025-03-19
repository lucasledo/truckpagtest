<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\ApiTokenIsValid;

Route::prefix('products')->group(function () {
    Route::get('', [ProductController::class, 'index']);
    Route::get('{code}', [ProductController::class, 'show']);
    Route::delete('{code}', [ProductController::class, 'destroy']);
    Route::put('{code}', [ProductController::class, 'update']);
})->middleware(ApiTokenIsValid::class);
