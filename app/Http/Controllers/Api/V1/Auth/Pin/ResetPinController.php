<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth\Pin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Auth\ResetPinRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

final class ResetPinController extends ApiController
{
    public function __invoke(ResetPinRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! Hash::check($request->string('password')->toString(), $user->password)) {
            return $this->errorResponse('Account password is incorrect.', 401);
        }

        $user->update([
            'pin' => Hash::make((string) $request->integer('pin')),
            'pin_set_at' => now(),
            'pin_attempts' => 0,
            'pin_locked_until' => null,
        ]);

        return $this->successResponse('PIN reset successfully.');
    }
}
