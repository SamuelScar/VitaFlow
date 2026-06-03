<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;

/**
 * Valida que a nova senha informada é diferente da senha atual do usuário. Recebe o hash atual no construtor e compara via Hash::check.
 */
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
