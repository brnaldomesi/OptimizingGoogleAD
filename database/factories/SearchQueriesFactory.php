<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Models\SearchQuery::class, function (Faker $faker) {
    return [

        'query' => $faker->word,

    ];
});
