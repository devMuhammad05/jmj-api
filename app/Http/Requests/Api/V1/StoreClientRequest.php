<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\RiskLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreClientRequest extends FormRequest
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
            // Client
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:clients,email'],
            'phone' => ['required', 'string', 'max:20'],

            // MetaTrader
            'mt_account_number' => ['required', 'string', 'max:50'],
            'mt_password' => ['required', 'string', 'max:50'],
            'mt_server' => ['required', 'string', 'max:100'],
            'initial_deposit' => ['required', 'numeric', 'min:0'],
            'risk_level' => ['required', new Enum(RiskLevel::class)],
        ];
    }
}
