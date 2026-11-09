<?php

declare(strict_types=1);

namespace App\Features\Auth\Actions;

use Illuminate\Support\Facades\Auth;

class LoginUserAction
{
    /**
     * @param array<string, mixed> $credentials
     */
    public function execute(array $credentials, bool $remember = false): bool
    {
        $login    = (string) ($credentials['username'] ?? $credentials['login'] ?? $credentials['email'] ?? '');
        $password = (string) ($credentials['password'] ?? '');

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $login, 'password' => $password], $remember)) {
            return true;
        }

        $fallbackField = $field === 'email' ? 'username' : 'email';
        return Auth::attempt([$fallbackField => $login, 'password' => $password], $remember);
    }
}
