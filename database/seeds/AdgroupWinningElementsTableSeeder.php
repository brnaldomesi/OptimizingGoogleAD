<?php

use App\Models\Adgroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdgroupWinningElementsTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Adgroup::all() as $adgroup) {
            factory(App\Models\AdgroupWinningElement::class, 3)->create([
                'adgroup_id'	=>	$adgroup->id,
                'account_id'	=>	$adgroup->campaign->account->id,

                'type'			=>	'headline_1',
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\AdgroupWinningElement::class, 3)->create([
                'adgroup_id'	=>	$adgroup->id,
                'account_id'	=>	$adgroup->campaign->account->id,
                'type'			=>	'headline_2',
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\AdgroupWinningElement::class, 3)->create([
                'adgroup_id'	=>	$adgroup->id,
                'account_id'	=>	$adgroup->campaign->account->id,
                'type'			=>	'description',
                'date_range'	=>	'last_30_days',
            ]);

            factory(App\Models\AdgroupWinningElement::class, 3)->create([
                'adgroup_id'	=>	$adgroup->id,
                'account_id'	=>	$adgroup->campaign->account->id,
                'type'			=>	'path_1_path_2',
                'date_range'	=>	'last_30_days',
                'value'			=>	'London/Hotels',
            ]);
        }
    }
}
