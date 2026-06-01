<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

class DifferentFromCurrentPassword implements ValidationRule
{
    public function __construct(
        private readonly string $currentPasswordHash,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== null && Hash::check((string) $value, $this->currentPasswordHash)) {
            $fail('A nova senha deve ser diferente da senha atual.');
        }
    }
}
