<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\UpdateExpoPushTokenRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpoPushTokenController extends ApiController
{
    public function update(UpdateExpoPushTokenRequest $request): JsonResponse
    {
        $request->user()->update([
            'expo_push_token' => $request->validated('expo_push_token'),
        ]);

        return $this->successResponse('Push token updated.');
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->user()->update(['expo_push_token' => null]);

        return $this->successResponse('Push token removed.');
    }
}
