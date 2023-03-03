<?php

namespace App\Http\Controllers\api;

//use App\Http\Resources\BudgetCommanderActionsResource;
use App\Models\BudgetCommander;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BudgetCommanderActionsController extends Controller
{

    public function show(\App\Models\Account $account)
    {
        return BudgetCommander::where('account_id', $account->id)->first();
    }

    public function update(Request $request, \App\Models\Account $account)
    {
        $budgetcommander =  BudgetCommander::where('account_id', $account->id)->first();
        if(isset($request['notify_via_email']))$budgetcommander->notify_via_email = $request['notify_via_email'];
        if(isset($request['pause_campaigns']))$budgetcommander->pause_campaigns = $request['pause_campaigns'];
        if(isset($request['enable_campaigns']))$budgetcommander->enable_campaigns = $request['enable_campaigns'];
        if(isset($request['rollover_spend']))$budgetcommander->rollover_spend = $request['rollover_spend'];
        //control spend is disabled for now
        // if(isset($request['control_spend']))$budgetcommander->control_spend = $request['control_spend'];
        if(isset($request['emergency_stop']))$budgetcommander->emergency_stop = $request['emergency_stop'];
        if(isset($request['excess_budget']))$budgetcommander->excess_budget = $request['excess_budget'];
        $budgetcommander->save();

        return $budgetcommander;
    }

}
