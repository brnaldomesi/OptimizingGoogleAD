<?php

namespace App\Http\Controllers\User;

use Auth;
use App\Models\Advert;
use App\Models\Recent;
use App\Models\Account;
use App\Models\Adgroup;
use App\Models\Campaign;
use App\Decorators\AdgroupDecorator;
use App\Http\Controllers\Controller;
use App\Decorators\CampaignDecorator;
use App\Libraries\HumanReadibleDateRange;
use App\Decorators\BestPerformerDecorator;
use App\Decorators\WorstPerformerDecorator;

class DashboardController extends Controller
{
    public function index(Account $account)
    {
        //no oauth token so ask for it
        if (! Auth::user()->refresh_token) {
            logger('user has no refresh token, returning connect adwords...');
            return view('user.connect-adwords');
        }

        return view('auth.login');

    }
}
