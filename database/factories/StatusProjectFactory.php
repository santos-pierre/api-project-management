<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\StatusProject;
use Faker\Generator as Faker;

$factory->define(StatusProject::class, function (Faker $faker) {
    $name = $faker->word;
    return [
        'slug' => $name,
        'name' => $name
    ];
});
