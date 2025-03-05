<?php

namespace Database\Factories;

use App\Models\Drink;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    protected array $productTypes = [
        'pizza', 'drink'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $productType = fake()->randomElement($this->productTypes);

        if ($productType == 'pizza') {
            $pizza = Pizza::inRandomOrder()->first() ?? Pizza::factory()->create();
            $productId = $pizza->id;
        } elseif ($productType == 'drink') {
            $drink = Drink::inRandomOrder()->first() ?? Drink::factory()->create();
            $productId = $drink->id;
        }

        $quantity = $this->faker->numberBetween(1, 3);

        return [
            'user_id' => $user->id,
            'product_type' => $productType,
            'product_id' => $productId,
            'quantity' => $quantity,
        ];
    }
}
