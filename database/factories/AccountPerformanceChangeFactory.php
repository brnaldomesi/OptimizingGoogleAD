<?php

use Faker\Generator as Faker;

$factory->define(App\Models\AccountPerformanceChange::class, function (Faker $faker) {
    return [
        'account_id'        =>  $faker->uuid,
        'date_range'	    =>	$faker->randomElement(['last_30_days', 'last_7_days', 'last_14_days']),
        'ctr'               =>	$faker->randomFloat(2, 1, 10),
        'cpa'               =>	$faker->randomFloat(2, 10, 100),
        'roas'              =>  $faker->randomFloat(2, 10, 100),
        'conversion_rate'   =>  $faker->randomFloat(2, 1, 10),
        'ctr_baseline'               =>	$faker->randomFloat(2, 1, 10),
        'roas_baseline'               =>	$faker->randomFloat(2, 1, 10),
        'conversion_rate_baseline'               =>	$faker->randomFloat(2, 1, 10),
        'cpa_baseline'               =>	$faker->randomFloat(2, 1, 10),
        'ctr_graph_data' => [
            'current'   =>  [11.50, 8, 7.50, 6.50],
            'previous'  =>  [10.90, 13.09, 15.17, 12.23],
            ],
        'conversion_rate_graph_data' => [
            'current'   =>  [11.50, 8, 7.50, 6.50],
            'previous'  =>  [10.90, 13.09, 15.17, 12.23],
            ],
        'cpa_graph_data' =>  [
            'current'   =>  [11.50, 8, 7.50, 6.50],
            'previous'  =>  [10.90, 13.09, 15.17, 12.23],
            ],
        'roas_graph_data' => [
            'current'   =>  [11.50, 8, 7.50, 6.50],
            'previous'  =>  [10.90, 13.09, 15.17, 12.23],
            ],

    ];
});
