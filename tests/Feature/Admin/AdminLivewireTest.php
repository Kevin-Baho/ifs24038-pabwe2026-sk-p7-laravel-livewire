<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

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
        $this->admin  = User::factory()->admin()->create();
        $this->member = User::factory()->member()->create(['name' => 'John Member', 'username' => 'johnm']);
    }

    public function test_admin_user_list_renders_for_admin(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSeeLivewire(UserList::class)
            ->assertSee('John Member');
    }

    public function test_member_cannot_access_admin_user_list(): void
    {
        $this->actingAs($this->member)
            ->get(route('admin.users'))
            ->assertForbidden();
    }

    public function test_user_list_search_filter(): void
    {
        Livewire::actingAs($this->admin)
            ->test(UserList::class)
            ->set('search', 'johnm')
            ->assertSee('John Member')
            ->set('search', 'nonexistent')
            ->assertDontSee('John Member');
    }

    public function test_admin_user_stock_list_renders_and_exports(): void
    {
        Stock::factory()->count(2)->create([
            'user_id' => $this->member->id,
            'name'    => 'Barang Member',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.users.stocks', $this->member->id))
            ->assertOk()
            ->assertSeeLivewire(AdminUserStockList::class)
            ->assertSee('Barang Member');

        $component = Livewire::actingAs($this->admin)
            ->test(AdminUserStockList::class, ['user' => $this->member])
            ->assertSee('Barang Member');

        $export = $component->call('exportExcel');
        $this->assertNotNull($export);
    }
}
