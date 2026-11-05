<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Features\Admin\Livewire\AdminUserStockList;
use App\Features\Admin\Livewire\UserList;
use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminLivewireTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin  = User::factory()->admin()->create(['name' => 'Admin Boss']);
        $this->member = User::factory()->member()->create(['name' => 'Member User']);
    }

    public function test_user_list_component(): void
    {
        $userA = User::factory()->member()->create(['name' => 'Charlie User']);
        $userB = User::factory()->member()->create(['name' => 'Delta Member']);
        Stock::factory()->count(2)->create(['user_id' => $userA->id, 'price' => 5000]);

        $this->actingAs($this->admin);

        Livewire::test(UserList::class)
            ->assertSee('Charlie User')
            ->assertSee('Delta Member')
            ->set('search', 'Charlie')
            ->assertSee('Charlie User')
            ->assertDontSee('Delta Member');
    }

    public function test_admin_user_stock_list_component(): void
    {
        Stock::factory()->create([
            'user_id' => $this->member->id,
            'name'    => 'Barang Target A',
        ]);
        Stock::factory()->create([
            'user_id' => $this->member->id,
            'name'    => 'Barang Target B',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(AdminUserStockList::class, ['user' => $this->member])
            ->assertSee('Barang Target A')
            ->assertSee('Barang Target B')
            ->set('search', 'Target A')
            ->assertSee('Barang Target A')
            ->assertDontSee('Barang Target B');

        // Test mount with integer ID
        Livewire::test(AdminUserStockList::class, ['user' => $this->member->id])
            ->assertSet('targetUser.id', $this->member->id);
    }

    public function test_member_cannot_access_admin_routes(): void
    {
        $this->actingAs($this->member)
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs($this->member)
            ->get('/admin/users/' . $this->member->id . '/stocks')
            ->assertForbidden();
    }
}
