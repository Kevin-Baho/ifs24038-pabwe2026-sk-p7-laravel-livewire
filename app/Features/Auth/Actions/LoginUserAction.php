<?php

declare(strict_types=1);

namespace App\Features\Auth\Actions;

use Illuminate\Support\Facades\Auth;

class LoginUserAction
{
    /**
     * @param array{username: string, password: string} $credentials
     */
    public function execute(array $credentials, bool $remember = false): bool
    {
        return Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
        ], $remember);
    }
}
