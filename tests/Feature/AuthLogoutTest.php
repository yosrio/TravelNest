<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthLogoutTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_logout_and_token_is_deleted()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Logged out successfully.'
                 ]);

        $this->assertCount(0, $user->tokens()->get());
    }

    #[Test]
    public function guest_cannot_access_logout_endpoint()
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }
}
