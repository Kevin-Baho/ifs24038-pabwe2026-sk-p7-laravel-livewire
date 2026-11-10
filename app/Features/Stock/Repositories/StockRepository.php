<?php

declare(strict_types=1);

namespace App\Features\Stock\Repositories;

use App\Features\Stock\Contracts\StockRepositoryInterface;
use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StockRepository implements StockRepositoryInterface
{
    public function allForUser(int $userId): Collection
    {
        return Stock::query()
            ->where('user_id', $userId)
            ->latest('id')
            ->get();
    }

    public function paginateForUser(
        int $userId,
        string $search = '',
        ?StockStatus $status = null,
        string $sortBy = 'name',
        string $sortDir = 'asc',
        int $perPage = 10,
    ): LengthAwarePaginator {
        $query = Stock::query()->where('user_id', $userId);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status !== null) {
            $query->where('status', $status->value);
        }

        $allowedSorts = ['name', 'sku', 'quantity', 'price', 'status', 'created_at'];
        $sortBy  = in_array($sortBy, $allowedSorts, true) ? $sortBy : 'name';
        $sortDir = $sortDir === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    }

    public function findForUser(int $userId, int $stockId): Stock
    {
        return Stock::query()
            ->where('user_id', $userId)
            ->findOrFail($stockId);
    }

    public function create(array $data): Stock
    {
        $quantity = (int) ($data['quantity'] ?? 0);
        $minStock = (int) ($data['min_stock'] ?? 5);
        $data['status'] = StockStatus::determine($quantity, $minStock)->value;

        return Stock::create($data);
    }

    public function update(Stock $stock, array $data): Stock
    {
        $stock->fill($data);

        // Re-sync status whenever quantity or min_stock changes
        $stock->syncStatus();
        $stock->save();

        return $stock->fresh() ?? $stock;
    }

    public function delete(Stock $stock): void
    {
        $stock->delete();
    }

    public function adjustQuantity(Stock $stock, int $delta): Stock
    {
        $stock->quantity = max(0, $stock->quantity + $delta);
        $stock->status   = StockStatus::determine($stock->quantity, $stock->min_stock)->value;
        $stock->save();

        return $stock;
    }

    public function allForAdmin(int $userId): Collection
    {
        return Stock::query()
            ->where('user_id', $userId)
            ->latest('id')
            ->get();
    }
}
