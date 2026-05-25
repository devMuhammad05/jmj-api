<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\ReferralSource;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;

class ReferralSourceController extends ApiController
{
    public function index(): JsonResponse
    {
        $sources = collect(ReferralSource::cases())->map(fn (ReferralSource $source) => [
            'value' => $source->value,
            'label' => ucfirst($source->value),
        ]);

        return $this->successResponse('Referral sources retrieved successfully', $sources);
    }
}
