<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Auth\Actions\LoginUserAction;
use App\Features\Auth\Actions\RegisterUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_user_action(): void
    {
        $action = new RegisterUserAction();
        $user   = $action->execute([
            'name'     => 'Budi',
            'username' => 'budi123',
            'email'    => 'budi@example.com',
            'password' => 'secret123',
        ]);

        $this->assertSame('Budi', $user->name);
        $this->assertSame('budi123', $user->username);
        $this->assertSame('member', $user->role);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_login_user_action(): void
    {
        User::factory()->create([
            'username' => 'johndoe',
            'password' => Hash::make('password123'),
        ]);

        $action = new LoginUserAction();

        $success = $action->execute(['username' => 'johndoe', 'password' => 'password123']);
        $this->assertTrue($success);
        $this->assertTrue(Auth::check());

        Auth::logout();

        $failure = $action->execute(['username' => 'johndoe', 'password' => 'wrong-pass']);
        $this->assertFalse($failure);
        $this->assertFalse(Auth::check());
    }
}

