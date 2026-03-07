<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\IdType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class StoreVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        Gate::authorize('submit', \App\Models\Verification::class);

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
            'id_type' => ['required', new Enum(IdType::class)],
            'id_number' => ['required', 'string', 'max:50'],
            'id_card_front_img_url' => ['required', 'url'],
            'id_card_back_img_url' => ['nullable', 'url'],
            'selfie_img_url' => ['required', 'url'],
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
            'id_type' => 'The selected ID type is invalid.',
        ];
    }
}
