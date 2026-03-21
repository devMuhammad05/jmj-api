<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

final class GetOtpController extends ApiController
{
    /**
     * Get OTP for a given email (development/testing only).
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $otp = Cache::get($request->query('email'));

        if (! $otp) {
            return $this->errorResponse('No OTP found for this email.', 404);
        }

        return $this->successResponse('OTP retrieved successfully.', [
            'email' => $request->email,
            'otp' => (string) $otp,
        ]);
    }
}
