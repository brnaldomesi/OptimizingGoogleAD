<?php

use App\Models\Advert;
use Illuminate\Database\Seeder;
use App\Models\AdvertPerformance;
use Illuminate\Support\Facades\DB;

class AdvertPerformanceTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Advert::all() as $advert) {
            factory(App\Models\AdvertPerformance::class)->create([
                'advert_id'		=>	$advert->id,
                'account_id'	=>	$advert->adgroup->campaign->account->id,
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\AdvertPerformance::class)->create([
                'advert_id'		=>	$advert->id,
                'account_id'	=>	$advert->adgroup->campaign->account->id,
                'date_range'	=>	'last_7_days',
            ]);

            factory(App\Models\AdvertPerformance::class)->create([
                'advert_id'		=>	$advert->id,
                'account_id'	=>	$advert->adgroup->campaign->account->id,
                'date_range'	=>	'yesterday',
            ]);
        }
    }
}
