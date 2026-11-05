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
        return Stock::where('user_id', $userId)->get();
    }

    public function paginateForUser(
        int $userId,
        string $search = '',
        ?StockStatus $status = null,
        string $sortBy = 'name',
        string $sortDir = 'asc',
        int $perPage = 10,
    ): LengthAwarePaginator {
        $query = Stock::where('user_id', $userId);

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($status !== null) {
            $query->where('status', $status->value);
        }

        $allowedSorts = ['name', 'sku', 'quantity', 'price', 'status', 'created_at'];
        $sortBy = in_array($sortBy, $allowedSorts, true) ? $sortBy : 'name';
        $sortDir = $sortDir === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortBy, $sortDir)->paginate($perPage);
    }

    public function findForUser(int $userId, int $stockId): Stock
    {
        return Stock::where('user_id', $userId)->findOrFail($stockId);
    }

    public function create(array $data): Stock
    {
        $stock = new Stock($data);
        $stock->syncStatus();
        $stock->save();
        return $stock;
    }

    public function update(Stock $stock, array $data): Stock
    {
        $stock->fill($data);
        $stock->syncStatus();
        $stock->save();
        return $stock;
    }

    public function delete(Stock $stock): void
    {
        $stock->delete();
    }

    public function adjustQuantity(Stock $stock, int $delta): Stock
    {
        $stock->quantity = max(0, $stock->quantity + $delta);
        $stock->syncStatus();
        $stock->save();
        return $stock;
    }

    public function allForAdmin(int $userId): Collection
    {
        return Stock::where('user_id', $userId)->get();
    }
}
