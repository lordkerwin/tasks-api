<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'body' => $faker->paragraphs('3', true),
        'due_date' => $faker->dateTimeBetween('now', '+90 days')
    ];
});
