<?php

namespace App\Policies;

use App\User;
use App\Models\Account;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the account.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Account  $account
     * @return mixed
     */
    public function view(User $user, Account $account)
    {
        return ($user->id === $account->user_id);
    }

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
