<?php

namespace App\Libraries;

use Log;
use Carbon\Carbon;
use App\Models\Account;
use App\Libraries\AdWordsAPISession;
use Illuminate\Support\Facades\Artisan;
use App\Libraries\CustomerService\CustomerServiceSelector;
use App\Libraries\CustomerService\UpdateAccountsWithDataFromGoogle;

//using a factory here because I dont want to do all of this stuff in testing or seeding, also, we'll reuse this if we decide to allow for more than one acct in the future

//@todo: this could be refactored as it's a pretty big function

class AccountFactory
{
    protected $user;

    public function __construct($user, $google_id)
    {
        $this->user = $user;
        $this->googleId = $google_id;
    }

    public function create()
    {
        Log::info('Creating new account');

        //create a new account
        $account = new Account;
        $account->user_id = $this->user->id;
        $account->google_id = $this->googleId;

        $account->save();
    }
}
