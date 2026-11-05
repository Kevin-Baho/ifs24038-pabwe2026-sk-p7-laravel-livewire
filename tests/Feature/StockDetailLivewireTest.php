<?php

declare(strict_types=1);

namespace Tests\Feature;

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

    private User  $member;
    private Stock $stock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->member = User::factory()->member()->create();
        $this->stock  = Stock::factory()->create([
            'user_id'     => $this->member->id,
            'name'        => 'Mouse Wireless',
            'sku'         => 'MS-001',
            'description' => '# Fitur Utama' . "\n" . '- 2.4 GHz' . "\n" . '- Silent click',
            'quantity'    => 10,
            'unit'        => 'pcs',
            'price'       => 150000,
            'min_stock'   => 5,
        ]);
    }

    public function test_member_can_view_and_update_own_stock(): void
    {
        Storage::fake('public');
        $this->actingAs($this->member);

        $newCover = UploadedFile::fake()->image('mouse.jpg');

        Livewire::test(StockDetail::class, ['stock' => $this->stock])
            ->assertSee('Mouse Wireless')
            ->assertSee('MS-001')
            ->assertSee('Silent click')
            ->assertSet('isReadOnly', false)
            ->set('name', 'Mouse Bluetooth')
            ->set('cover', $newCover)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Mouse Bluetooth');

        $this->assertSame('Mouse Bluetooth', $this->stock->fresh()->name);
        $this->assertNotNull($this->stock->fresh()->cover_image_path);
    }

    public function test_member_can_increment_and_decrement(): void
    {
        $this->actingAs($this->member);

        Livewire::test(StockDetail::class, ['stock' => $this->stock])
            ->call('increment');

        $this->assertSame(11, $this->stock->fresh()->quantity);

        Livewire::test(StockDetail::class, ['stock' => $this->stock])
            ->call('decrement');

        $this->assertSame(10, $this->stock->fresh()->quantity);
    }

    public function test_member_can_delete_stock(): void
    {
        $this->actingAs($this->member);

        Livewire::test(StockDetail::class, ['stock' => $this->stock])
            ->call('delete')
            ->assertRedirect(route('stocks.index'));

        $this->assertDatabaseMissing('stocks', ['id' => $this->stock->id]);
    }

    public function test_admin_view_is_read_only(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Livewire::test(StockDetail::class, ['stock' => $this->stock])
            ->assertSet('isReadOnly', true)
            ->assertSee('Mode baca saja (Admin)')
            ->assertSee('Mouse Wireless');
    }

    public function test_admin_cannot_mutate_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        Livewire::test(StockDetail::class, ['stock' => $this->stock])
            ->call('save')
            ->assertForbidden();

        Livewire::test(StockDetail::class, ['stock' => $this->stock])
            ->call('increment')
            ->assertForbidden();

        Livewire::test(StockDetail::class, ['stock' => $this->stock])
            ->call('decrement')
            ->assertForbidden();

        Livewire::test(StockDetail::class, ['stock' => $this->stock])
            ->call('delete')
            ->assertForbidden();
    }
}
