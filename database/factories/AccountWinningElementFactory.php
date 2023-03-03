<?php

use Faker\Generator as Faker;

$factory->define(App\Models\AccountWinningElement::class, function (Faker $faker) {
    return [
        'account_id'	=>  $faker->uuid,
        'type'       	=>  $faker->randomElement(['headline_1', 'headline_2', 'description', 'path_1_path_2']),
        'value'			=>	$faker->sentence(3),
        'order'			=>	$faker->numberBetween(1, 3),
        'date_range'	=>	$faker->randomElement(['last_30_days', 'last_7_days', 'last_14_days']),

    ];
});
