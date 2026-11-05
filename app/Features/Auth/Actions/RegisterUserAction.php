<?php

declare(strict_types=1);

namespace App\Features\Auth\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    /**
     * @param array{name: string, username: string, email: string, password: string} $data
     */
    public function execute(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'member',
        ]);
    }
}
