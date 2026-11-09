<?php

declare(strict_types=1);

namespace Tests\Unit\DTO;

use App\Features\Ai\DTO\GeneratedStockItem;
use PHPUnit\Framework\TestCase;

class GeneratedStockItemTest extends TestCase
{
    public function test_from_array_and_to_array(): void
    {
        $data = [
            'name'        => 'Item Test',
            'sku'         => 'SKU-001',
            'description' => 'A test item',
            'quantity'    => 20,
            'unit'        => 'pcs',
            'price'       => 15000.0,
            'min_stock'   => 5,
        ];

        $dto = GeneratedStockItem::fromArray($data);

        $this->assertSame('Item Test', $dto->name);
        $this->assertSame('SKU-001', $dto->sku);
        $this->assertSame('A test item', $dto->description);
        $this->assertSame(20, $dto->quantity);
        $this->assertSame('pcs', $dto->unit);
        $this->assertSame(15000.0, $dto->price);
        $this->assertSame(5, $dto->minStock);

        $array = $dto->toArray();
        $this->assertSame($data['name'], $array['name']);
        $this->assertSame($data['sku'], $array['sku']);
        $this->assertSame($data['description'], $array['description']);
        $this->assertSame($data['quantity'], $array['quantity']);
        $this->assertSame($data['unit'], $array['unit']);
        $this->assertSame($data['price'], $array['price']);
        $this->assertSame($data['min_stock'], $array['min_stock']);
    }

    public function test_from_array_with_empty_defaults(): void
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
