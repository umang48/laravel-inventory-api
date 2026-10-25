<?php

namespace Database\Factories;

use App\Models\Product;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        // Ensure there is at least one category to attach products to
        $category = Category::inRandomOrder()->first() ?? Category::create([
            'name' => 'General',
            'slug' => 'general'
        ]);

        return [
            'category_id' => $category->id,
            'name' => ucwords($this->faker->words(3, true)), // e.g., "Sleek Granite Mouse"
            'sku' => $this->faker->unique()->bothify('SKU-####-????'), // e.g., "SKU-4829-ABCD"
            'price' => $this->faker->randomFloat(2, 100, 50000), // Random price between ₹100 and ₹50,000
            'stock_level' => $this->faker->numberBetween(5, 200),
        ];
    }
}
