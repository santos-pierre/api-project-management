<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Project;
use Carbon\Carbon;
use App\StatusProject;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    $title = $faker->sentence(4);
    $slug = Str::of($title)->slug('-')->__toString();
    return [
        'slug' => $slug,
        'title' => $title,
        'author' => function () {
            return factory(User::class)->create()->id;
        },
        'description' => $faker->text,
        'deadline' => Carbon::now()->addDay(3),
        'repository_url' => $faker->url,
        'status_project_id' => function () {
            return StatusProject::all()->pluck('id')->random();
        },
    ];
});
