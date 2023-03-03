<?php

namespace App\Rules;

use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Rule;

class OnlyPermittedCharactersInPath implements Rule
{
    public function passes($attribute, $value)
    {
        $forbidden = [
            ' ',
            '$',
            '!',
            '?',
            '*',
            '#',
            '@',
        ];

        if (Str::contains($value, $forbidden)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute includes non-permitted characters.';
    }
}
