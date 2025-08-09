<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_view_their_profile()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'bio' => 'Hello World',
            'profile_photo' => '/storage/profile_photos/sample.jpg',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/profile');

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'name' => 'John Doe',
                    'bio' => 'Hello World',
                    'profile_photo' => '/storage/profile_photos/sample.jpg',
                ]
            ]);
    }

    #[Test]
    public function authenticated_user_can_update_their_profile()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Jane Doe',
            'bio' => 'Updated bio',
            'profile_photo' => 'data:image/jpeg;base64,' . base64_encode(file_get_contents(__DIR__ . '/files/sample.jpg')),
        ];

        $response = $this->putJson('/api/user/profile', $payload);

        $response->assertOk()
            ->assertJson([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => [
                    'name' => 'Jane Doe',
                    'bio' => 'Updated bio',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Doe',
            'bio' => 'Updated bio',
        ]);
    }

    #[Test]
    public function guest_cannot_access_profile_endpoints()
    {
        $this->getJson('/user/profile')->assertUnauthorized();
        $this->putJson('/user/profile', [])->assertUnauthorized();
    }
}
