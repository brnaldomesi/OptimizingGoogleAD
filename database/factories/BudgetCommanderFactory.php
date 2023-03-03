<?php

use Faker\Generator as Faker;

$factory->define(App\Models\BudgetCommander::class, function (Faker $faker) {

    return [
        'id'        =>  $faker->uuid,
        'notify_via_email' => 1,
        'pause_campaigns' => 1,
        'enable_campaigns' => 1,
        'rollover_spend' => 1,
        'control_spend' => 1,
        'email_sent' => 0,
        'excess_budget' => 1000,
        'budget' => $faker->randomFloat(2, 100, 10000),
        'kpi_name' => $faker->randomElement(['cpa','roas']),
        'kpi_value' => $faker->numberBetween(5, 100)
        
    ];
    
});