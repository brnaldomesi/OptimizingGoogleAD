<?php

use App\Models\Advert;
use App\Models\Account;
use App\Models\Adgroup;
use App\Models\Campaign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorstPerfomersTableSeeder extends Seeder
{
    public function run()
    {
        Account::all()->each(function ($account) {
            $campaign = factory(App\Models\Campaign::class)->create([
                'account_id'	=>	$account->id,
            ]);

            $adgroup = factory(App\Models\Adgroup::class)->create([
                'campaign_id'	=>	$campaign->id,
            ]);

            $advert = factory(App\Models\Advert::class)->create([
                'adgroup_id'	=>	$adgroup->id,
            'account_id'	=>	$adgroup->campaign->account->id,
            ]);

            factory(App\Models\WorstPerformer::class)->create([
                'account_id'	=>	$account->id,
                'advert_id'		=>	$advert->id,
                'date_range'	=>	'last_30_days',
            ]);
        });
    }
}
