<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(App\Models\Campaign::class, function (Faker $faker) {
    $network = $faker->randomElement(['SEARCH', 'CONTENT']);
    return [
        'name' 		=> $faker->name.' campaign',
        'google_id' => Str::random(10),
        'status' => 'enabled',
        'advertising_channel_type' => $network,
        'campaign_trial_type' => 'base campaign'
    ];
});