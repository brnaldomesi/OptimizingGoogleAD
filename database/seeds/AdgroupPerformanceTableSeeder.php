<?php

use App\Models\Adgroup;
use Illuminate\Database\Seeder;
use App\Models\AdgroupPerformance;
use Illuminate\Support\Facades\DB;

class AdgroupPerformanceTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Adgroup::all() as $adgroup) {
            factory(App\Models\AdgroupPerformance::class)->create([
                'adgroup_id'	=>	$adgroup->id,
                'account_id'	=>	$adgroup->campaign->account->id,
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\AdgroupPerformance::class)->create([
                'adgroup_id'	=>	$adgroup->id,
                'account_id'	=>	$adgroup->campaign->account->id,
                'date_range'	=>	'last_7_days',
            ]);

            factory(App\Models\AdgroupPerformance::class)->create([
                'adgroup_id'	=>	$adgroup->id,
                'account_id'	=>	$adgroup->campaign->account->id,
                'date_range'	=>	'yesterday',
            ]);
        }
    }
}
