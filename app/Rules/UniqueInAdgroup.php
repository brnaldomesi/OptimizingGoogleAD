<?php

namespace App\Rules;

use App\Models\Advert;
use Illuminate\Contracts\Validation\Rule;

class UniqueInAdgroup implements Rule
{
    public function passes($attribute, $value)
    {
        $existingAdvert = Advert::where('adgroup_id', request('adgroup_id'))
            ->where('headline_1', request('headline_1'))
            ->where('headline_2', request('headline_2'))
            ->where('description', request('description'))
            ->where('path_1', request('path_1'))
            ->where('path_2', request('path_2'))
            ->first();

        if ($existingAdvert) {
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
        return 'The advert cannot be exactly the same as any other ad in this ad group.';
    }
}
