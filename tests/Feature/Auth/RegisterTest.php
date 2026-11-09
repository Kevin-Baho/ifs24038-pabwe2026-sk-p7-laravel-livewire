<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Features\Auth\Livewire\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_renders(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSeeLivewire(Register::class);
    }

    public function test_validation_rules(): void
    {
        Livewire::test(Register::class)
            ->set('name', '')
            ->set('username', '')
            ->set('email', 'not-an-email')
            ->set('password', '123')
            ->set('password_confirmation', '456')
            ->call('register')
            ->assertHasErrors(['name', 'username', 'email', 'password']);
    }

    public function test_successful_registration(): void
    {
        Livewire::test(Register::class)
            ->set('name', 'John Doe')
            ->set('username', 'johndoe')
            ->set('email', 'john@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('stocks.index'));

        $this->assertAuthenticated();

        $user = User::where('username', 'johndoe')->first();
        $this->assertNotNull($user);
        $this->assertSame('member', $user->role);
    }
}
