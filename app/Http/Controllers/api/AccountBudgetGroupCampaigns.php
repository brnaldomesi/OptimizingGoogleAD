<?php

namespace App\Http\Controllers\api;

use App\Models\Account;
use App\Models\Campaign;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AccountBudgetGroupCampaigns extends Controller
{

    public function show(Account $account)
    {

        $campaigns = DB::table('campaigns')
        ->selectRaw('budget_commander.id as budget_group_id,budget_commander.budget_group_name,campaigns.status,campaigns.name,campaigns.advertising_channel_type as ad_network_type_1,campaigns.id')
        ->join('budget_commander', 'budget_commander.id', '=', 'campaigns.budget_group_id', 'left outer')
        ->where('campaigns.account_id', '=', $account->id)
        ->where('campaigns.status', '!=', 'removed')
        ->orderBy('campaigns.status')
        ->orderBy('campaigns.name')
        ->get();

        return $campaigns;
        
    }

}
