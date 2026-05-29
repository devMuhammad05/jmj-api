<?php

use App\Http\Controllers\Api\V1\AppSettingController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\GetOtpController;
use App\Http\Controllers\Api\V1\Auth\Pin\ChangePinController;
use App\Http\Controllers\Api\V1\Auth\Pin\ResetPinController;
use App\Http\Controllers\Api\V1\Auth\Pin\SetupPinController;
use App\Http\Controllers\Api\V1\Auth\Pin\VerifyPinController;
use App\Http\Controllers\Api\V1\Auth\SendOtpController;
use App\Http\Controllers\Api\V1\Auth\VerifyRegistrationOtpController;
use App\Http\Controllers\Api\V1\ClientPortfolioController;
use App\Http\Controllers\Api\V1\ExpoPushTokenController;
use App\Http\Controllers\Api\V1\MetaTraderCredentialController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PaymentGatewayController;
use App\Http\Controllers\Api\V1\PayoutAccountController;
use App\Http\Controllers\Api\V1\PlanController;
use App\Http\Controllers\Api\V1\PoolController;
use App\Http\Controllers\Api\V1\PoolInvestmentController;
use App\Http\Controllers\Api\V1\ProfitDistributionController;
use App\Http\Controllers\Api\V1\RateController;
use App\Http\Controllers\Api\V1\ReferralSourceController;
use App\Http\Controllers\Api\V1\SignalController;
use App\Http\Controllers\Api\V1\SubscriptionController;
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
        Route::post('/verify-registration-otp', VerifyRegistrationOtpController::class);
        Route::post('/send-otp', SendOtpController::class);
        Route::get('/get-otp', GetOtpController::class);

        Route::middleware(['auth:sanctum', 'verified.email'])->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'update']);
        });

        // PIN Routes
        Route::middleware(['auth:sanctum', 'verified.email', 'throttle:10,1'])
            ->prefix('pin')
            ->group(function (): void {
                Route::post('/setup', SetupPinController::class);
                Route::post('/verify', VerifyPinController::class);
                Route::post('/change', ChangePinController::class);
                Route::post('/reset', ResetPinController::class);
            });
    });

    // App Settings (public)
    Route::get('/app-settings', [AppSettingController::class, 'show']);

    // Referral Sources (public)
    Route::get('/referral-sources', [ReferralSourceController::class, 'index']);

    // Plans (public)
    Route::get('/plans', [PlanController::class, 'index']);

    // Rates (public)
    Route::get('/rates', [RateController::class, 'index']);
    Route::get('/rates/{key}', [RateController::class, 'show']);

    Route::middleware(['auth:sanctum', 'verified.email'])->group(function (): void {
        // Signal Routes
        Route::prefix('signals')->group(function (): void {
            Route::get('/', [SignalController::class, 'index']);
            Route::get('/active', [SignalController::class, 'active']);
            Route::get('/statistics', [SignalController::class, 'statistics']);
            Route::get('/{signal}', [SignalController::class, 'show']);
        });

        // Client Portfolio Route
        Route::get('/client-portfolio', [ClientPortfolioController::class, 'index']);

        // Trading Class Routes (Learning Hub)
        Route::prefix('trading-classes')->group(function (): void {
            Route::get('/', [TradingClassController::class, 'index']);
            Route::get('/{tradingClass}', [TradingClassController::class, 'show']);
        });

        if (app()->isProduction()) {
            Route::middleware('verified.kyc')->group(function (): void {
                Route::post('/metatrader-credentials', [MetaTraderCredentialController::class, 'store']);
            });
        }

        if (app()->isLocal()) {
            Route::post('/metatrader-credentials', [MetaTraderCredentialController::class, 'store']);
        }

        Route::get('/trading-stats', [TradingStatsController::class, 'show']);

        Route::get('/verifications', [VerificationController::class, 'index']);
        Route::post('/verifications', [VerificationController::class, 'store']);

        // Pool Routes
        Route::get('/pools', [PoolController::class, 'index']);
        Route::get('/pools/{pool}', [PoolController::class, 'show']);

        // Pool Investment Routes
        Route::get('/pool-investments', [PoolInvestmentController::class, 'index']);
        Route::post('/pool-investments', [PoolInvestmentController::class, 'store']);
        Route::get('/pool-investments/{poolInvestment}', [PoolInvestmentController::class, 'show']);

        // Profit Distribution Routes
        Route::get('/profit-distributions', [ProfitDistributionController::class, 'index']);
        Route::get('/profit-distributions/{profitDistribution}', [ProfitDistributionController::class, 'show']);

        // Payout Account Routes
        Route::get('/payout-accounts', [PayoutAccountController::class, 'index']);
        Route::post('/payout-accounts', [PayoutAccountController::class, 'store']);

        // Payment Gateway Routes
        Route::get('/payment-gateways', [PaymentGatewayController::class, 'index']);

        // Payment Routes
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::get('/payments/{payment}', [PaymentController::class, 'show']);

        // Subscription Routes
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);
        Route::get('/subscriptions/current', [SubscriptionController::class, 'current']);
        Route::get('/subscriptions', [SubscriptionController::class, 'index']);

        // Expo Push Token Routes
        Route::put('/expo-push-token', [ExpoPushTokenController::class, 'update']);
        Route::delete('/expo-push-token', [ExpoPushTokenController::class, 'destroy']);

        // Notification Routes
        Route::prefix('notifications')->group(function (): void {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        });
    });
});
