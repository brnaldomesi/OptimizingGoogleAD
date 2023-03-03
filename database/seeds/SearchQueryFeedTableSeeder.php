<?php

use App\Models\Account;
use App\Models\SearchQuery;
use Illuminate\Database\Seeder;

class SearchQueryFeedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Account::all() as $account) {
            $search_query = SearchQuery::first();

            factory(App\Models\SearchQueryFeed::class, 9)->create([
                'search_query_id'	=>	$search_query->id,
                'query'	=>	$search_query->query,
                'account_id'	=>	$account->id,
                'date_range'	=>	'last_30_days',
            ]);
        }
    }
}
