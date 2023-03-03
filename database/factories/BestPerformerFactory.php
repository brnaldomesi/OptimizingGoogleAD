<?php

use Faker\Generator as Faker;

$factory->define(App\Models\BestPerformer::class, function (Faker $faker) {
    return [
        'account_id'        =>  $faker->uuid,
        'advert_id'        	=>  $faker->uuid,
        'date_range'	    =>	$faker->randomElement(['last_30_days', 'last_7_days', 'last_14_days']),
        'cpa'               =>	$faker->randomFloat(2, 10, 100),
        'roas'              =>  $faker->randomFloat(2, 10, 100),
        'conversion_rate'   =>  $faker->randomFloat(2, 1, 10),
        'ctr'               =>	$faker->randomFloat(2, 1, 10),
    ];
});
