<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Models\SearchQueryNGramFeed::class, function (Faker $faker) {
    return [

        'date_range'	    =>	$faker->randomElement(['last_30_days', 'last_7_days', 'last_14_days']),

        'priority'			=>	$faker->randomElement(['warning', 'primary', 'success']),
        'headline'		=>	$faker->randomElement(['There is a problem with this search query n-gram', 'This n-gram is amazing']),
        'message'		=>	$faker->randomElement(['This n-gram is above CPA target', 'This search query n-gram is below CPA target']),
        'suggestion'		=>	$faker->randomElement(['We recommend reducing spend in this area', 'We recommend increasing spend in this area']),
        'display_from_date'		=>	Carbon::now(),

    ];
});
