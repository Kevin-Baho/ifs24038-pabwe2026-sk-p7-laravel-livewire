<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_roles_and_stocks_relation(): void
    {
        $admin = User::factory()->admin()->create();
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isMember());

        $member = User::factory()->member()->create();
        $this->assertTrue($member->isMember());
        $this->assertFalse($member->isAdmin());

        Stock::factory()->count(3)->create(['user_id' => $member->id]);
        $this->assertCount(3, $member->stocks);
    }
}

