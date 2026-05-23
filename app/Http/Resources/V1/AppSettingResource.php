<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'app_name' => $this->app_name,
            'support_email' => $this->support_email,
            'support_phone' => $this->support_phone,
            'support_whatsapp' => $this->support_whatsapp,
            'address' => $this->address,
            'facebook_url' => $this->facebook_url,
            'twitter_url' => $this->twitter_url,
            'instagram_url' => $this->instagram_url,
            'deriv_referral_url' => $this->deriv_referral_url,
            'youtube_tutorials_url' => $this->youtube_tutorials_url,
        ];
    }
}
