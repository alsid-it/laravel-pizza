<?php

namespace Tests\Feature;

use App\Models\Drink;
use App\Models\Order;
use App\Models\Pizza;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function createAndAuthenticateUser()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        return $user;
    }

    public function test_can_list_orders()
    {
        $user = $this->createAndAuthenticateUser();

        Order::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'userId',
                        'orderList',
                        'createdAt',
                    ],
                ],
            ]);
    }

    public function test_cannot_list_orders()
    {
        $response = $this->getJson('/api/v1/orders');

        $response->assertStatus(401);
    }

    public function test_can_create_order()
    {
        $user = $this->createAndAuthenticateUser();

        $pizza = Pizza::factory()->create();
        $drink = Drink::factory()->create();

        $orderData = [
            'order_list' => json_encode([
                'pizzas' => [$pizza->id => 2],
                'drinks' => [$drink->id => 1],
            ]),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'address' => $this->faker->address,
            'status' => 'в работе',
            'user_id' => $user->id,
            'delivery_datetime' => $this->faker->date('Y-m-d H:i:s'),
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Order created successfully.',
            ]);
    }

    public function test_cannot_create_order()
    {
        $pizza = Pizza::factory()->create();
        $drink = Drink::factory()->create();

        $pizzaIdNotFound = $pizza->id + 999;
        $drinkIdNotFound = $drink->id + 999;

        $orderData = [
            'order_list' => json_encode([
                'pizzas' => [$pizzaIdNotFound => 2],
                'drinks' => [$drinkIdNotFound => 1]
            ]),
        ];

        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(401);
    }

    public function test_can_show_order()
    {
        $user = $this->createAndAuthenticateUser();

        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $order->id,
                    'userId' => $order->user_id,
                ],
            ]);
    }

    public function test_cannot_show_order()
    {
        $user = $this->createAndAuthenticateUser();

        $order = Order::factory()->create(['user_id' => $user->id]);

        $orderIdNotFound = $order->id + 999;

        $response = $this->getJson('/api/v1/orders/' . $orderIdNotFound);

        $response->assertStatus(404);
    }

    public function test_can_update()
    {
        $user = $this->createAndAuthenticateUser();

        $order = Order::factory()->create(['user_id' => $user->id]);
        $pizza = Pizza::factory()->create();
        $drink = Drink::factory()->create();

        $updateData = [
            'order_list' => json_encode([
                'pizzas' => [$pizza->id => 3],
                'drinks' => [$drink->id => 2],
            ]),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'address' => $this->faker->address,
            'status' => 'в работе',
            'user_id' => $user->id,
            'delivery_datetime' => $this->faker->date('Y-m-d H:i:s'),
        ];

        $response = $this->putJson("/api/v1/orders/{$order->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Order updated successfully.',
            ]);
    }

    public function test_cannot_update()
    {
        $user = $this->createAndAuthenticateUser();

        $order = Order::factory()->create(['user_id' => $user->id]);
        $pizza = Pizza::factory()->create();
        $pizzaIdNotFound = $pizza->id + 999;
        $drink = Drink::factory()->create();
        $drinkIdNotFound = $drink->id + 999;

        $updateData = [
            'order_list' => json_encode([
                'pizzas' => [$pizzaIdNotFound => 3],
                'drinks' => [$drinkIdNotFound => 2],
            ]),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'address' => $this->faker->address,
            'status' => 'в работе',
            'user_id' => $user->id,
            'delivery_datetime' => $this->faker->date('Y-m-d H:i:s'),
        ];

        $response = $this->putJson("/api/v1/orders/{$drinkIdNotFound}", $updateData);

        $response->assertStatus(404);
    }

    public function test_can_delete_order()
    {
        $user = $this->createAndAuthenticateUser();

        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/v1/orders/{$order->id}");

        $response->assertStatus(204);
    }

    public function test_cannot_delete_order()
    {
        $user = $this->createAndAuthenticateUser();

        $order = Order::factory()->create(['user_id' => $user->id]);
        $orderIdNotFound = $order->id + 999;

        $response = $this->deleteJson('/api/v1/orders/' . $orderIdNotFound);

        $response->assertStatus(404);
    }
}
