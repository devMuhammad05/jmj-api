<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\RiskLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreMetaTraderCredentialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mt_account_number' => ['required', 'string', 'max:50'],
            'mt_password' => ['required', 'string', 'max:50'],
            'mt_server' => ['required', 'string', 'max:100'],
            'initial_deposit' => ['required', 'numeric', 'min:0'],
            'risk_level' => ['required', new Enum(RiskLevel::class)],
            'payment_gateway_id' => ['required', 'exists:payment_gateways,id'],
            'payment_proof_url' => ['required', 'url'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
        ];
    }
}
