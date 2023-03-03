<?php

namespace App\Http\Controllers\User;

use Auth;
use App\Models\Account;
use App\Models\Adgroup;
use App\Models\Campaign;
use App\Libraries\Breadcrumbs;
use App\Libraries\RecentFactory;
use App\Decorators\AdgroupDecorator;
use App\Http\Controllers\Controller;

class AdgroupController extends Controller
{
    public function index($scope, $id, $tab = 'all')
    {
        $dateRange = Auth::user()->date_range;

        //scope can be one of account or campaign
        if ($scope == 'account') {
            $account = Account::with('adgroups')->findOrFail($id);

            $breadcrumbs = new Breadcrumbs;
            $breadcrumbs->active($account->name);

            $adgroups = Adgroup::with(['performance' => function ($query) use ($dateRange) {
                $query->where('date_range', $dateRange);
            }])->where('account_id', $account->id)->get();
        } elseif ($scope == 'campaign') {
            $campaign = Campaign::findOrFail($id);
            $account = $campaign->account;

            //set the breadcrumbs
            $breadcrumbs = new Breadcrumbs;
            $breadcrumbs->pushParent($account->name, url('user/campaigns/'.$account->id));
            $breadcrumbs->active($campaign->name);

            $adgroups = Adgroup::with(['performance' => function ($query) use ($dateRange) {
                $query->where('date_range', $dateRange);
            }])->where('campaign_id', $campaign->id)->get();

            //record the recent for the dashboard
            (new RecentFactory(Auth::id(), 'campaign', $campaign->id))->create();
        } else {
            abort(404);
        }

        $this->authorize('view', $account);

        $adgroups = (new AdgroupDecorator($adgroups))->get();

        $adgroupsWithWinners = $adgroups->filter(function ($adgroup) {
            return $adgroup->performance->message == 'has_winners';
        });

        $adgroupsWithTooManyAdverts = $adgroups->filter(function ($adgroup) {
            return $adgroup->performance->message == 'too_many_ads';
        });

        $adgroupsWithTooFewAdverts = $adgroups->filter(function ($adgroup) {
            return $adgroup->performance->message == 'too_few_ads';
        });

        //compose the individual data tables
        $output = [

            'tab'			=>	$tab,
            'breadcrumbs'	=> 	$breadcrumbs->get(),

            'adgroupsTable' => 	view('user.adgroup.table', ['adgroups' => $adgroups])->render(),

            'adgroupsWithWinnersTable' => view('user.adgroup.table', ['adgroups' => $adgroupsWithWinners])->render(),

            'adgroupsWithTooManyAdvertsTable' => view('user.adgroup.table', ['adgroups' => $adgroupsWithTooManyAdverts])->render(),

            'adgroupsWithTooFewAdvertsTable' => view('user.adgroup.table', ['adgroups' => $adgroupsWithTooFewAdverts])->render(),

        ];

        return view('user.adgroup.index', $output);
    }
}
