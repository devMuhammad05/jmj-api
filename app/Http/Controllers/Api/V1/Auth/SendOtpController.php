<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use App\Traits\Otp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SendOtpController extends ApiController
{
    use Otp;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->errorResponse('We could not find an account with the provided email.', 404);
        }

        $sendOtp = $this->sendOtp($request->email);

        if (! $sendOtp['success']) {
            return $this->errorResponse('Failed to send OTP. Please try again.', 500);
        }

        return $this->successResponse('OTP has been sent to your email.');
    }
}
