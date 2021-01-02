<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Comment;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'body' => substr($faker->text, 0, 500),
        'user_id' => fn() => factory(User::class)->create()->id,
    ];
});
