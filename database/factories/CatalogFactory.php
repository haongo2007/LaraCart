<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Larviu\Models\Category;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Category::class, function (Faker $faker) {
	static $order = 1;
    return [
        'name' => $faker->word,
        'slug' => Str::slug($faker->word, '-'),
        'is_active' => 1,
        'order' => $order++,
        'id_parent' => 0,
        'creator' => 1,
    ];
});

