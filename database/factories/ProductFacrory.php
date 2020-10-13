<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Webcityro\Larasearch\Models\Product;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => ucfirst($faker->unique()->words(3, true)),
        'price' => $faker->unique()->randomFloat(1, 0, 100)
    ];
});
