<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Stock\Enums\StockStatus;
use PHPUnit\Framework\TestCase;

class StockStatusTest extends TestCase
{
    public function test_enum_values(): void
    {
        $this->assertSame('available', StockStatus::Available->value);
        $this->assertSame('low_stock', StockStatus::LowStock->value);
        $this->assertSame('out_of_stock', StockStatus::OutOfStock->value);
    }

    public function test_labels(): void
    {
        $this->assertSame('Tersedia', StockStatus::Available->label());
        $this->assertSame('Stok Menipis', StockStatus::LowStock->label());
        $this->assertSame('Habis', StockStatus::OutOfStock->label());
    }

    public function test_badge_classes(): void
    {
        $this->assertStringContainsString('green', StockStatus::Available->badgeClass());
        $this->assertStringContainsString('yellow', StockStatus::LowStock->badgeClass());
        $this->assertStringContainsString('red', StockStatus::OutOfStock->badgeClass());
    }

    public function test_from_quantity_logic(): void
    {
        $this->assertSame(StockStatus::OutOfStock, StockStatus::fromQuantity(0, 5));
        $this->assertSame(StockStatus::OutOfStock, StockStatus::fromQuantity(-3, 5));

        $this->assertSame(StockStatus::LowStock, StockStatus::fromQuantity(5, 5));
        $this->assertSame(StockStatus::LowStock, StockStatus::fromQuantity(3, 5));

        $this->assertSame(StockStatus::Available, StockStatus::fromQuantity(6, 5));
        $this->assertSame(StockStatus::Available, StockStatus::fromQuantity(100, 5));
    }
}

