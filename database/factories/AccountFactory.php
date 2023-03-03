<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Models\Account::class, function (Faker $faker) {
    $graph_data = [];
    foreach (range(1, 32) as $i) {
        $graph_data = array_merge($graph_data, [3 * $i]);
    }

    return [
        'user_id'	=> $faker->uuid,
        'name' 		=> $faker->company,
        'google_id' => '0000000000',
        'currency_code'	=>	'USD',
        'ad_performance_report_processed_at' => Carbon::now(),
        'timezone'	=>	'Europe/London',
        'budget'	=>	$faker->randomFloat(2, 100, 10000),
        'kpi_name'	=>	'cpa',
        'kpi_value'	=>	$faker->numberBetween(5, 100),
        'elapsed_time'  =>  $faker->randomFloat(2, 0, 100),
        'budget_actual_vs_target'  =>  $faker->randomFloat(2, 0, 100),
        'budget_forecast_vs_target'  =>  $faker->randomFloat(2, 0, 100),
        'kpi_actual_vs_target'  =>  $faker->randomFloat(2, 0, 100),
        'kpi_forecast_vs_target'  =>  $faker->randomFloat(2, 0, 100),
        'budget_target_graph_data'  =>  $graph_data,
        'budget_actual_graph_data'  =>  $graph_data,
        'kpi_target_graph_data' =>  $graph_data,
        'kpi_actual_graph_data' =>  $graph_data,
        'is_synced' => 0

    ];
});


