<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

use App\Enums\ReferralSource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

final class RegisterUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'country' => ['nullable', 'string', 'max:255'],
            'referral_source' => ['nullable', new Enum(ReferralSource::class)],
            'referral_code' => ['nullable', 'string', 'size:8', 'exists:users,referral_code'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
