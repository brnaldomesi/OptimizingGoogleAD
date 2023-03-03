<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EarlyAccess implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $emails = \App\Models\EmailSubscriber::where('is_banned', 0)->pluck('email')->toarray();
        return in_array($value,$emails);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The email address provided isn\'t on the guest list.';
    }
}
