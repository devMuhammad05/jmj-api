<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Plan;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class MatchesPlanPrice implements ValidationRule
{
    public function __construct(
        private readonly int $planId,
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $plan = Plan::find($this->planId);

        if (! $plan) {
            $fail('The selected plan does not exist.');

            return;
        }

        if ((float) $value !== (float) $plan->price) {
            $fail('The amount does not match the selected plan price.');
        }
    }
}
