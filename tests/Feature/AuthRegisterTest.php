<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_registers_a_user_with_valid_data()
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Registration successful',
                     'data' => [
                         'name' => 'John Doe',
                         'email' => 'john@example.com'
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);
    }

    #[Test]
    public function it_fails_with_invalid_data()
    {
        $payload = [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short'
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    #[Test]
    public function it_fails_with_duplicate_email()
    {
        User::factory()->create(['email' => 'john@example.com']);

        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
}
