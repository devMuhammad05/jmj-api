<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Auth\LoginUserRequest;
use App\Http\Requests\Api\V1\Auth\RegisterUserRequest;
use App\Http\Requests\Api\V1\Auth\UpdateProfileRequest;
use App\Models\User;
use App\Traits\Otp;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\JsonResponse;

final class AuthController extends ApiController
{
    use Otp;

    public function __construct(
        private readonly Hasher $hasher,
        private readonly AuthManager $authManager,
    ) {}

    /**
     * Handle a registration request for the application.
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'country' => $request->country,
            'password' => $this->hasher->make($request->password),
        ]);

        $this->sendOtp($user->email);

        return $this->successResponse(
            'Registration successful. Please check your email for OTP verification.',
            [
                'email' => $user->email,
            ],
            201,
        );
    }

    /**
     * Handle a login request for the application.
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        if (! $this->authManager->attempt($request->only('email', 'password'))) {
            return $this->errorResponse('Invalid login details', 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if (! $user->hasVerifiedEmail()) {
            $this->sendOtp($user->email);

            return $this->errorResponse(
                'Email not verified. A new OTP has been sent to your email.',
                403
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse('User logged in successfully', [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'pin_configured' => $user->isPinSet(),
        ]);
    }

    /**
     * Handle a logout request for the application.
     */
    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = $this->authManager->user();

        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $user->currentAccessToken();

        $token->delete();

        return $this->successResponse('User logged out successfully');
    }

    /**
     * Get the authenticated user.
     */
    public function me(): JsonResponse
    {
        return $this->successResponse(
            'User profile retrieved successfully',
            $this->authManager->user(),
        );
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->authManager->user();

        $user->update($request->validated());

        return $this->successResponse(
            'User profile updated successfully',
            $user,
        );
    }
}
