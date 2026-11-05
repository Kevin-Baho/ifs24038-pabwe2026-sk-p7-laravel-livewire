<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Admin\Services\AdminUserService;
use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserServiceTest extends TestCase
{
    use RefreshDatabase;

    private AdminUserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AdminUserService();
    }

    public function test_get_all_users_with_metrics(): void
    {
        $user1 = User::factory()->create(['name' => 'Alice']);
        $user2 = User::factory()->create(['name' => 'Bob']);

        Stock::factory()->create(['user_id' => $user1->id, 'price' => 10000]);
        Stock::factory()->create(['user_id' => $user1->id, 'price' => 20000]);

        $users = $this->service->getAllUsersWithMetrics();

        $this->assertCount(2, $users);
        $alice = $users->firstWhere('name', 'Alice');
        $this->assertSame(2, $alice->stocks_count);
        $this->assertSame(30000.0, (float) $alice->stocks_sum_price);
    }

    public function test_get_user_by_id(): void
    {
        $user  = User::factory()->create();
        $found = $this->service->getUserById($user->id);

        $this->assertSame($user->id, $found->id);
    }

    public function test_get_user_stocks_with_search(): void
    {
        $user = User::factory()->create();
        Stock::factory()->create(['user_id' => $user->id, 'name' => 'Barang A', 'sku' => 'A1']);
        Stock::factory()->create(['user_id' => $user->id, 'name' => 'Barang B', 'sku' => 'B1']);

        $all   = $this->service->getUserStocks($user);
        $this->assertCount(2, $all);

        $searched = $this->service->getUserStocks($user, 'Barang A');
        $this->assertCount(1, $searched);
        $this->assertSame('Barang A', $searched->first()->name);

        $skuSearched = $this->service->getUserStocks($user, 'B1');
        $this->assertCount(1, $skuSearched);
    }
}

