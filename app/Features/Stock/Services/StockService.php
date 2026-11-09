<?php

declare(strict_types=1);

namespace App\Features\Stock\Services;

use App\Features\Stock\Contracts\StockRepositoryInterface;
use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StockService
{
    public function __construct(
        private readonly StockRepositoryInterface $repository,
    ) {}

    public function listForUser(
        int $userId,
        string $search = '',
        ?StockStatus $status = null,
        string $sortBy = 'name',
        string $sortDir = 'asc',
        int $perPage = 10,
    ): LengthAwarePaginator {
        return $this->repository->paginateForUser($userId, $search, $status, $sortBy, $sortDir, $perPage);
    }

    public function allForUser(int $userId): Collection
    {
        return $this->repository->allForUser($userId);
    }

    public function findForUser(int $userId, int $stockId): Stock
    {
        return $this->repository->findForUser($userId, $stockId);
    }

    public function create(int $userId, array $data, ?UploadedFile $cover = null): Stock
    {
        $data['user_id'] = $userId;

        if ($cover !== null) {
            $data['cover_image_path'] = $cover->store('covers', 'public');
        }

        return $this->repository->create($data);
    }

    public function update(Stock $stock, array $data, ?UploadedFile $cover = null): Stock
    {
        if ($cover !== null) {
            if ($stock->cover_image_path) {
                Storage::disk('public')->delete($stock->cover_image_path);
            }
            $data['cover_image_path'] = $cover->store('covers', 'public');
        }

        return $this->repository->update($stock, $data);
    }

    public function delete(Stock $stock): void
    {
        if ($stock->cover_image_path) {
            Storage::disk('public')->delete($stock->cover_image_path);
        }

        $this->repository->delete($stock);
    }

    public function adjustQuantity(Stock $stock, int $delta): Stock
    {
        return $this->repository->adjustQuantity($stock, $delta);
    }

    public function increment(Stock $stock): Stock
    {
        return $this->adjustQuantity($stock, 1);
    }

    public function decrement(Stock $stock): Stock
    {
        return $this->adjustQuantity($stock, -1);
    }

    public function allForAdmin(int $userId): Collection
    {
        return $this->repository->allForAdmin($userId);
    }
}
