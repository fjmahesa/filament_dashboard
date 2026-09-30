<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Middleware\CheckApiKey;

Route::middleware([CheckApiKey::class])->group(function () {
    Route::post('/contact', [ContactController::class, 'store']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
