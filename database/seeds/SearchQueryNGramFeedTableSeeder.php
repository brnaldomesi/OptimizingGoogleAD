<?php

use App\Models\Account;
use Illuminate\Database\Seeder;
use App\Models\SearchQueryNGramPerformance;

class SearchQueryNGramFeedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Account::all() as $account) {
            $search_query_n_gram = SearchQueryNGramPerformance::first();

            factory(App\Models\SearchQueryNGramFeed::class, 9)->create([
                'search_query_n_gram_id'	=>	$search_query_n_gram->id,
                'n_gram'	=>	$search_query_n_gram->n_gram,
                'account_id'	=>	$account->id,
                'date_range'	=>	'last_30_days',
            ]);
        }
    }
}
