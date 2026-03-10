<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

use App\Rules\StrongPin;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class ChangePinRequest extends FormRequest
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
            'current_pin' => ['required', 'integer', 'digits:4'],
            'pin' => ['required', 'integer', 'digits:4', 'same:pin_confirmation', 'different:current_pin', new StrongPin],
            'pin_confirmation' => ['required', 'integer', 'digits:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'pin.same' => 'PIN confirmation does not match.',
            'pin.different' => 'New PIN must be different from the current PIN.',
        ];
    }
}
