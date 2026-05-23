<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\PayoutAccountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayoutAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(PayoutAccountType::class)],
            'label' => ['nullable', 'string', 'max:100'],
            'is_default' => ['boolean'],
            // Bank fields
            'bank_name' => ['required_if:type,bank', 'nullable', 'string', 'max:255'],
            'account_name' => ['required_if:type,bank', 'nullable', 'string', 'max:255'],
            'account_number' => ['required_if:type,bank', 'nullable', 'string', 'max:20'],
            // Crypto fields
            'wallet_address' => ['required_if:type,crypto', 'nullable', 'string', 'max:255'],
            'network' => ['required_if:type,crypto', 'nullable', 'string', 'max:100'],
            'coin' => ['required_if:type,crypto', 'nullable', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'bank_name.required_if' => 'Bank name is required for bank accounts.',
            'account_name.required_if' => 'Account name is required for bank accounts.',
            'account_number.required_if' => 'Account number is required for bank accounts.',
            'wallet_address.required_if' => 'Wallet address is required for crypto accounts.',
            'network.required_if' => 'Network is required for crypto accounts.',
            'coin.required_if' => 'Coin is required for crypto accounts.',
        ];
    }
}
