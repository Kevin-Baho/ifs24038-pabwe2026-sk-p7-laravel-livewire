<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        $admin = User::create([
            'name'     => 'Administrator',
            'username' => 'admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('admin'),
            'role'     => 'admin',
        ]);

        // Member account
        $member = User::create([
            'name'     => 'Member User',
            'username' => 'member',
            'email'    => 'member@example.com',
            'password' => Hash::make('member'),
            'role'     => 'member',
        ]);

        // Create some stocks for the member
        Stock::factory()->count(15)->create(['user_id' => $member->id]);
    }
}
