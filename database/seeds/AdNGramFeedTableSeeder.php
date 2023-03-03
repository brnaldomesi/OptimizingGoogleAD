<?php

use App\Models\Account;
use Illuminate\Database\Seeder;
use App\Models\AdNGramPerformance;

class AdNGramFeedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Account::all() as $account) {
            $performance = AdNGramPerformance::first();

            factory(App\Models\AdNGramFeed::class, 9)->create([
                'n_gram_id'	=>	$performance->id,
                'account_id'	=>	$account->id,
                'date_range'	=>	'last_30_days',
            ]);
        }
    }
}
