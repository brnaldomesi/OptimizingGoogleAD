<?php

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CampaignsTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Account::all() as $account) {

            $budget_group_ids = \App\Models\BudgetCommander::where('account_id',$account->id)
            ->where('budget_group_name', '!=','master')
            ->get('id')
            ->toArray();

            factory(App\Models\Campaign::class, 10)->create([
                'account_id'	=>	$account->id,
                'budget_group_id' =>  $budget_group_ids[0]['id']
            ]);

            factory(App\Models\Campaign::class, 10)->create([
                'account_id'	=>	$account->id,
                'budget_group_id' =>  $budget_group_ids[1]['id']
            ]);
        }
    }
}
