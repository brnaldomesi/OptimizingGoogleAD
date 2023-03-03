<?php

use App\Models\Adgroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdvertsTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach (Adgroup::all() as $adgroup) {
            factory(App\Models\Advert::class, 2)->create([
                'adgroup_id'	=>	$adgroup->id,
                'account_id'	=>	$adgroup->campaign->account->id,
            ]);
        }
    }
}
