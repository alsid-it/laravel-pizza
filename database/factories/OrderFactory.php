<?php

namespace Database\Factories;

use App\Http\Controllers\Api\V1\OrderController;
use App\Models\Drink;
use App\Models\Order;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $orderList = $this->generateOrderList();

        $user = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'order_list' => json_encode($orderList),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'address' => $this->faker->address,
            'status' => $this->faker->randomElement(Order::statuses()),
            'user_id' => $user->id,
        ];
    }

    /**
     * Генерация order_list.
     *
     * @return array
     */
    protected function generateOrderList(): array
    {
        // Генерация случайного количества пицц и напитков
        $pizzas = Pizza::inRandomOrder()->limit($this->faker->numberBetween(1, 5))->get();
        $drinks = Drink::inRandomOrder()->limit($this->faker->numberBetween(1, 3))->get();

        $maxPizzaInOrder = OrderController::getPizzaMaxInOrder();
        $maxDrinkInOrder = OrderController::getDrinkMaxInOrder();

        $orderList = [
            'pizzas' => [],
            'drinks' => [],
        ];

        $pizzaAmount = 0;
        foreach ($pizzas as $pizza) {
            $addPizza = $this->faker->numberBetween(1, 3);
            $pizzaAmount += $addPizza;

            if ($pizzaAmount <= $maxPizzaInOrder) {
                $orderList['pizzas'][$pizza->id] = $addPizza;
            } else {
                break;
            }
        }

        $drinkAmount = 0;
        foreach ($drinks as $drink) {
            $addDrink = $this->faker->numberBetween(1, 3);
            $drinkAmount += $addDrink;

            if ($drinkAmount <= $maxDrinkInOrder) {
                $orderList['drinks'][$drink->id] = $this->faker->numberBetween(1, 2);
            } else {
                break;
            }
        }

        return $orderList;
    }
}
