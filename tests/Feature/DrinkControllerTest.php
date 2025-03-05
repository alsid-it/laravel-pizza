<?php

namespace Tests\Feature;

use App\Models\Drink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DrinkControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAndAuthenticateUser()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        return $user;
    }

    public function test_can_list_drinks ()
    {
        $this->createAndAuthenticateUser();

        Drink::factory()->count(10)->create();

        $response = $this->getJson('/api/v1/drinks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'image', 'createdAt', 'updatedAt']
                ],
            ]);
    }

    public function test_cannot_list_drinks_when_unauthenticated () {
        $response = $this->getJson('/api/v1/drinks');

        $response->assertStatus(401);
    }

    public function test_can_create_drink () {
        $this->createAndAuthenticateUser();

        Storage::fake('public');

        $file = UploadedFile::fake()->image('drink.jpg');

        $response = $this->postJson('/api/v1/drinks', [
            'name' => 'Drink 1',
            'image' => $file,
        ]);

        $response->assertStatus(201);

        Storage::disk('public')->assertExists('images/' . $file->hashName());
    }

    public function test_cannot_create_drink_without_name () {
        $this->createAndAuthenticateUser();

        Storage::fake('public');

        $file = UploadedFile::fake()->image('drink.jpg');

        $response = $this->postJson('/api/v1/drinks', [
            'image' => $file,
        ]);

        $response->assertStatus(422);
    }

    public function test_can_show_drink () {
        $this->createAndAuthenticateUser();

        $drink = Drink::factory()->create();

        $response = $this->getJson('/api/v1/drinks/' . $drink->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Drink found.',
                'data' => ['id' => $drink->id, 'name' => $drink->name],
            ]);
    }

    public function test_cannot_show_drink_non_existent_drink()
    {
        $this->createAndAuthenticateUser();

        $drink = Drink::factory()->create();

        $nonExistentDrinkId = $drink->id + 999;

        $response = $this->getJson("/api/v1/drinks/{$nonExistentDrinkId}");

        $response->assertStatus(404);
    }

    public function test_can_update_drink()
    {
        $this->createAndAuthenticateUser();

        $drink = Drink::factory()->create();

        $response = $this->putJson("/api/v1/drinks/{$drink->id}", [
            'name' => 'Updated Drink',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Drink updated successfully.',
                'data' => ['name' => 'Updated Drink'],
            ]);
    }

    public function test_cannot_update_non_existent_drink()
    {
        $this->createAndAuthenticateUser();

        $drink = Drink::factory()->create();

        $nonExistentDrinkId = $drink->id + 999;

        $response = $this->putJson("/api/v1/drinks/{$nonExistentDrinkId}", [
            'name' => 'Updated Drink',
        ]);

        $response->assertStatus(404);
    }

    public function test_can_delete_drink()
    {
        $this->createAndAuthenticateUser();

        $drink = Drink::factory()->create();

        $response = $this->deleteJson("/api/v1/drinks/{$drink->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('drinks', ['id' => $drink->id]);
    }

    public function test_cannot_delete_non_existent_drink()
    {
        $this->createAndAuthenticateUser();

        $drink = Drink::factory()->create();

        $nonExistentDrinkId = $drink->id + 999;

        $response = $this->deleteJson("/api/v1/drinks/{$nonExistentDrinkId}");

        $response->assertStatus(404);
    }

}
