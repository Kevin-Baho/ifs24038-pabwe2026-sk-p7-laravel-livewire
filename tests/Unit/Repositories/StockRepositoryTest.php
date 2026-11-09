<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Features\Stock\Repositories\StockRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private StockRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new StockRepository();
    }

    public function test_crud_operations(): void
    {
        $user = User::factory()->create();

        // Create
        $stock = $this->repository->create([
            'user_id'   => $user->id,
            'name'      => 'Keyboard Mechanical',
            'sku'       => 'KB-001',
            'quantity'  => 10,
            'unit'      => 'pcs',
            'price'     => 750000,
            'min_stock' => 5,
        ]);

        $this->assertInstanceOf(Stock::class, $stock);
        $this->assertSame(StockStatus::AVAILABLE, $stock->status);
        $this->assertDatabaseHas('stocks', ['id' => $stock->id, 'name' => 'Keyboard Mechanical']);

        // Find for user
        $found = $this->repository->findForUser($user->id, $stock->id);
        $this->assertSame($stock->id, $found->id);

        // Update
        $updated = $this->repository->update($stock, [
            'name'     => 'Keyboard Wireless',
            'quantity' => 2,
        ]);
        $this->assertSame('Keyboard Wireless', $updated->name);
        $this->assertSame(StockStatus::LOW_STOCK, $updated->status);

        // Adjust quantity
        $adjusted = $this->repository->adjustQuantity($updated, 5);
        $this->assertSame(7, $adjusted->quantity);
        $this->assertSame(StockStatus::AVAILABLE, $adjusted->status);

        // Adjust below 0 should clamp to 0
        $clamped = $this->repository->adjustQuantity($adjusted, -20);
        $this->assertSame(0, $clamped->quantity);
        $this->assertSame(StockStatus::OUT_OF_STOCK, $clamped->status);

        // All for user & All for admin
        $all = $this->repository->allForUser($user->id);
        $this->assertCount(1, $all);

        $adminAll = $this->repository->allForAdmin($user->id);
        $this->assertCount(1, $adminAll);

        // Delete
        $this->repository->delete($clamped);
        $this->assertDatabaseMissing('stocks', ['id' => $stock->id]);
    }

    public function test_paginate_for_user_with_search_and_filters(): void
    {
        $user = User::factory()->create();

        Stock::factory()->create([
            'user_id'  => $user->id,
            'name'     => 'Monitor LG 24"',
            'sku'      => 'LG-24',
            'quantity' => 20,
            'status'   => StockStatus::AVAILABLE->value,
        ]);

        Stock::factory()->create([
            'user_id'  => $user->id,
            'name'     => 'Mouse Wireless',
            'sku'      => 'MS-01',
            'quantity' => 2,
            'status'   => StockStatus::LOW_STOCK->value,
        ]);

        // Default pagination
        $paginated = $this->repository->paginateForUser($user->id);
        $this->assertSame(2, $paginated->total());

        // Search by name
        $searchResult = $this->repository->paginateForUser($user->id, 'Monitor');
        $this->assertSame(1, $searchResult->total());
        $this->assertSame('Monitor LG 24"', $searchResult->items()[0]->name);

        // Search by sku
        $skuResult = $this->repository->paginateForUser($user->id, 'MS-01');
        $this->assertSame(1, $skuResult->total());

        // Filter by status
        $statusResult = $this->repository->paginateForUser($user->id, '', StockStatus::LOW_STOCK);
        $this->assertSame(1, $statusResult->total());
        $this->assertSame('Mouse Wireless', $statusResult->items()[0]->name);

        // Sorting
        $sorted = $this->repository->paginateForUser($user->id, '', null, 'quantity', 'desc');
        $this->assertSame(20, $sorted->items()[0]->quantity);

        // Fallback invalid sort
        $fallback = $this->repository->paginateForUser($user->id, '', null, 'invalid_col', 'desc');
        $this->assertSame(2, $fallback->total());
    }
}
