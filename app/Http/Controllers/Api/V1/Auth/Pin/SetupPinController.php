<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth\Pin;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\Auth\SetupPinRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

final class SetupPinController extends ApiController
{
    public function __invoke(SetupPinRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->isPinSet()) {
            return $this->errorResponse(
                'PIN is already configured.',
                422
            );
        }

        $user->update([
            'pin' => Hash::make((string) $request->integer('pin')),
            'pin_set_at' => now(),
        ]);

        return $this->successResponse('PIN set up successfully.');
    }
}
