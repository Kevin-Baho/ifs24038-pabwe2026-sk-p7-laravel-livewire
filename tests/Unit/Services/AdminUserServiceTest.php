<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

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
        $u1 = User::factory()->create(['name' => 'Alice']);
        Stock::factory()->count(2)->create(['user_id' => $u1->id, 'price' => 10000]);

        $u2 = User::factory()->create(['name' => 'Bob']);

        $all = $this->service->getAllUsersWithMetrics();
        $this->assertCount(2, $all);

        $alice = $all->firstWhere('name', 'Alice');
        $this->assertSame(2, $alice->stocks_count);
        $this->assertEquals(20000, $alice->stocks_sum_price);
    }

    public function test_get_user_by_id(): void
    {
        $user = User::factory()->create(['name' => 'Charlie']);
        $found = $this->service->getUserById($user->id);
        $this->assertSame($user->id, $found->id);
    }

    public function test_get_user_stocks_with_and_without_search(): void
    {
        $user = User::factory()->create();
        Stock::factory()->create(['user_id' => $user->id, 'name' => 'Kamera Canon', 'sku' => 'CAM-01']);
        Stock::factory()->create(['user_id' => $user->id, 'name' => 'Lensa Sony', 'sku' => 'LNS-02']);

        // No search
        $stocks = $this->service->getUserStocks($user);
        $this->assertCount(2, $stocks);

        // Search by name
        $searchName = $this->service->getUserStocks($user, 'Kamera');
        $this->assertCount(1, $searchName);
        $this->assertSame('Kamera Canon', $searchName->first()->name);

        // Search by SKU
        $searchSku = $this->service->getUserStocks($user, 'LNS-02');
        $this->assertCount(1, $searchSku);
        $this->assertSame('Lensa Sony', $searchSku->first()->name);
    }
}
