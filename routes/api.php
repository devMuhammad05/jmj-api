<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\Pin\ChangePinController;
use App\Http\Controllers\Api\V1\Auth\Pin\ResetPinController;
use App\Http\Controllers\Api\V1\Auth\Pin\SetupPinController;
use App\Http\Controllers\Api\V1\Auth\Pin\VerifyPinController;
use App\Http\Controllers\Api\V1\MetaTraderCredentialController;
use App\Http\Controllers\Api\V1\PoolController;
use App\Http\Controllers\Api\V1\PoolInvestmentController;
use App\Http\Controllers\Api\V1\ProfitDistributionController;
use App\Http\Controllers\Api\V1\SignalController;
use App\Http\Controllers\Api\V1\TradingClassController;
use App\Http\Controllers\Api\V1\TradingStatsController;
use App\Http\Controllers\Api\V1\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function (): void {
    Route::get('/', fn () => 'API is active');

    // Auth Routes
    Route::prefix('auth')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'update']);
        });

        // PIN Routes
        Route::middleware(['auth:sanctum', 'throttle:10,1'])
            ->prefix('pin')
            ->group(function (): void {
                Route::post('/setup', SetupPinController::class);
                Route::post('/verify', VerifyPinController::class);
                Route::post('/change', ChangePinController::class);
                Route::post('/reset', ResetPinController::class);
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
        // Trading Class Routes (Learning Hub)
        Route::prefix('trading-classes')->group(function (): void {
            Route::get('/', [TradingClassController::class, 'index']);
            Route::get('/{tradingClass}', [
                TradingClassController::class,
                'show',
            ]);
        });

        Route::post('/metatrader-credentials', [
            MetaTraderCredentialController::class,
            'store',
        ]);

        Route::get('/trading-stats', [TradingStatsController::class, 'show']);

        Route::get('/verifications', [VerificationController::class, 'index']);
        Route::post('/verifications', [VerificationController::class, 'store']);

        // Pool Routes
        Route::get('/pools', [PoolController::class, 'index']);
        Route::get('/pools/{pool}', [PoolController::class, 'show']);

        // Pool Investment Routes
        Route::get('/pool-investments', [
            PoolInvestmentController::class,
            'index',
        ]);
        Route::post('/pool-investments', [
            PoolInvestmentController::class,
            'store',
        ]);
        Route::get('/pool-investments/{poolInvestment}', [
            PoolInvestmentController::class,
            'show',
        ]);

        // Profit Distribution Routes
        Route::get('/profit-distributions', [
            ProfitDistributionController::class,
            'index',
        ]);
        Route::get('/profit-distributions/{profitDistribution}', [
            ProfitDistributionController::class,
            'show',
        ]);
    });
});
