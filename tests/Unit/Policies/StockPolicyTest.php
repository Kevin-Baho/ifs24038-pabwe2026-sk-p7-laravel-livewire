<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Features\Stock\Models\Stock;
use App\Features\Stock\Policies\StockPolicy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockPolicyTest extends TestCase
{
    use RefreshDatabase;

    private StockPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new StockPolicy();
    }

    public function test_view_any(): void
    {
        $user = User::factory()->make();
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view(): void
    {
        $owner = User::factory()->create(['id' => 10, 'role' => 'member']);
        $admin = User::factory()->create(['id' => 11, 'role' => 'admin']);
        $stranger = User::factory()->create(['id' => 12, 'role' => 'member']);

        $stock = Stock::factory()->create(['user_id' => $owner->id]);

        $this->assertTrue($this->policy->view($owner, $stock));
        $this->assertTrue($this->policy->view($admin, $stock));
        $this->assertFalse($this->policy->view($stranger, $stock));
    }

    public function test_create(): void
    {
        $member = User::factory()->make(['role' => 'member']);
        $admin = User::factory()->make(['role' => 'admin']);

        $this->assertTrue($this->policy->create($member));
        $this->assertFalse($this->policy->create($admin));
    }

    public function test_update_and_delete(): void
    {
        $owner = User::factory()->create(['id' => 20, 'role' => 'member']);
        $admin = User::factory()->create(['id' => 21, 'role' => 'admin']);
        $stranger = User::factory()->create(['id' => 22, 'role' => 'member']);

        $stock = Stock::factory()->create(['user_id' => $owner->id]);

        // Update
        $this->assertTrue($this->policy->update($owner, $stock));
        $this->assertFalse($this->policy->update($admin, $stock));
        $this->assertFalse($this->policy->update($stranger, $stock));

        // Delete
        $this->assertTrue($this->policy->delete($owner, $stock));
        $this->assertFalse($this->policy->delete($admin, $stock));
        $this->assertFalse($this->policy->delete($stranger, $stock));
    }
}
