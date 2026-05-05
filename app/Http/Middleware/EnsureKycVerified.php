<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\VerificationStatus;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureKycVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if (! $user || ! $user->verification || $user->verification->status !== VerificationStatus::APPROVED) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your identity has not been verified. Please complete KYC verification to continue.',
            ], 403);
        }

        return $next($request);
    }
}
