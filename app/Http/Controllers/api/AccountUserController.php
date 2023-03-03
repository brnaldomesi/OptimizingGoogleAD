<?php

namespace App\Http\Controllers\api;

use App\Models\Account;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AccountUserController extends Controller
{
    public function show(User $userid)
    {
        $id = $userid->current_account_id;
        $account = Account::find($id);

        if (is_null($account)) {
            $account = Account::where('user_id', Auth::id())->first();
        }

        return $account;
    }
}
