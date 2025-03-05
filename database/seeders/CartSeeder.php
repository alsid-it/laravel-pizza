<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Drink;
use App\Models\Order;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productTypes = [
            'pizza',
            'drink'
        ];

        for ($i = 0; $i < 10; $i++) {
            $randomUser = User::inRandomOrder()->first();
            $userId = $randomUser->id;

            $productType = fake()->randomElement($productTypes);

            $productId = 0;

            if ($productType == 'pizza') {
                $randomPizza = Pizza::inRandomOrder()->first();
                $productId = $randomPizza->id;
            } elseif ($productType == 'drink') {
                $randomDrink = Drink::inRandomOrder()->first();
                $productId = $randomDrink->id;
            }

            Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'product_type' => $productType,
                'quantity' => rand(1, 3),
            ]);
        }
    }
}
