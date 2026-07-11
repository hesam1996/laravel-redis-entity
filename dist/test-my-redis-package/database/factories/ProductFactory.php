<?php

namespace Database\Factories;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->words(3, true);
        $description = $this->faker->words(rand(100, 200), true);

        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'title' => $title,
            'description' => $description,
            'slug' => Str::slug($title),
            'image' => $this->faker->imageUrl(640, 480, 'products', true),
            'stock' => $this->faker->numberBetween(0, 100),
            'price' => $this->faker->numberBetween(10000, 1000000),
        ];
    }
}
