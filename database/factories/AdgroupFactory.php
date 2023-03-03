<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(App\Models\Adgroup::class, function (Faker $faker) {
    return [
        'name' 		=> $faker->sentence(2).' ad group',
        'account_id'	=> $faker->uuid,
        'google_id' => Str::random(10),
    ];
});
