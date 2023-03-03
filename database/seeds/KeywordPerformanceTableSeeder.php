<?php

use App\Models\Keyword;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeywordPerformanceTableSeeder extends Seeder
{
    public function run()
    {
        foreach (Keyword::all() as $keyword) {
            $date_ranges = ['last_30_days', 'last_7_days'];

            foreach ($date_ranges as $date_range) {
                factory(App\Models\KeywordPerformance::class)->create([
                    'keyword_id'		=>	$keyword->id,
                    'account_id'	=>	$keyword->adgroup->campaign->account->id,
                    'date_range'	=>	$date_range,
                ]);
            }
        }
    }
}
