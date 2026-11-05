<?php

declare(strict_types=1);

namespace App\Features\Stock\Contracts;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface StockRepositoryInterface
{
    public function allForUser(int $userId): Collection;

    public function paginateForUser(
        int $userId,
        string $search = '',
        ?StockStatus $status = null,
        string $sortBy = 'name',
        string $sortDir = 'asc',
        int $perPage = 10,
    ): LengthAwarePaginator;

    public function findForUser(int $userId, int $stockId): Stock;

    public function create(array $data): Stock;

    public function update(Stock $stock, array $data): Stock;

    public function delete(Stock $stock): void;

    public function adjustQuantity(Stock $stock, int $delta): Stock;

    public function allForAdmin(int $userId): Collection;
}
