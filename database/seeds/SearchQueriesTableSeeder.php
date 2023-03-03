<?php

use App\Models\Account;
use App\Models\SearchQuery;
use Illuminate\Database\Seeder;

class SearchQueriesTableSeeder extends Seeder
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
                factory(App\Models\SearchQuery::class, 9)->create([
                    'account_id'	=>	$account->id,
                ]);
            }
        }
    }
}
