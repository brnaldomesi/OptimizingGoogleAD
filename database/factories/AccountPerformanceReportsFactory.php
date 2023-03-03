<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Models\AccountPerformanceReport::class, function (Faker $faker) {

    return [
        'id'        =>  $faker->uuid,
        'date_range' => 'last_30_days',
        'date' => Carbon::now(),
        'conversions' => $faker->numberBetween(20, 100)
        
    ];
    
});



