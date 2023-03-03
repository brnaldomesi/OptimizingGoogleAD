<?php

namespace App\Http\Controllers\api;

use App\Models\Account;
use App\Models\Campaign;
use App\Models\BudgetCommander;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;

class AccountBudgetGroups extends Controller
{

    public function add(Request $request, Account $account){

        $rules = [
            'budget_group_id' => ['required', 'uuid'],
            'account_id' => ['required', 'uuid'],
            'budget_group_name' => ['required', 'not_regex:/(?i)\master\b/'],
            'kpi_value' => ['required', 'numeric', 'min:0'],
            'kpi_name' => ['required', 'regex:/(?i)(cpa|roas)\b/'],
            'budget' => ['required', 'numeric', 'min:0'],
            'notify_via_email' => ['required', 'boolean'],
            'pause_campaigns' => ['required', 'boolean'],
            'enable_campaigns' => ['required', 'boolean'],
            'rollover_spend' => ['required', 'boolean'],
            'control_spend' => ['required', 'boolean'],
            'emergency_stop' => ['required', 'boolean'],
            'campaign_ids' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            \Log::error($validator->messages());
            return response()->json(['error' => $validator->messages()], 400);
        }
        
        $budget_groups = $account->budgetCommander->where('id', $request->budget_group_id)
        ->where('account_id',$account->id);
        
        if(sizeOf($budget_groups->all())==0){
            
            $budget_group = new BudgetCommander;

        }else{

            $budget_group = $budget_groups->first();
        }

        $budget_group->id = $request->budget_group_id;
        $budget_group->account_id = $account->id;
        $budget_group->budget_group_name = $request->budget_group_name;
        $budget_group->kpi_value = isset($request->kpi_value) ? $request->kpi_value : 0;
        $budget_group->kpi_name = isset($request->kpi_name) ? $request->kpi_name : 0;
        $budget_group->budget = isset($request->budget) ? $request->budget : 0;
        $budget_group->notify_via_email = isset($request->notify_via_email) ? $request->notify_via_email : 0;
        $budget_group->pause_campaigns = isset($request->pause_campaigns) ? $request->pause_campaigns : 0;
        $budget_group->enable_campaigns = isset($request->enable_campaigns) ? $request->enable_campaigns : 0;
        $budget_group->rollover_spend = isset($request->rollover_spend) ? $request->rollover_spend : 0;
        $budget_group->control_spend = isset($request->control_spend) ? $request->control_spend : 0;
        $budget_group->emergency_stop = isset($request->emergency_stop) ? $request->emergency_stop : 0;
        
        $budget_group->save();
        //create the relationship in the campaigns table
        Campaign::where('budget_group_id', $request->budget_group_id)->update(['budget_group_id'=>'']);
        $campaigns = Campaign::whereIn('id',$request->campaign_ids);
        $campaigns->update(['budget_group_id'=>$request->budget_group_id]);
        
    }

    public function delete(Request $request, Account $account){

        $rules = [
            'budget_group_id' => ['required', 'uuid'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
        {
            \Log::error($validator->messages());
            return response()->json(['error' => $validator->messages()], 400);
        }
        
        $budget_groups = $account->budgetCommander->where('id', $request->budget_group_id)
        ->where('account_id',$account->id);
        
        if(sizeOf($budget_groups->all())==0){
            
            $message = 'Could not delete budget group. It does not exist.';
            \Log::info($message);
            return response()->json(['error' => $message], 400);
        }
        
        $budget_group = $budget_groups->first()->delete();

        //remove the relationship in the campaigns table
        Campaign::where('budget_group_id', $request->budget_group_id)->update(['budget_group_id'=>'']);

        
    }

    public function show(Account $account)
    {

        function getDates(){
            if(Carbon::now()->day==1){
                $tomorrow = Carbon::now()->addDays(1)->format('Y-m-d');
                return [$tomorrow, $tomorrow];//there's no data yet this month so we want to return 0 spend
            }
    
            $yesterday = Carbon::now()->subDays(1)->format('Y-m-d');
            $first_day_of_this_month = (new Carbon('first day of this month'))->format('Y-m-d');
    
            return [$first_day_of_this_month, $yesterday];        
        }

        // \Log::info(getDates()[0] . ' ' . getDates()[1]);

        $budget_group_columns = [
            'id',
            'budget_group_name',
            'kpi_value',
            'kpi_name',
            'budget',
            'notify_via_email',
            'pause_campaigns',
            'enable_campaigns',
            'rollover_spend',
            'control_spend', 
            'excess_budget',
            'emergency_stop'
        ];
        
        $budget_groups = \App\Models\BudgetCommander::where('account_id', $account->id)->where('budget_group_name' ,'!=','master')
        ->with(['campaign' => function ($query) {
            $query->select('id', 'budget_group_id');
        }])->get($budget_group_columns);

        function getBudgetGroupTotal($budget_group, $column){
            
            $total = 0;
            foreach($budget_group->campaign as $key => $campaign) {
                $campaign_total = $campaign->performance
                    ->where('date', '>=', getDates()[0])
                    ->where('date', '<=', getDates()[1])
                    ->where('date_range', 'last_112_days')
                    ->sum($column);
                $total+=$campaign_total;
            }
            return $total;
        }
        
        $budget_groups->each(function ($budget_group, $key) {
            $budget_group->cost += getBudgetGroupTotal($budget_group, 'cost');
            $budget_group->conversions += getBudgetGroupTotal($budget_group, 'conversions');
            $budget_group->conversion_value += getBudgetGroupTotal($budget_group, 'conversion_value');
            $budget_group->budget_group_id = $budget_group->id;
            unset($budget_group->campaign);
            unset($budget_group->id);
        });

        return $budget_groups;
    
    }

}


