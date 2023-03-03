<?php

use App\Models\Account;
use App\Models\Keyword;
use Illuminate\Database\Seeder;

class BudgetCommanderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Account::all() as $account) {

            factory(App\Models\BudgetCommander::class, 1)->create([
                'account_id'	=>	$account->id,
                'budget_group_name' => 'master'
            ]);

            factory(App\Models\BudgetCommander::class, 1)->create([
                'account_id'	=>	$account->id,
                'budget_group_name' => 'Search'
            ]);

            factory(App\Models\BudgetCommander::class, 1)->create([
                'account_id'	=>	$account->id,
                'budget_group_name' => 'Display'
            ]);
        }
    }
}
