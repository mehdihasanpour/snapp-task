<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CardNumberFormat implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $iranCardNumberRegex = [
            '/^(603799)\d+$/',
            '/^(621986)\d+$/',
            '/^(589210)\d+$/',
        ];

        $isValid = false;
        foreach ($iranCardNumberRegex as $regex) {
            if (preg_match($regex, $value)) {
                $isValid = true;
            }
        }
        
        $isValid ?: $fail('The :attribute format is not supported by iranian banks.');
    }
}
