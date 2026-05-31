<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\MetaTraderPlatformType;
use App\Enums\PoolInvestmentStatus;
use App\Enums\RiskLevel;
use App\Models\Pool;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'contribution' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_gateway_id' => ['required', 'integer', 'exists:payment_gateways,id'],
            'payment_proof_url' => ['required', 'url'],
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
            'contribution.min' => 'The contribution amount must be at least 0.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
        ];
    }

    /**
     * Add post-validation checks that require database lookups.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $pool = Pool::find($this->input('pool_id'));

            if (! $pool) {
                return;
            }

            $contribution = (float) $this->input('contribution', 0);

            if ($contribution < (float) $pool->each_contribution_amount) {
                $validator->errors()->add(
                    'contribution',
                    "The minimum contribution for this pool is \${$pool->each_contribution_amount}."
                );
            }

            $activeApplications = $pool->poolInvestments()
                ->whereIn('status', [PoolInvestmentStatus::PENDING, PoolInvestmentStatus::VERIFIED])
                ->count();

            if ($activeApplications >= $pool->number_of_investors) {
                $validator->errors()->add(
                    'pool_id',
                    'This pool has reached its maximum capacity and is no longer accepting applications.'
                );
            }
        });
    }
}
