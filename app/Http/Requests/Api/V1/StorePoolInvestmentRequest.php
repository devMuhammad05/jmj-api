<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\MetaTraderPlatformType;
use App\Enums\RiskLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePoolInvestmentRequest extends FormRequest
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
            'pool_id' => ['required', 'uuid', 'exists:pools,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:20'],
            'account_name' => ['required', 'string', 'max:255'],
            'contribution' => ['required', 'numeric', 'min:1000'],
            'payment_proof_path' => ['required', 'url'],
            'terms_accepted' => ['required', 'boolean', 'accepted'],
            // MetaTrader account — all optional, but if account number is given the rest become required
            'mt_account_number' => ['nullable', 'string', 'max:50'],
            'mt_password' => ['nullable', 'required_with:mt_account_number', 'string'],
            'mt_server' => ['nullable', 'required_with:mt_account_number', 'string', 'max:100'],
            'platform_type' => ['nullable', 'required_with:mt_account_number', Rule::enum(MetaTraderPlatformType::class)],
            'initial_deposit' => ['nullable', 'required_with:mt_account_number', 'numeric', 'min:0'],
            'risk_level' => ['nullable', 'required_with:mt_account_number', Rule::enum(RiskLevel::class)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pool_id.exists' => 'The selected pool does not exist.',
            'contribution.min' => 'The minimum investment amount is $1,000.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
        ];
    }
}
