<?php

namespace Tests\Feature;

use App\Models\Drink;
use App\Models\Pizza;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $pizza;
    protected $drink;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->pizza = Pizza::factory()->create();
        $this->drink = Drink::factory()->create();
    }

    public function test_can_get_cart_list()
    {
        Sanctum::actingAs($this->user);
        Cart::factory()->create(['user_id' => $this->user->id, 'product_type' => 'pizza', 'product_id' => $this->pizza->id]);
        $response = $this->getJson('/api/v1/carts');
        $response->assertStatus(200)
            ->assertJsonStructure([$this->user->id => ['pizza' => [$this->pizza->id]]]);
    }

    public function test_cannot_get_cart_list()
    {
        $response = $this->getJson('/api/v1/carts');
        $response->assertStatus(401);
    }

    public function test_can_create_cart()
    {
        Sanctum::actingAs($this->user);
        $data = [
            'pizzas' => json_encode([$this->pizza->id => 2]),
            'drinks' => json_encode([$this->drink->id => 3])
        ];
        $response = $this->postJson('/api/v1/carts', $data);
        $response->assertStatus(201)
            ->assertJson(['message' => 'Данные успешно добавлены в корзину']);
    }

    public function test_cannot_create_cart()
    {
        Sanctum::actingAs($this->user);
        $data = ['pizzas' => 'invalid', 'drinks' => 'invalid'];
        $response = $this->postJson('/api/v1/carts', $data);
        $response->assertStatus(400); // Bad request
    }

    public function test_can_show_cart()
    {
        Sanctum::actingAs($this->user);
        Cart::factory()->create(['user_id' => $this->user->id, 'product_type' => 'pizza', 'product_id' => $this->pizza->id]);
        $response = $this->getJson('/api/v1/carts/' . $this->user->id);
        $response->assertStatus(200)
            ->assertJsonStructure(['pizza' => [$this->pizza->id]]);
    }

    public function test_cannot_show_cart()
    {
        Sanctum::actingAs($this->user);
        $nonExistentUserId = $this->user->id + 999;
        $response = $this->getJson('/api/v1/carts/' . $nonExistentUserId);
        $response->assertStatus(400);
    }

    public function testUpdateSuccess()
    {
        Sanctum::actingAs($this->user);
        Cart::factory()->create(['user_id' => $this->user->id, 'product_type' => 'pizza', 'product_id' => $this->pizza->id]);
        $data = [
            'pizzas' => json_encode([$this->pizza->id => 5]),
            'drinks' => json_encode([$this->drink->id => 1])
        ];
        $response = $this->putJson('/api/v1/carts/' . $this->user->id, $data);
        $response->assertStatus(201)
            ->assertJson(['message' => 'Данные корзины успешно обновлены']);
    }

    public function testUpdateFailure()
    {
        Sanctum::actingAs($this->user);
        $nonExistentUserId = $this->user->id + 999;
        $data = ['pizzas' => 'invalid', 'drinks' => 'invalid'];
        $response = $this->putJson('/api/v1/carts/' . $nonExistentUserId, $data);
        $response->assertStatus(404); // User not found
    }

    public function testDestroySuccess()
    {
        Sanctum::actingAs($this->user);
        Cart::factory()->create(['user_id' => $this->user->id]);
        $response = $this->deleteJson('/api/v1/carts/' . $this->user->id);
        $response->assertStatus(204); // No content
    }

    public function testDestroyFailure()
    {
        Sanctum::actingAs($this->user);
        $nonExistentUserId = $this->user->id + 999;
        $response = $this->deleteJson('/api/v1/carts/' . $nonExistentUserId);
        $response->assertStatus(400); // User not found
    }
}
