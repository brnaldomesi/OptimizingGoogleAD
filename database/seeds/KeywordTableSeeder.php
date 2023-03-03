<?php

use App\Models\Account;
use App\Models\Adgroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeywordTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach (Account::all() as $account) {
            foreach (Adgroup::all() as $adgroup) {
                foreach (range(0, 10) as $i) {
                    factory(App\Models\Keyword::class, 2)->create([
                        'adgroup_id'	=>	$adgroup->id,
                        'account_id'	=>	$adgroup->campaign->account->id,
                    ]);
                }
            }
        }
    }
}
