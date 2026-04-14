<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Models\User;
use App\Notifications\User\WelcomeNotification;
use App\Traits\Otp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class VerifyRegistrationOtpController extends ApiController
{
    use Otp;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'digits:6'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            return $this->errorResponse('User not found.', 404);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->successResponse('Email already verified.');
        }

        $verifyOtp = $this->verifyOtp($data['email'], $data['otp']);

        if (! $verifyOtp) {
            return $this->errorResponse('Invalid OTP provided.', 422);
        }

        $user->markEmailAsVerified();
        $user->notify(new WelcomeNotification);

        return $this->successResponse('OTP verified successfully. You can now login.');
    }
}
