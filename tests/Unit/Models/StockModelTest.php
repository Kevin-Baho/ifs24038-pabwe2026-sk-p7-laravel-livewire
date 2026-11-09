<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StockModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_casts_and_relations(): void
    {
        $user = User::factory()->create();
        $stock = Stock::create([
            'user_id'   => $user->id,
            'name'      => 'MacBook Pro',
            'sku'       => 'MBP-01',
            'quantity'  => '25',
            'unit'      => 'unit',
            'price'     => '38999000.50',
            'min_stock' => '5',
            'status'    => StockStatus::AVAILABLE->value,
        ]);

        $this->assertIsInt($stock->quantity);
        $this->assertSame(25, $stock->quantity);
        $this->assertIsInt($stock->min_stock);
        $this->assertSame(5, $stock->min_stock);
        $this->assertSame('38999000.50', (string) $stock->price);
        $this->assertInstanceOf(StockStatus::class, $stock->status);
        $this->assertSame(StockStatus::AVAILABLE, $stock->status);
        $this->assertInstanceOf(User::class, $stock->user);
        $this->assertSame($user->id, $stock->user->id);
    }

    public function test_cover_image_url(): void
    {
        Storage::fake('public');

        $stock = Stock::factory()->create(['cover_image_path' => null]);
        $this->assertNull($stock->coverImageUrl());

        // File path exists in DB but not in storage
        $stock->cover_image_path = 'covers/non_existent.jpg';
        $this->assertNull($stock->coverImageUrl());

        // File exists in storage
        Storage::disk('public')->put('covers/existing.jpg', 'fake-image');
        $stock->cover_image_path = 'covers/existing.jpg';
        $this->assertNotNull($stock->coverImageUrl());
        $this->assertStringContainsString('covers/existing.jpg', $stock->coverImageUrl());
    }

    public function test_sync_status(): void
    {
        $stock = new Stock(['quantity' => 0, 'min_stock' => 5]);
        $stock->syncStatus();
        $this->assertSame(StockStatus::OUT_OF_STOCK, $stock->status);

        $stock->quantity = 4;
        $stock->syncStatus();
        $this->assertSame(StockStatus::LOW_STOCK, $stock->status);

        $stock->quantity = 15;
        $stock->syncStatus();
        $this->assertSame(StockStatus::AVAILABLE, $stock->status);
    }

    public function test_factory_states(): void
    {
        $user = User::factory()->create();

        $available = Stock::factory()->available()->create(['user_id' => $user->id]);
        $this->assertSame(StockStatus::AVAILABLE, $available->status);

        $low = Stock::factory()->lowStock()->create(['user_id' => $user->id]);
        $this->assertSame(StockStatus::LOW_STOCK, $low->status);

        $out = Stock::factory()->outOfStock()->create(['user_id' => $user->id]);
        $this->assertSame(StockStatus::OUT_OF_STOCK, $out->status);
    }
}
