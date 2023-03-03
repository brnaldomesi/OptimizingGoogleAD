<?php

use App\Models\Account;
use Illuminate\Database\Seeder;

class SearchQueryNGramPerformanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Account::all() as $account) {
            foreach (range(0, 20) as $i) {
                factory(App\Models\SearchQueryNGramPerformance::class, 9)->create([
                    'account_id'	=>	$account->id,
                ]);
            }
        }
    }
}
