<?php

namespace Tests\Feature;

use App\Models\Pizza;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PizzaControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAndAuthenticateUser()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        return $user;
    }

    public function test_can_list_pizzas()
    {
        $this->createAndAuthenticateUser();

        Pizza::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/pizzas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'image', 'createdAt', 'updatedAt']
                ],
            ]);
    }

    public function test_cannot_list_pizzas_when_unauthenticated()
    {
        $response = $this->getJson('/api/v1/pizzas');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_can_create_pizza()
    {
        $this->createAndAuthenticateUser();

        Storage::fake('public');

        $file = UploadedFile::fake()->image('pizza.jpg');

        $response = $this->postJson('/api/v1/pizzas', [
            'name' => 'Pizza 1',
            'image' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Pizza created successfully.',
                'data' => ['name' => 'Pizza 1'],
            ]);

        Storage::disk('public')->assertExists('images/' . $file->hashName());
    }

    public function test_cannot_create_pizza_without_name()
    {
        $this->createAndAuthenticateUser();

        Storage::fake('public');

        $file = UploadedFile::fake()->image('pizza.jpg');

        $response = $this->postJson('/api/v1/pizzas', [
            'image' => $file,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_show_pizza()
    {
        $this->createAndAuthenticateUser();

        $pizza = Pizza::factory()->create();

        $response = $this->getJson("/api/v1/pizzas/{$pizza->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Pizza found.',
                'data' => ['id' => $pizza->id, 'name' => $pizza->name],
            ]);
    }

    public function test_cannot_show_non_existent_pizza()
    {
        $this->createAndAuthenticateUser();

        $pizza = Pizza::factory()->create();

        $nonExistentPizzaId = $pizza->id + 999;

        $response = $this->getJson("/api/v1/pizzas/{$nonExistentPizzaId}");

        $response->assertStatus(404);
    }

    public function test_can_update_pizza()
    {
        $this->createAndAuthenticateUser();

        $pizza = Pizza::factory()->create();

        $response = $this->putJson("/api/v1/pizzas/{$pizza->id}", [
            'name' => 'Updated Pizza',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Pizza updated successfully.',
                'data' => ['name' => 'Updated Pizza'],
            ]);
    }

    public function test_cannot_update_non_existent_pizza()
    {
        $this->createAndAuthenticateUser();

        $pizza = Pizza::factory()->create();

        $nonExistentPizzaId = $pizza->id + 999;

        $response = $this->putJson("/api/v1/pizzas/{$nonExistentPizzaId}", [
            'name' => 'Updated Pizza',
        ]);

        $response->assertStatus(404);
    }

    public function test_can_delete_pizza()
    {
        $this->createAndAuthenticateUser();

        $pizza = Pizza::factory()->create();

        $response = $this->deleteJson("/api/v1/pizzas/{$pizza->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('pizzas', ['id' => $pizza->id]);
    }

    public function test_cannot_delete_non_existent_pizza()
    {
        $this->createAndAuthenticateUser();

        $pizza = Pizza::factory()->create();

        $nonExistentPizzaId = $pizza->id + 999;

        $response = $this->deleteJson("/api/v1/pizzas/{$nonExistentPizzaId}");

        $response->assertStatus(404);
    }
}
