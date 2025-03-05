<?php

namespace Database\Seeders;

use App\Http\Controllers\Api\V1\OrderController;
use App\Models\Drink;
use App\Models\Order;
use App\Models\Pizza;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        // Создаем 10 заказов
        for ($i = 0; $i < 10; $i++) {
            // Генерация order_list
            $order_list = $this->generateOrderList();

            // Получаем случайного пользователя
            $randomUser = User::inRandomOrder()->first();

            if ($randomUser) {
                $userId = $randomUser->id;
            } else {
                User::factory()->count(1)->create();
                $randomUser = User::inRandomOrder()->first();
                $userId = $randomUser->id;
            }

            // Создаем заказ
            Order::create([
                'order_list' => $order_list,
                'phone' => $faker->phoneNumber,
                'email' => $faker->email,
                'address' => $faker->address,
                'status' => $faker->randomElement(Order::statuses()),
                'user_id' => $userId,
            ]);
        }
    }

    protected function generateOrderList(): string
    {
        // Генерация массива для напитков
        $drinks = [];
        $totalDrinks = 0;
        $maxDrinks = OrderController::getDrinkMaxInOrder(); // Максимальное количество напитков
        $drinkCount = rand(1, 5); // Количество разных напитков в заказе
        $minDrinkId = Drink::min('id');
        $maxDrinkId = Drink::max('id');

        for ($i = 0; $i < $drinkCount; $i++) {
            $drinkId = rand($minDrinkId, $maxDrinkId); // ID напитка
            $quantity = rand(1, 3); // Количество напитков (от 1 до 3)

            // Проверка, чтобы общее количество напитков не превышало 10
            if ($totalDrinks + $quantity > $maxDrinks) {
                $quantity = $maxDrinks - $totalDrinks;
            }

            if ($quantity > 0) {
                $drinks[$drinkId] = $quantity;
                $totalDrinks += $quantity;
            }

            if ($totalDrinks >= $maxDrinks) {
                break;
            }
        }

        // Генерация массива для пицц
        $pizzas = [];
        $totalPizzas = 0;
        $maxPizzas = OrderController::MAX_PIZZAS_IN_ORDER; // Максимальное количество пицц
        $pizzaCount = rand(1, 5); // Количество разных пицц в заказе
        $minPizzaId = Pizza::min('id');
        $maxPizzaId = Pizza::max('id');

        for ($i = 0; $i < $pizzaCount; $i++) {
            $pizzaId = rand($minPizzaId, $maxPizzaId); // ID пиццы
            $quantity = rand(1, 5); // Количество пицц (от 1 до 5)

            // Проверка, чтобы общее количество пицц не превышало 20
            if ($totalPizzas + $quantity > $maxPizzas) {
                $quantity = $maxPizzas - $totalPizzas;
            }

            if ($quantity > 0) {
                $pizzas[$pizzaId] = $quantity;
                $totalPizzas += $quantity;
            }

            if ($totalPizzas >= $maxPizzas) {
                break;
            }
        }

        return json_encode([
            'drinks' => $drinks,
            'pizzas' => $pizzas,
        ]);
    }
}
