<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => ['required', 'integer', Rule::exists('plans', 'id')->where('is_active', true)],
            'gateway_code' => ['required', 'string', Rule::exists('payment_gateways', 'code')->where('is_active', true)],
            'payment_proof' => ['required', 'string', 'url'],
        ];
    }
}
