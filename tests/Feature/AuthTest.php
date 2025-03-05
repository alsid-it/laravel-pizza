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
        $testMail = fake()->unique()->safeEmail();

        $userData = [
            'name' => fake()->name(),
            'email' => $testMail,
            'password' => Hash::make('password'),
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'email' => $testMail,
        ]);
    }

    public function test_user_cannot_register_with_invalid_data()
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '',
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(422);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/v1/login', $loginData);

        $response->assertStatus(200);

        $response->assertJsonStructure(['token']);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/v1/login', $loginData);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/logout');

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Token removed successfully',
        ]);

        $this->assertNull($user->fresh()->tokens->first());
    }

    public function test_unauthenticated_user_cannot_logout()
    {
        $response = $this->getJson('/api/v1/logout');

        $response->assertStatus(401);

        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }
}
