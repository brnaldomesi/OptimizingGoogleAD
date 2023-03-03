<?php

use App\Models\Account;
use App\Models\Adgroup;
use Illuminate\Database\Seeder;

class AdgroupFeedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Account::all() as $account) {
            $adgroup = Adgroup::first();

            factory(App\Models\AdGroupFeed::class, 9)->create([
                'adgroup_id'	=>	$adgroup->id,
                'account_id'	=>	$account->id,
                'date_range'	=>	'last_30_days',
            ]);
        }
    }
}
