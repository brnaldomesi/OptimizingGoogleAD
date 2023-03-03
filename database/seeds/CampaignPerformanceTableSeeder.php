<?php

use App\Models\Campaign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CampaignPerformance;

class CampaignPerformanceTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Campaign::all() as $campaign) {
            factory(App\Models\CampaignPerformance::class)->create([
                'campaign_id'	=>	$campaign->id,
                'account_id'	=>	$campaign->account->id,
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\CampaignPerformance::class)->create([
                'campaign_id'	=>	$campaign->id,
                'account_id'	=>	$campaign->account->id,
                'date_range'	=>	'last_7_days',
            ]);

            factory(App\Models\CampaignPerformance::class)->create([
                'campaign_id'	=>	$campaign->id,
                'account_id'	=>	$campaign->account->id,
                'date_range'	=>	'THIS_MONTH',
            ]);
        }
    }
}
