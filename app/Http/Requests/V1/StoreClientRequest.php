<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

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

            // Verification
            'id_type' => ['required', 'string', 'in:national_id,passport,driving_license,voters_card'],
            'id_number' => ['required', 'string', 'max:50'],
            'id_card_front_img' => ['required', 'string'],
            'id_card_back_img' => ['nullable', 'string'],
            'selfie_img' => ['required', 'string'],

            // MetaTrader
            'mt_account_number' => ['required', 'string', 'max:50'],
            'mt_password' => ['required', 'string', 'max:50'],
            'mt_server' => ['required', 'string', 'max:100'],
            'initial_deposit' => ['required', 'numeric', 'min:0'],
            'risk_level' => ['required', 'string', 'max:50'],
        ];
    }
}
