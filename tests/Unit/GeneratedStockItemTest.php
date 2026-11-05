<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Ai\DTO\GeneratedStockItem;
use PHPUnit\Framework\TestCase;

class GeneratedStockItemTest extends TestCase
{
    public function test_dto_properties_and_arrays(): void
    {
        $data = [
            'name'        => 'Pulpen Gel',
            'sku'         => 'PEN-001',
            'description' => 'Tinta hitam',
            'quantity'    => 20,
            'unit'        => 'box',
            'price'       => 25000.0,
            'min_stock'   => 5,
        ];

        $dto = GeneratedStockItem::fromArray($data);

        $this->assertSame('Pulpen Gel', $dto->name);
        $this->assertSame('PEN-001', $dto->sku);
        $this->assertSame('Tinta hitam', $dto->description);
        $this->assertSame(20, $dto->quantity);
        $this->assertSame('box', $dto->unit);
        $this->assertSame(25000.0, $dto->price);
        $this->assertSame(5, $dto->minStock);

        $this->assertSame($data, $dto->toArray());
    }

    public function test_dto_from_array_with_defaults(): void
    {
        $dto = GeneratedStockItem::fromArray([]);

        $this->assertSame('', $dto->name);
        $this->assertSame('', $dto->sku);
        $this->assertSame('', $dto->description);
        $this->assertSame(0, $dto->quantity);
        $this->assertSame('pcs', $dto->unit);
        $this->assertSame(0.0, $dto->price);
        $this->assertSame(5, $dto->minStock);
    }
}

