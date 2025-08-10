<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_see_dashboard_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user/dashboard');

        $response->assertOk()
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'total_trips',
                         'upcoming_trips',
                         'total_expenses'
                     ]
                 ]);
    }

    #[Test]
    public function guest_cannot_access_dashboard()
    {
        $this->getJson('/api/user/dashboard')->assertUnauthorized();
    }
}
