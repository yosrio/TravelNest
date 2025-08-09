<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_in_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $payload = [
            'email' => 'john@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'token',
                         'user' => ['id', 'name', 'email']
                     ]
                 ]);

        $this->assertNotNull($response->json('data.token'));
    }

    #[Test]
    public function it_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $payload = [
            'email' => 'john@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Invalid credentials'
                 ]);
    }

    #[Test]
    public function it_fails_with_non_existent_email()
    {
        $payload = [
            'email' => 'unknown@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(401)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Invalid credentials'
                 ]);
    }
}
