<?php

namespace App\Rules;

use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Rule;

class NoDoublePunctuation implements Rule
{
    public function passes($attribute, $value)
    {
        $forbidden = [
            ',,',
            '..',
            '!!',
            '??',
            '**',
            '--',
            '__',
            '##',
            '@@',
        ];

        if (Str::contains($value, $forbidden)) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return ':attribute includes double punctuation.';
    }
}
