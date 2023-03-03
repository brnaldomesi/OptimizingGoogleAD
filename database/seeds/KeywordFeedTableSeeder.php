<?php

use App\Models\Account;
use App\Models\Keyword;
use Illuminate\Database\Seeder;

class KeywordFeedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Account::all() as $account) {
            $keyword = Keyword::first();

            factory(App\Models\KeywordFeed::class, 9)->create([
                'keyword_id'	=>	$keyword->id,
                'keyword_text'	=>	$keyword->keyword_text,
                'keyword_match_type'	=>	$keyword->keyword_match_type,
                'account_id'	=>	$account->id,
            ]);
        }
    }
}
