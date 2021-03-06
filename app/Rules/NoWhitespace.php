<?php

namespace Servidor\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class NoWhitespace implements Rule
{
    public function passes($attribute, $value): bool
    {
        unset($attribute);

        return !Str::contains($value, ["\t", "\n", ' ']);
    }

    public function message(): string
    {
        return 'The :attribute cannot contain whitespace or newlines.';
    }
}
