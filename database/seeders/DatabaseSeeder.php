<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Features\Stock\Enums\StockStatus;
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

        // 3 initial stocks for the member (available, low_stock, out_of_stock)
        Stock::create([
            'user_id'     => $member->id,
            'name'        => 'MacBook Pro M3 Max',
            'sku'         => 'SKU-MBP-001',
            'description' => 'Laptop workstation berperforma tinggi untuk tim engineering.',
            'quantity'    => 25,
            'unit'        => 'unit',
            'price'       => 38999000.00,
            'min_stock'   => 5,
            'status'      => StockStatus::AVAILABLE,
        ]);

        Stock::create([
            'user_id'     => $member->id,
            'name'        => 'Monitor Dell UltraSharp 27"',
            'sku'         => 'SKU-MON-002',
            'description' => 'Monitor 4K IPS Black untuk visual akurat.',
            'quantity'    => 3,
            'unit'        => 'unit',
            'price'       => 8499000.00,
            'min_stock'   => 5,
            'status'      => StockStatus::LOW_STOCK,
        ]);

        Stock::create([
            'user_id'     => $member->id,
            'name'        => 'Magic Keyboard with Touch ID',
            'sku'         => 'SKU-KEY-003',
            'description' => 'Keyboard wireless Apple dengan sensor sidik jari.',
            'quantity'    => 0,
            'unit'        => 'pcs',
            'price'       => 2499000.00,
            'min_stock'   => 5,
            'status'      => StockStatus::OUT_OF_STOCK,
        ]);
    }
}
