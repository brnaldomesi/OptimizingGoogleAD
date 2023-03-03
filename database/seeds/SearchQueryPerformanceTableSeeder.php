<?php

use App\Models\Account;
use App\Models\SearchQuery;
use Illuminate\Database\Seeder;

class SearchQueryPerformanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Account::all() as $account) {
            foreach (SearchQuery::all() as $search_query) {
                factory(App\Models\SearchQueryPerformance::class, 9)->create([
                    'account_id'	=>	$account->id,
                    'search_query_id'	=>	$search_query->id,
                ]);
            }
        }
    }
}
