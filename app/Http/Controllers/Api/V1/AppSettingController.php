<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\V1\AppSettingResource;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;

class AppSettingController extends ApiController
{
    public function show(): JsonResponse
    {
        $settings = AppSetting::getSettings();

        return $this->successResponse('App settings retrieved successfully', new AppSettingResource($settings));
    }
}
