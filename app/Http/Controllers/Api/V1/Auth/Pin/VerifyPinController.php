<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth\Pin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Auth\VerifyPinRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class VerifyPinController extends ApiController
{
    private const MAX_ATTEMPTS = 5;

    private const LOCKOUT_MINUTES = 30;

    public function __invoke(VerifyPinRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->isPinSet()) {
            return $this->errorResponse('PIN has not been configured yet.', 422);
        }

        if ($user->isPinLocked()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many failed attempts. Try again '.$user->pin_locked_until->diffForHumans().'.',
            ], 429);
        }

        if (! $user->verifyPin($request->integer('pin'))) {
            $attempts = $user->pin_attempts + 1;
            $update = ['pin_attempts' => $attempts];

            if ($attempts >= self::MAX_ATTEMPTS) {
                $update['pin_locked_until'] = now()->addMinutes(self::LOCKOUT_MINUTES);
                $update['pin_attempts'] = 0;

                $user->update($update);

                return response()->json([
                    'status' => 'error',
                    'message' => 'Too many failed attempts. Your PIN has been locked for 30 minutes.',
                ], 429);
            }

            $user->update($update);

            $remaining = self::MAX_ATTEMPTS - $attempts;

            return $this->errorResponse(
                "Incorrect PIN. {$remaining} ".($remaining === 1 ? 'attempt' : 'attempts').' remaining.',
                401
            );
        }

        $user->update(['pin_attempts' => 0, 'pin_locked_until' => null]);

        return $this->successResponse('PIN verified successfully.');
    }
}
