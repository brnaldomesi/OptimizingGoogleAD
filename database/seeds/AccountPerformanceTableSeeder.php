<?php

use App\Models\Account;
use Illuminate\Database\Seeder;
use App\Models\AccountPerformance;
use Illuminate\Support\Facades\DB;

class AccountPerformanceTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Account::all() as $account) {
            factory(App\Models\AccountPerformance::class)->create([
                'account_id'	=>	$account->id,
                'date_range'	=>	'last_30_days',
            ]);
        }
    }
}
