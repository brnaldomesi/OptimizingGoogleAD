<?php

namespace App\Http\Controllers\User;

use Auth;
use App\Models\Account;
use App\Models\Campaign;
use App\Libraries\Breadcrumbs;
use App\Http\Controllers\Controller;
use App\Decorators\CampaignDecorator;

class CampaignController extends Controller
{
    public function index($accountId)
    {
        $account = Account::findOrFail($accountId);

        $this->authorize('view', $account);

        $dateRange = Auth::user()->date_range;

        $breadcrumbs = new Breadcrumbs;
        $breadcrumbs->pushParent($account->name, url('user/accounts'));

        $campaigns = Campaign::with(['performance' => function ($query) use ($dateRange) {
            $query->where('date_range', $dateRange);
        }])->where('account_id', $account->id)->get();

        $campaigns = (new CampaignDecorator($campaigns))->get();

        return view('user.campaign.index', [
            'breadcrumbs'	=>	$breadcrumbs->get(),
            'campaigns'	=> 	$campaigns,
        ]);
    }
}
