<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id'      => ['required', 'integer', 'exists:plans,id'],
            'gateway_code' => ['required', 'string', 'exists:payment_gateways,code'],
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'reference'    => ['nullable', 'string', 'max:255'],
        ];
    }
}
