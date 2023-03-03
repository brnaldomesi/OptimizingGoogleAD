<?php

use Faker\Generator as Faker;

$factory->define(App\Models\AdNGramPerformance::class, function (Faker $faker) {
    return [
        'account_id'        =>  $faker->uuid,
        'date_range'	    =>	'last_14_days',

        'n_gram'			=>	$faker->words(3, true),
        'n_gram_count'		=>	$faker->numberBetween(1, 100),
        'show_on_graph'	=>	$faker->boolean(10),

        'ctr'               =>	$faker->randomFloat(2, 1, 10),

        'ctr_significance'               =>	$faker->randomFloat(2, 0, 100),

        'impressions'       =>  $faker->numberBetween(1000, 10000),
        'clicks'            =>  $faker->numberBetween(100, 1000),
        'conversions'       =>  $faker->numberBetween(0, 100),
        'average_position'  =>  $faker->randomFloat(1, 1, 10),
        'cost'              =>  $faker->randomFloat(2, 10, 100),
        'average_cpc'       =>	$faker->randomFloat(2, 1, 10),
        'conversion_value'  =>  $faker->randomFloat(2, 10, 100),
        'cpa'               =>	$faker->randomFloat(2, 10, 100),
        'roas'              =>  $faker->randomFloat(2, 10, 100),
        'conversion_rate'   =>  $faker->randomFloat(2, 1, 10),
        'graph_order'       =>  $faker->numberBetween(1, 3),

    ];
});
