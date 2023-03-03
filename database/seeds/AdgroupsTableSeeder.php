<?php

use App\Models\Campaign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdgroupsTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Campaign::all() as $campaign) {
            factory(App\Models\Adgroup::class, 2)->create([
                'account_id'	=>	$campaign->account_id,
                'campaign_id'	=>	$campaign->id,
            ]);
        }
    }
}
