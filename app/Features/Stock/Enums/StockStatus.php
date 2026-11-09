<?php

declare(strict_types=1);

namespace App\Features\Stock\Enums;

enum StockStatus: string
{
    case AVAILABLE = 'available';
    case LOW_STOCK = 'low_stock';
    case OUT_OF_STOCK = 'out_of_stock';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Tersedia',
            self::LOW_STOCK => 'Stok Menipis',
            self::OUT_OF_STOCK => 'Habis',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::AVAILABLE => 'bg-emerald-950/60 text-emerald-400 border border-emerald-800/60',
            self::LOW_STOCK => 'bg-amber-950/60 text-amber-400 border border-amber-800/60',
            self::OUT_OF_STOCK => 'bg-rose-950/60 text-rose-400 border border-rose-800/60',
        };
    }

    public static function determine(int $quantity, int $minStock): self
    {
        if ($quantity <= 0) {
            return self::OUT_OF_STOCK;
        }

        if ($quantity <= $minStock) {
            return self::LOW_STOCK;
        }

        return self::AVAILABLE;
    }

    public static function fromQuantity(int $quantity, int $minStock): self
    {
        return self::determine($quantity, $minStock);
    }
}
