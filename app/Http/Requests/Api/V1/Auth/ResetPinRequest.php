<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

use App\Rules\StrongPin;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class ResetPinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string'],
            'pin' => ['required', 'integer', 'digits:4', 'same:pin_confirmation', new StrongPin],
            'pin_confirmation' => ['required', 'integer', 'digits:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'pin.same' => 'PIN confirmation does not match.',
        ];
    }
}
