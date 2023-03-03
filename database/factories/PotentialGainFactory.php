<?php

use Faker\Generator as Faker;

$factory->define(App\Models\PotentialGain::class, function (Faker $faker) {
    return [
        'account_id'        =>  $faker->uuid,
        'date_range'	    =>	$faker->randomElement(['last_30_days', 'last_7_days', 'last_14_days']),
        'winners'		=>	$faker->numberBetween(0, 10),
        'clicks'		=>	$faker->numberBetween(1000, 10000),
        'conversions'	=>	$faker->numberBetween(100, 1000),
        'cpa'			=>	$faker->randomFloat(2, 10, 100),
        'cost_change'	=>	$faker->randomFloat(2, 10, 100),
    ];
});
