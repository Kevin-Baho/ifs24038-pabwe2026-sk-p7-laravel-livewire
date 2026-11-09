<?php

declare(strict_types=1);

namespace Tests\Feature\Stock;

use App\Features\Stock\Livewire\StockDetail;
use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class StockDetailLivewireTest extends TestCase
{
    use RefreshDatabase;

    private User $member;
    private Stock $stock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->member = User::factory()->member()->create();
        $this->stock  = Stock::factory()->create([
            'user_id'     => $this->member->id,
            'name'        => 'Mouse Gaming',
            'quantity'    => 10,
            'price'       => 450000,
            'min_stock'   => 3,
        ]);
    }

    public function test_detail_renders_for_owner(): void
    {
        $this->actingAs($this->member)
            ->get(route('stocks.detail', $this->stock->id))
            ->assertOk()
            ->assertSee('Mouse Gaming');
    }

    public function test_admin_sees_read_only_mode(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('stocks.detail', $this->stock->id))
            ->assertOk()
            ->assertSee('Mode Read-Only');

        Livewire::actingAs($admin)
            ->test(StockDetail::class, ['stock' => $this->stock])
            ->assertSet('isReadOnly', true);
    }

    public function test_increment_and_decrement(): void
    {
        Livewire::actingAs($this->member)
            ->test(StockDetail::class, ['stock' => $this->stock])
            ->call('increment')
            ->assertSet('quantity', 11);

        $this->assertSame(11, $this->stock->fresh()->quantity);

        Livewire::actingAs($this->member)
            ->test(StockDetail::class, ['stock' => $this->stock])
            ->call('decrement')
            ->assertSet('quantity', 10);

        $this->assertSame(10, $this->stock->fresh()->quantity);
    }

    public function test_save_and_cover_management(): void
    {
        Storage::fake('public');

        $component = Livewire::actingAs($this->member)
            ->test(StockDetail::class, ['stock' => $this->stock])
            ->call('openEditModal')
            ->assertSet('showEditModal', true)
            ->set('name', 'Mouse Gaming RGB Pro')
            ->set('price', 500000)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showEditModal', false);

        $this->assertSame('Mouse Gaming RGB Pro', $this->stock->fresh()->name);

        // Upload and update cover
        $cover = UploadedFile::fake()->image('cover.jpg');
        $component->set('cover', $cover)
            ->call('updateCover')
            ->assertHasNoErrors();

        $this->assertNotNull($this->stock->fresh()->cover_image_path);
        Storage::disk('public')->assertExists($this->stock->fresh()->cover_image_path);

        // Remove cover
        $component->call('removeCover');
        $this->assertNull($this->stock->fresh()->cover_image_path);
    }

    public function test_delete_stock(): void
    {
        Livewire::actingAs($this->member)
            ->test(StockDetail::class, ['stock' => $this->stock])
            ->call('delete')
            ->assertRedirect(route('stocks.index'));

        $this->assertDatabaseMissing('stocks', ['id' => $this->stock->id]);
    }

    public function test_read_only_blocks_mutations(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(StockDetail::class, ['stock' => $this->stock])->call('increment')->assertStatus(403);
        Livewire::actingAs($admin)->test(StockDetail::class, ['stock' => $this->stock])->call('decrement')->assertStatus(403);
        Livewire::actingAs($admin)->test(StockDetail::class, ['stock' => $this->stock])->call('save')->assertStatus(403);
        Livewire::actingAs($admin)->test(StockDetail::class, ['stock' => $this->stock])->call('updateCover')->assertStatus(403);
        Livewire::actingAs($admin)->test(StockDetail::class, ['stock' => $this->stock])->call('removeCover')->assertStatus(403);
        Livewire::actingAs($admin)->test(StockDetail::class, ['stock' => $this->stock])->call('delete')->assertStatus(403);
    }
}
