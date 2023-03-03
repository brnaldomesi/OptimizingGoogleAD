<?php

use Faker\Generator as Faker;

$factory->define(App\Models\AdvertPerformance::class, function (Faker $faker) {
    return [
        'advert_id'         =>  '',
        'account_id'         =>  '',
        'date_range'	    =>	$faker->randomElement(['last_30_days', 'last_7_days', 'last_14_days']),
        'impressions'       =>  $faker->numberBetween(1000, 10000),
        'clicks'            =>  $faker->numberBetween(100, 1000),
        'conversions'       =>  $faker->numberBetween(0, 100),
        'cost'              =>  $faker->randomFloat(2, 10, 100),
        'average_cpc'       =>	$faker->randomFloat(2, 1, 10),
        'conversion_value'  =>  $faker->randomFloat(2, 10, 100),
        'cpa'               =>	$faker->randomFloat(2, 10, 100),
        'roas'              =>  $faker->randomFloat(2, 10, 100),
        'conversion_rate'   =>  $faker->randomFloat(2, 1, 10),
        'ctr'               =>	$faker->randomFloat(2, 1, 10),
        'impression_share'  =>  $faker->randomFloat(2, 1, 10),
        'ctr_significance'  =>  $faker->randomFloat(2, 1, 100),
        'conversion_rate_significance'  =>  $faker->randomFloat(2, 1, 100),
        'ctr_message'       =>  $faker->randomElement(['winning', 'losing', null]),
        'conversion_rate_message' =>  $faker->randomElement(['winning', 'losing', null]),
    ];
});
