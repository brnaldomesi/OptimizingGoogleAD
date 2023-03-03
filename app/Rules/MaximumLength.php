<?php

namespace App\Rules;

use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Rule;

class MaximumLength implements Rule
{
    protected $permittedLengths = [

        'headline_1'    =>  30,
        'headline_2'    =>  30,
        'description'   =>  80,
        'path_1'        =>  15,
        'path_2'        =>  15,
    ];

    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        //no customisers
        if (! Str::contains($value, ['{', '}'])) {
            return iconv_strlen($value) <= $this->permittedLengths[$attribute];
        }

        //customiser with defaults
        if (Str::contains($value, '{') and Str::contains($value, ':') and Str::contains($value, '}')) {

            //default is between colon and closing } eg {keyword:default}
            $customiser = Str::after($value, '{');
            $customiser = Str::before($customiser, ':');

            $customiserLength = iconv_strlen($customiser) + 3; //for the {:}

            return iconv_strlen($value) <= $this->permittedLengths[$attribute] + $customiserLength;
        }

        //case 2 - customisers with no defaults
        $customiser = Str::after($value, '{');
        $customiser = Str::before($customiser, '}');

        $customiserLength = iconv_strlen($customiser) + 2; //for the {}

        return iconv_strlen($value) <= $this->permittedLengths[$attribute] + $customiserLength;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is too long.';
    }
}
