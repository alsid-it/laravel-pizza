<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_user_route()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_user_route()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}
