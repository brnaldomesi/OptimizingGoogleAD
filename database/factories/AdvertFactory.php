<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(App\Models\Advert::class, function (Faker $faker) {
    return [
        'adgroup_id'    => $faker->uuid,
        'headline_1'    => $faker->sentence(3),
        'headline_2'	=> $faker->sentence(3),
        'headline_3'	=> $faker->sentence(3),
        'description'   => $faker->sentence,
        'description_2'   => $faker->sentence,
        'path_1' 		=> $faker->word,
        'path_2' 		=> $faker->word,
        'google_id' 	=> Str::random(10),
        'status'		=> 'enabled',
        'final_urls' 	=> [$faker->url, $faker->url],
        'loser'         => $faker->boolean(30),
        'potential_savings'  => $faker->randomFloat(2, null, 100),
        'domain'        => $faker->domainName,
    ];
});
