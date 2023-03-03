<?php

namespace App\Policies;

use App\User;
use App\Models\Account;
use Illuminate\Auth\Access\HandlesAuthorization;

class Registered
{
    use HandlesAuthorization;

    /**
     * Determine whether the user has successfully registered (their accounts have downloaded).
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return true;
        if (Auth::user()->accounts->count() == 0) {
            return false;
        }

        return true;

    }

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
