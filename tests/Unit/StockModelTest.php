<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_casts_and_attributes(): void
    {
        $user = User::factory()->create();

        $stock = Stock::create([
            'user_id'          => $user->id,
            'name'             => 'Kertas A4',
            'sku'              => 'PAP-001',
            'description'      => 'Kertas 80 gsm',
            'quantity'         => 15,
            'unit'             => 'rim',
            'price'            => 45000.50,
            'min_stock'        => 5,
            'status'           => StockStatus::Available,
            'cover_image_path' => null,
        ]);

        $this->assertIsInt($stock->quantity);
        $this->assertIsInt($stock->min_stock);
        $this->assertInstanceOf(StockStatus::class, $stock->status);
        $this->assertSame(15, $stock->quantity);
        $this->assertTrue($stock->user->is($user));
    }

    public function test_sync_status(): void
    {
        $stock = new Stock(['quantity' => 0, 'min_stock' => 5]);
        $stock->syncStatus();
        $this->assertSame(StockStatus::OutOfStock, $stock->status);

        $stock->quantity = 4;
        $stock->syncStatus();
        $this->assertSame(StockStatus::LowStock, $stock->status);

        $stock->quantity = 20;
        $stock->syncStatus();
        $this->assertSame(StockStatus::Available, $stock->status);
    }

    public function test_factory_states(): void
    {
        $available = Stock::factory()->available()->make();
        $this->assertSame(StockStatus::Available, $available->status);

        $low = Stock::factory()->lowStock()->make();
        $this->assertSame(StockStatus::LowStock, $low->status);

        $out = Stock::factory()->outOfStock()->make();
        $this->assertSame(StockStatus::OutOfStock, $out->status);
    }
}

