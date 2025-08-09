<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use PHPUnit\Framework\Attributes\Test;

class AuthPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sends_reset_password_email_with_valid_email()
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/password/forgot', [
            'email' => 'john@example.com',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Reset password link has been sent to your email.'
                 ]);

        Mail::assertSent(ResetPasswordMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    #[Test]
    public function it_fails_to_send_reset_password_email_with_invalid_email()
    {
        Mail::fake();

        $response = $this->postJson('/api/password/forgot', [
            'email' => 'notfound@example.com',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);

        Mail::assertNothingSent();
    }

    #[Test]
    public function it_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('oldpassword'),
        ]);

        $token = 'valid-token-123';

        // Insert token ke tabel password_reset_tokens
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/password/reset', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Password has been reset successfully.',
                 ]);

        $this->assertTrue(\Hash::check('newpassword123', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    #[Test]
    public function it_fails_reset_password_with_invalid_token()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('oldpassword'),
        ]);

        $response = $this->postJson('/api/password/reset', [
            'email' => $user->email,
            'token' => 'invalid-token',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Invalid token or email.',
                 ]);
    }

    #[Test]
    public function it_fails_reset_password_with_expired_token()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('oldpassword'),
        ]);

        $token = 'expired-token';

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => now()->subHours(2), // token expired (more than 60 min)
        ]);

        $response = $this->postJson('/api/password/reset', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'Token expired.',
                 ]);
    }
}
