<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_guest_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_root_redirects_admin_to_admin_users(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/');
        $response->assertRedirect(route('admin.users'));
    }

    public function test_root_redirects_member_to_stocks(): void
    {
        $member = User::factory()->member()->create();

        $response = $this->actingAs($member)->get('/');
        $response->assertRedirect(route('stocks.index'));
    }

    public function test_logout_redirects_to_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
