<?php

use App\Models\Campaign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignWinningElementsTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Campaign::all() as $campaign) {
            factory(App\Models\CampaignWinningElement::class, 3)->create([
                'campaign_id'	=>	$campaign->id,
                'account_id'	=>	$campaign->account->id,
                'type'			=>	'headline_1',
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\CampaignWinningElement::class, 3)->create([
                'campaign_id'	=>	$campaign->id,
                'account_id'	=>	$campaign->account->id,
                'type'			=>	'headline_2',
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\CampaignWinningElement::class, 3)->create([
                'campaign_id'	=>	$campaign->id,
                'account_id'	=>	$campaign->account->id,
                'type'			=>	'description',
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\CampaignWinningElement::class, 3)->create([
                'campaign_id'	=>	$campaign->id,
                'account_id'	=>	$campaign->account->id,
                'type'			=>	'path_1_path_2',
                'date_range'	=>	'last_30_days',
                'value'			=>	'London/Hotels',
            ]);
        }
    }
}
