<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Features\Auth\Livewire\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_with_required_ids(): void
    {
        $response = $this->get(route('login'));
        $response->assertOk();
        $response->assertSee('id="login-username"', false);
        $response->assertSee('id="login-password"', false);
        $response->assertSee('id="login-submit"', false);
    }

    public function test_validation_errors(): void
    {
        Livewire::test(Login::class)
            ->set('username', '')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['username', 'password']);
    }

    public function test_invalid_credentials_returns_error(): void
    {
        Livewire::test(Login::class)
            ->set('username', 'wronguser')
            ->set('password', 'wrongpass')
            ->call('login')
            ->assertHasErrors(['username']);
    }

    public function test_member_login_via_username_redirects_to_stocks(): void
    {
        User::factory()->create([
            'username' => 'member1',
            'email'    => 'member1@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'member',
        ]);

        Livewire::test(Login::class)
            ->set('username', 'member1')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('stocks.index'));

        $this->assertAuthenticated();
    }

    public function test_admin_login_via_email_redirects_to_admin_users(): void
    {
        User::factory()->create([
            'username' => 'adminboss',
            'email'    => 'admin@example.com',
            'password' => Hash::make('adminpass'),
            'role'     => 'admin',
        ]);

        Livewire::test(Login::class)
            ->set('username', 'admin@example.com')
            ->set('password', 'adminpass')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.users'));

        $this->assertAuthenticated();
    }
}
