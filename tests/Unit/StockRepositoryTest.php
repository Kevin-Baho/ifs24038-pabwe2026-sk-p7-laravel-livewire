<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Features\Stock\Repositories\StockRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function test_all_for_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Stock::factory()->count(3)->create(['user_id' => $user1->id]);
        Stock::factory()->count(2)->create(['user_id' => $user2->id]);

        $stocks = $this->repository->allForUser($user1->id);
        $this->assertCount(3, $stocks);

        $adminStocks = $this->repository->allForAdmin($user2->id);
        $this->assertCount(2, $adminStocks);
    }

    public function test_paginate_for_user_with_filters_and_sorting(): void
    {
        $user = User::factory()->create();

        Stock::factory()->create([
            'user_id'   => $user->id,
            'name'      => 'Buku Tulis',
            'sku'       => 'BK-001',
            'quantity'  => 10,
            'price'     => 5000,
            'min_stock' => 5,
            'status'    => StockStatus::Available,
        ]);

        Stock::factory()->create([
            'user_id'   => $user->id,
            'name'      => 'Pensil 2B',
            'sku'       => 'PS-002',
            'quantity'  => 2,
            'price'     => 3000,
            'min_stock' => 5,
            'status'    => StockStatus::LowStock,
        ]);

        // Search by name
        $result = $this->repository->paginateForUser($user->id, search: 'Buku');
        $this->assertCount(1, $result->items());
        $this->assertSame('Buku Tulis', $result->items()[0]->name);

        // Search by SKU
        $result = $this->repository->paginateForUser($user->id, search: 'PS-002');
        $this->assertCount(1, $result->items());

        // Filter by status
        $result = $this->repository->paginateForUser($user->id, status: StockStatus::LowStock);
        $this->assertCount(1, $result->items());

        // Sort desc by name
        $result = $this->repository->paginateForUser($user->id, sortBy: 'name', sortDir: 'desc');
        $this->assertSame('Pensil 2B', $result->items()[0]->name);

        // Sort by invalid field defaults to name
        $result = $this->repository->paginateForUser($user->id, sortBy: 'invalid_col');
        $this->assertSame('Buku Tulis', $result->items()[0]->name);
    }

    public function test_find_for_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $stock = Stock::factory()->create(['user_id' => $user1->id]);

        $found = $this->repository->findForUser($user1->id, $stock->id);
        $this->assertSame($stock->id, $found->id);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findForUser($user2->id, $stock->id);
    }

    public function test_crud_operations(): void
    {
        $user = User::factory()->create();

        $created = $this->repository->create([
            'user_id'   => $user->id,
            'name'      => 'Spidol',
            'sku'       => 'SPD-10',
            'quantity'  => 0,
            'unit'      => 'pcs',
            'price'     => 10000,
            'min_stock' => 5,
        ]);

        $this->assertSame('Spidol', $created->name);
        $this->assertSame(StockStatus::OutOfStock, $created->status);

        $updated = $this->repository->update($created, [
            'name'     => 'Spidol Permanen',
            'quantity' => 15,
        ]);

        $this->assertSame('Spidol Permanen', $updated->name);
        $this->assertSame(StockStatus::Available, $updated->status);

        $this->repository->delete($updated);
        $this->assertDatabaseMissing('stocks', ['id' => $created->id]);
    }

    public function test_adjust_quantity(): void
    {
        $user  = User::factory()->create();
        $stock = Stock::factory()->create([
            'user_id'   => $user->id,
            'quantity'  => 5,
            'min_stock' => 5,
            'status'    => StockStatus::LowStock,
        ]);

        // Increment
        $inc = $this->repository->adjustQuantity($stock, 1);
        $this->assertSame(6, $inc->quantity);
        $this->assertSame(StockStatus::Available, $inc->status);

        // Decrement below 0 is clamped to 0
        $dec = $this->repository->adjustQuantity($stock, -100);
        $this->assertSame(0, $dec->quantity);
        $this->assertSame(StockStatus::OutOfStock, $dec->status);
    }
}

