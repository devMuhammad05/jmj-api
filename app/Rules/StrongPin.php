<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class StrongPin implements ValidationRule
{
    /** @var list<string> */
    private const WEAK_PINS = [
        '0000',
        '1111',
        '2222',
        '3333',
        '4444',
        '5555',
        '6666',
        '7777',
        '8888',
        '9999',
        '1234',
        '2345',
        '3456',
        '4567',
        '5678',
        '6789',
        '9876',
        '8765',
        '7654',
        '6543',
        '5432',
        '4321',
        '3210',
        '0123',
        '1230',
        '1212',
        '2121',
        '1122',
        '2211',
        '1221',
        '0000',
        '0101',
        '1010',
        '1100',
        '0011',
    ];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail,
    ): void {
        $pin = str_pad((string) $value, 4, '0', STR_PAD_LEFT);

        if (in_array($pin, self::WEAK_PINS, true)) {
            $fail(
                'This PIN is too common. Please choose a less predictable PIN.',
            );
        }
    }
}
