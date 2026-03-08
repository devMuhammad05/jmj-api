<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\MetaTraderCredentialController;
use App\Http\Controllers\Api\V1\SignalController;
use App\Http\Controllers\Api\V1\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function (): void {
    Route::get('/', fn() => 'API is active');

    // Auth Routes
    Route::prefix('auth')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'update']);
        });
    });

    // Public Signal Routes (no authentication required)
    Route::prefix('signals')->group(function (): void {
        Route::get('/', [SignalController::class, 'index']);
        Route::get('/active', [SignalController::class, 'active']);
        Route::get('/statistics', [SignalController::class, 'statistics']);
        Route::get('/{signal}', [SignalController::class, 'show']);
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/metatrader-credentials', [MetaTraderCredentialController::class, 'store']);

        Route::get('/verifications', [VerificationController::class, 'index']);
        Route::post('/verifications', [VerificationController::class, 'store']);
    });
});
