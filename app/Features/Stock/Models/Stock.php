<?php

declare(strict_types=1);

namespace App\Features\Stock\Models;

use App\Features\Stock\Enums\StockStatus;
use App\Models\User;
use Database\Factories\StockFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Stock extends Model
{
    /** @use HasFactory<StockFactory> */
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'user_id',
        'name',
        'sku',
        'description',
        'quantity',
        'unit',
        'price',
        'min_stock',
        'status',
        'cover_image_path',
    ];

    protected function casts(): array
    {
        return [
            'quantity'  => 'integer',
            'min_stock' => 'integer',
            'price'     => 'decimal:2',
            'status'    => StockStatus::class,
        ];
    }

    protected static function newFactory(): StockFactory
    {
        return StockFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coverImageUrl(): ?string
    {
        if ($this->cover_image_path && Storage::disk('public')->exists($this->cover_image_path)) {
            return Storage::disk('public')->url($this->cover_image_path);
        }

        return null;
    }

    public function syncStatus(): void
    {
        $quantity = (int) ($this->quantity ?? 0);
        $minStock = (int) ($this->min_stock ?? 5);
        $this->status = StockStatus::determine($quantity, $minStock);
    }
}
