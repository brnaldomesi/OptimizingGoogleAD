<?php

namespace App\Http\Controllers\User;

use Auth;
use App\Models\Advert;
use App\Models\Adgroup;
use App\Models\Campaign;
use App\Libraries\Breadcrumbs;
use App\Libraries\RecentFactory;
use App\Libraries\SimplifyWinners;
use App\Decorators\AdvertDecorator;
use App\Http\Controllers\Controller;
use App\Models\AccountWinningElement;
use App\Models\AdgroupWinningElement;
use App\Models\CampaignWinningElement;

class AdvertController extends Controller
{
    public function index($adgroup_id, $showCreator = false)
    {
        $adgroup = Adgroup::with('winningElements')->findOrFail($adgroup_id);

        //record the recent for the dashboard
        (new RecentFactory(Auth::id(), 'adgroup', $adgroup->id))->create();

        //simplify it a bit
        $account = $adgroup->campaign->account;
        $campaign = $adgroup->campaign;

        $dateRange = Auth::user()->date_range;

        //pick up the ads and performance
        $adverts = Advert::with(['performance' => function ($query) use ($dateRange) {
            $query->where('date_range', $dateRange);
        }])
            ->where('adgroup_id', $adgroup->id)
            ->where('status', 'enabled')
            ->get();

        $adverts = (new AdvertDecorator($adverts))->get();

        //pick up the winning elements for the ad creator
        $accountWinners = AccountWinningElement::where('account_id', $account->id)->where('date_range', $dateRange)->orderBy('order')->get();

        $accountWinners = (new SimplifyWinners($accountWinners))->get();

        $campaignWinners = CampaignWinningElement::where('campaign_id', $campaign->id)->where('date_range', $dateRange)->orderBy('order')->get();

        $campaignWinners = (new SimplifyWinners($campaignWinners))->get();

        $adgroupWinners = AdgroupWinningElement::where('adgroup_id', $adgroup->id)->where('date_range', $dateRange)->orderBy('order')->get();

        $adgroupWinners = (new SimplifyWinners($adgroupWinners))->get();

        //set a default final url
        $defaultFinalURL = $adverts[0]->final_urls[0];

        //set the breadcrumbs
        $breadcrumbs = new Breadcrumbs;
        $breadcrumbs->pushParent($account->name, url('user/campaigns/'.$account->id));
        $breadcrumbs->pushParent($adgroup->campaign->name, url('user/adgroups/campaign/'.$adgroup->campaign->id));
        $breadcrumbs->active($adgroup->name);

        $this->authorize('view', $account);

        return view('user.advert.index', [
            'breadcrumbs'		=> 	$breadcrumbs->get(),
            'adverts'			=> 	$adverts,
            'adgroup'			=>	$adgroup,
            'accountWinners'	=>	$accountWinners,
            'campaignWinners'	=>	$campaignWinners,
            'adgroupWinners'	=>	$adgroupWinners,
            'defaultFinalURL'	=>	$defaultFinalURL,
            'showCreator'		=>	$showCreator,
        ]);
    }
}
