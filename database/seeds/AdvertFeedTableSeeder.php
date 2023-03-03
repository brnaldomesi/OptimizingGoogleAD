<?php

use App\Models\Advert;
use App\Models\Account;
use Illuminate\Database\Seeder;

class AdvertFeedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Account::all() as $account) {
            $advert = Advert::first();

            factory(App\Models\AdvertFeed::class, 9)->create([
                'advert_id'	=>	$advert->id,
                'account_id'	=>	$account->id,
            ]);
        }
    }
}
