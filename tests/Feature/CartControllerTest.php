<?php

namespace Tests\Feature;

use App\Models\Drink;
use App\Models\Pizza;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Pizza $pizza;
    protected Drink $drink;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->pizza = Pizza::factory()->create();
        $this->drink = Drink::factory()->create();
    }

    public function test_can_get_cart_list(): void
    {
        Sanctum::actingAs($this->user);
        Cart::factory()->create(['user_id' => $this->user->id, 'product_type' => 'pizza', 'product_id' => $this->pizza->id]);
        $response = $this->getJson('/api/v1/carts');
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([$this->user->id => ['pizza' => [$this->pizza->id]]]);
    }

    public function test_cannot_get_cart_list(): void
    {
        $response = $this->getJson('/api/v1/carts');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_can_create_cart(): void
    {
        Sanctum::actingAs($this->user);
        $data = [
            'pizzas' => json_encode([$this->pizza->id => 2]),
            'drinks' => json_encode([$this->drink->id => 3])
        ];
        $response = $this->postJson('/api/v1/carts', $data);
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson(['message' => 'Данные успешно добавлены в корзину']);
    }

    public function test_cannot_create_cart(): void
    {
        Sanctum::actingAs($this->user);
        $data = ['pizzas' => 'invalid', 'drinks' => 'invalid'];
        $response = $this->postJson('/api/v1/carts', $data);
        $response->assertStatus(Response::HTTP_BAD_REQUEST); // Bad request
    }

    public function test_can_show_cart(): void
    {
        Sanctum::actingAs($this->user);
        Cart::factory()->create(['user_id' => $this->user->id, 'product_type' => 'pizza', 'product_id' => $this->pizza->id]);
        $response = $this->getJson('/api/v1/carts/' . $this->user->id);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['pizza' => [$this->pizza->id]]);
    }

    public function test_cannot_show_cart(): void
    {
        Sanctum::actingAs($this->user);
        $nonExistentUserId = $this->user->id + 999;
        $response = $this->getJson('/api/v1/carts/' . $nonExistentUserId);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdateSuccess(): void
    {
        Sanctum::actingAs($this->user);
        Cart::factory()->create(['user_id' => $this->user->id, 'product_type' => 'pizza', 'product_id' => $this->pizza->id]);
        $data = [
            'pizzas' => json_encode([$this->pizza->id => 5]),
            'drinks' => json_encode([$this->drink->id => 1])
        ];
        $response = $this->putJson('/api/v1/carts/' . $this->user->id, $data);
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson(['message' => 'Данные корзины успешно обновлены']);
    }

    public function testUpdateFailure(): void
    {
        Sanctum::actingAs($this->user);
        $nonExistentUserId = $this->user->id + 999;
        $data = ['pizzas' => 'invalid', 'drinks' => 'invalid'];
        $response = $this->putJson('/api/v1/carts/' . $nonExistentUserId, $data);
        $response->assertStatus(Response::HTTP_NOT_FOUND); // User not found
    }

    public function testDestroySuccess(): void
    {
        Sanctum::actingAs($this->user);
        Cart::factory()->create(['user_id' => $this->user->id]);
        $response = $this->deleteJson('/api/v1/carts/' . $this->user->id);
        $response->assertStatus(Response::HTTP_NO_CONTENT); // No content
    }

    public function testDestroyFailure(): void
    {
        Sanctum::actingAs($this->user);
        $nonExistentUserId = $this->user->id + 999;
        $response = $this->deleteJson('/api/v1/carts/' . $nonExistentUserId);
        $response->assertStatus(Response::HTTP_BAD_REQUEST); // User not found
    }
}
