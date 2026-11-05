<?php

declare(strict_types=1);

namespace Tests\Unit;

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

    public function test_view_authorization(): void
    {
        $admin  = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $other  = User::factory()->member()->create();

        $stock = Stock::factory()->create(['user_id' => $member->id]);

        $this->assertTrue($this->policy->view($admin, $stock));
        $this->assertTrue($this->policy->view($member, $stock));
        $this->assertFalse($this->policy->view($other, $stock));
    }

    public function test_create_authorization(): void
    {
        $admin  = User::factory()->admin()->make();
        $member = User::factory()->member()->make();

        $this->assertTrue($this->policy->create($member));
        $this->assertFalse($this->policy->create($admin));
    }

    public function test_update_authorization(): void
    {
        $admin  = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $other  = User::factory()->member()->create();

        $stock = Stock::factory()->create(['user_id' => $member->id]);

        $this->assertTrue($this->policy->update($member, $stock));
        $this->assertFalse($this->policy->update($admin, $stock));
        $this->assertFalse($this->policy->update($other, $stock));
    }

    public function test_delete_authorization(): void
    {
        $admin  = User::factory()->admin()->create();
        $member = User::factory()->member()->create();
        $other  = User::factory()->member()->create();

        $stock = Stock::factory()->create(['user_id' => $member->id]);

        $this->assertTrue($this->policy->delete($member, $stock));
        $this->assertFalse($this->policy->delete($admin, $stock));
        $this->assertFalse($this->policy->delete($other, $stock));
    }
}

