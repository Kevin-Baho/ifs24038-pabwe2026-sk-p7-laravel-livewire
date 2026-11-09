<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Features\Stock\Enums\StockStatus;
use PHPUnit\Framework\TestCase;

class StockStatusTest extends TestCase
{
    public function test_enum_values(): void
    {
        $this->assertSame('available', StockStatus::AVAILABLE->value);
        $this->assertSame('low_stock', StockStatus::LOW_STOCK->value);
        $this->assertSame('out_of_stock', StockStatus::OUT_OF_STOCK->value);
    }

    public function test_labels(): void
    {
        $this->assertSame('Tersedia', StockStatus::AVAILABLE->label());
        $this->assertSame('Stok Menipis', StockStatus::LOW_STOCK->label());
        $this->assertSame('Habis', StockStatus::OUT_OF_STOCK->label());
    }

    public function test_badge_classes(): void
    {
        $this->assertStringContainsString('emerald', StockStatus::AVAILABLE->badgeClass());
        $this->assertStringContainsString('amber', StockStatus::LOW_STOCK->badgeClass());
        $this->assertStringContainsString('rose', StockStatus::OUT_OF_STOCK->badgeClass());
    }

    public function test_determine_and_from_quantity_logic(): void
    {
        $this->assertSame(StockStatus::OUT_OF_STOCK, StockStatus::determine(0, 5));
        $this->assertSame(StockStatus::OUT_OF_STOCK, StockStatus::determine(-2, 5));

        $this->assertSame(StockStatus::LOW_STOCK, StockStatus::determine(5, 5));
        $this->assertSame(StockStatus::LOW_STOCK, StockStatus::determine(3, 5));

        $this->assertSame(StockStatus::AVAILABLE, StockStatus::determine(6, 5));
        $this->assertSame(StockStatus::AVAILABLE, StockStatus::determine(100, 5));

        // Test fromQuantity alias
        $this->assertSame(StockStatus::OUT_OF_STOCK, StockStatus::fromQuantity(0, 5));
        $this->assertSame(StockStatus::LOW_STOCK, StockStatus::fromQuantity(2, 5));
        $this->assertSame(StockStatus::AVAILABLE, StockStatus::fromQuantity(10, 5));
    }
}
