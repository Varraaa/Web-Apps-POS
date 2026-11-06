<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'category' => fake()->randomElement(['Outerwear', 'Tops', 'Bottoms', 'Accessories']),
            'description' => fake()->sentence(12),
            'price' => fake()->randomElement([150000, 250000, 399000, 599000]),
            'discount_percent' => fake()->randomElement([0, 10, 20]),
            'stock' => fake()->numberBetween(5, 30),
            'size' => 'S / M / L / XL',
            'color' => fake()->safeColorName(),
            'image_url' => 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?auto=format&fit=crop&w=800&q=80',
            'is_featured' => fake()->boolean(20),
        ];
    }
}
