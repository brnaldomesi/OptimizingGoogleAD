<?php

use Faker\Generator as Faker;

$factory->define(App\Models\AccountPerformance::class, function (Faker $faker) {
    return [
        'account_id'        =>  $faker->uuid,
        'date_range'	    =>	'last_30_days',
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
        'ctr'               =>	$faker->randomFloat(2, 1, 10),
        //'message'           =>  $faker->randomElement(['too_few_ads', 'too_many_ads', 'has_winners']),
    ];
});
