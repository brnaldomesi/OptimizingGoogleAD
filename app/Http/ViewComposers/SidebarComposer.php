<?php

namespace App\Http\ViewComposers;

use Auth;
use App\Models\Account;
use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view)
    {
        //pick up the current account
        if (Auth::user()->current_account_id) {
            $account = Account::with('campaigns')->find(Auth::user()->current_account_id);
        } else {
            $account = Account::with('campaigns')->where('user_id', Auth::id())->orderBy('name')->first();
        }

        $view->with('account', $account);
    }
}
