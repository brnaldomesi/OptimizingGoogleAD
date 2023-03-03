<?php

use App\Models\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountWinningElementsTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Account::all() as $account) {
            factory(App\Models\AccountWinningElement::class, 3)->create([
                'account_id'	=>	$account->id,
                'type'			=>	'headline_1',
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\AccountWinningElement::class, 3)->create([
                'account_id'	=>	$account->id,
                'type'			=>	'headline_2',
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\AccountWinningElement::class, 3)->create([
                'account_id'	=>	$account->id,
                'type'			=>	'description',
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\AccountWinningElement::class, 3)->create([
                'account_id'	=>	$account->id,
                'type'			=>	'path_1_path_2',
                'date_range'	=>	'last_30_days',
                'value'			=>	'London/Hotels',
            ]);
        }
    }
}
