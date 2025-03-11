<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        // Arrange
        $testMail = fake()->unique()->safeEmail();
        $userData = [
            'name' => fake()->name(),
            'email' => $testMail,
            'password' => Hash::make('password'),
        ];

        // Act
        $response = $this->postJson('/api/v1/register', $userData);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => $testMail,
        ]);
    }

    public function test_user_cannot_register_with_invalid_data()
    {
        // Arrange
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '',
        ];

        // Act
        $response = $this->postJson('/api/v1/register', $userData);

        // Assert
        $response->assertStatus(422);
    }

    public function test_user_can_login()
    {
        // Arrange
        $password = 'password';
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);
        $loginData = [
            'email' => $user->email,
            'password' => $password,
        ];

        // Act
        $response = $this->postJson('/api/v1/login', $loginData);

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        // Arrange
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ];

        // Act
        $response = $this->postJson('/api/v1/login', $loginData);

        // Assert
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_logout()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Act
        $response = $this->getJson('/api/v1/logout');

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Token removed successfully',
        ]);
        $this->assertNull($user->fresh()->tokens->first());
    }

    public function test_unauthenticated_user_cannot_logout()
    {
        // Arrange
        // Ничего не нужно, так как пользователь не аутентифицирован

        // Act
        $response = $this->getJson('/api/v1/logout');

        // Assert
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }
}
