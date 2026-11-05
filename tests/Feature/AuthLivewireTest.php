<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Features\Auth\Livewire\Login;
use App\Features\Auth\Livewire\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class AuthLivewireTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_validation_and_failures(): void
    {
        Livewire::test(Login::class)
            ->call('login')
            ->assertHasErrors(['username' => 'required', 'password' => 'required'])
            ->set('username', 'nonexistent')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['username']);
    }

    public function test_member_login_redirects_to_stocks(): void
    {
        User::factory()->member()->create([
            'username' => 'memberuser',
            'password' => Hash::make('secret123'),
        ]);

        Livewire::test(Login::class)
            ->set('username', 'memberuser')
            ->set('password', 'secret123')
            ->call('login')
            ->assertRedirect(route('stocks.index'));

        $this->assertTrue(Auth::check());
    }

    public function test_admin_login_redirects_to_admin_users(): void
    {
        User::factory()->admin()->create([
            'username' => 'adminuser',
            'password' => Hash::make('admin123'),
        ]);

        Livewire::test(Login::class)
            ->set('username', 'adminuser')
            ->set('password', 'admin123')
            ->call('login')
            ->assertRedirect(route('admin.users'));

        $this->assertTrue(Auth::check());
    }

    public function test_register_validation_and_success(): void
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->call('register')
            ->assertHasErrors(['name', 'username', 'email', 'password'])
            ->set('name', 'Budi Santoso')
            ->set('username', 'budis')
            ->set('email', 'budi@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('stocks.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'budis',
            'email'    => 'budi@example.com',
            'role'     => 'member',
        ]);
        $this->assertTrue(Auth::check());
    }

    public function test_root_redirects(): void
    {
        // Guest -> login
        $this->get('/')->assertRedirect(route('login'));

        // Member -> stocks
        $member = User::factory()->member()->create();
        $this->actingAs($member)->get('/')->assertRedirect(route('stocks.index'));

        // Admin -> admin.users
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get('/')->assertRedirect(route('admin.users'));
    }

    public function test_logout(): void
    {
        $member = User::factory()->member()->create();

        $this->actingAs($member)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertFalse(Auth::check());
    }
}

