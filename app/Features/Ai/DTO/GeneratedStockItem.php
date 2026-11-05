<?php

declare(strict_types=1);

namespace App\Features\Ai\DTO;

final readonly class GeneratedStockItem
{
    public function __construct(
        public string $name,
        public string $sku,
        public string $description,
        public int    $quantity,
        public string $unit,
        public float  $price,
        public int    $minStock,
    ) {}

    /**
     * Create from raw associative array (AI response).
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name:        (string) ($data['name'] ?? ''),
            sku:         (string) ($data['sku'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            quantity:    (int)    ($data['quantity'] ?? 0),
            unit:        (string) ($data['unit'] ?? 'pcs'),
            price:       (float)  ($data['price'] ?? 0),
            minStock:    (int)    ($data['min_stock'] ?? 5),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'name'        => $this->name,
            'sku'         => $this->sku,
            'description' => $this->description,
            'quantity'    => $this->quantity,
            'unit'        => $this->unit,
            'price'       => $this->price,
            'min_stock'   => $this->minStock,
        ];
    }
}
