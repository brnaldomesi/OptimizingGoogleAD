<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(App\Models\Keyword::class, function (Faker $faker) {
    return [

        'keyword_match_type'		=>		$faker->randomElement(['exact', 'phrase', 'broad']),
        'keyword_text'		=>		$faker->word.' '.$faker->word,
        'google_id' 	=> Str::random(10),
        'status'		=> 'enabled',

    ];
});
