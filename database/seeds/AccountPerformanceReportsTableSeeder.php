<?php

use App\Models\Account;
use Illuminate\Database\Seeder;
use App\Models\AccountPerformanceReport;
use Illuminate\Support\Facades\DB;

class AccountPerformanceReportsTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Account::all() as $account) {
            factory(App\Models\AccountPerformanceReport::class)->create([
                'account_id'	=>	$account->id
            ]);
        }
    }
}
