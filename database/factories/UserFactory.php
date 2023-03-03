<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' 				=> $faker->name,
        'email' 			=> $faker->unique()->safeEmail,
        'password' 			=> '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'refresh_token' 	=> Str::random(10),
        'date_range'		=> 'last_14_days',
    ];
});
