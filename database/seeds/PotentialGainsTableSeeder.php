<?php

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PotentialGainsTableSeeder extends Seeder
{
    public function run()
    {
        Account::all()->each(function ($account) {
            factory(App\Models\PotentialGain::class)->create([
                'account_id'	=>	$account->id,
                'date_range'	=>	'last_30_days',
            ]);
        });
    }
}
