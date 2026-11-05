<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Livewire\StockList;
use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class StockListLivewireTest extends TestCase
{
    use RefreshDatabase;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->member = User::factory()->member()->create();
    }

    public function test_renders_stock_list_and_pagination(): void
    {
        Stock::factory()->count(15)->create(['user_id' => $this->member->id]);

        $this->actingAs($this->member);

        Livewire::test(StockList::class)
            ->assertStatus(200)
            ->assertViewHas('stocks', fn ($stocks) => $stocks->total() === 15);
    }

    public function test_search_and_filter_and_sort(): void
    {
        Stock::factory()->create([
            'user_id' => $this->member->id,
            'name'    => 'Kertas HVS',
            'status'  => StockStatus::Available,
        ]);

        Stock::factory()->create([
            'user_id'  => $this->member->id,
            'name'     => 'Spidol Whiteboard',
            'status'   => StockStatus::LowStock,
            'quantity' => 2,
        ]);

        $this->actingAs($this->member);

        // Search
        Livewire::test(StockList::class)
            ->set('search', 'Kertas')
            ->assertSee('Kertas HVS')
            ->assertDontSee('Spidol Whiteboard');

        // Status Filter
        Livewire::test(StockList::class)
            ->set('statusFilter', 'low_stock')
            ->assertSee('Spidol Whiteboard')
            ->assertDontSee('Kertas HVS');

        // Sort by quantity desc
        Livewire::test(StockList::class)
            ->call('sortBy', 'quantity')
            ->assertSet('sortBy', 'quantity')
            ->assertSet('sortDir', 'asc')
            ->call('sortBy', 'quantity')
            ->assertSet('sortDir', 'desc');
    }

    public function test_create_stock_validation_and_creation(): void
    {
        Storage::fake('public');
        $this->actingAs($this->member);

        $cover = UploadedFile::fake()->image('cover.jpg');

        Livewire::test(StockList::class)
            ->call('openCreateModal')
            ->assertSet('showFormModal', true)
            ->set('name', '')
            ->call('saveStock')
            ->assertHasErrors(['name' => 'required'])
            ->set('name', 'Buku Catatan')
            ->set('sku', 'BK-CAT')
            ->set('quantity', 25)
            ->set('unit', 'pcs')
            ->set('price', 15000)
            ->set('minStock', 5)
            ->set('cover', $cover)
            ->call('saveStock')
            ->assertHasNoErrors()
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('stocks', [
            'name'     => 'Buku Catatan',
            'user_id'  => $this->member->id,
            'quantity' => 25,
        ]);
    }

    public function test_edit_stock(): void
    {
        $stock = Stock::factory()->create([
            'user_id' => $this->member->id,
            'name'    => 'Stok Lama',
        ]);

        $this->actingAs($this->member);

        Livewire::test(StockList::class)
            ->call('openEditModal', $stock->id)
            ->assertSet('name', 'Stok Lama')
            ->assertSet('editingStockId', $stock->id)
            ->set('name', 'Stok Baru')
            ->call('saveStock')
            ->assertSet('showFormModal', false);

        $this->assertDatabaseHas('stocks', [
            'id'   => $stock->id,
            'name' => 'Stok Baru',
        ]);
    }

    public function test_delete_stock(): void
    {
        $stock = Stock::factory()->create(['user_id' => $this->member->id]);
        $this->actingAs($this->member);

        Livewire::test(StockList::class)
            ->call('deleteStock', $stock->id);

        $this->assertDatabaseMissing('stocks', ['id' => $stock->id]);
    }

    public function test_quick_adjust_quantity(): void
    {
        $stock = Stock::factory()->create([
            'user_id'  => $this->member->id,
            'quantity' => 5,
        ]);

        $this->actingAs($this->member);

        Livewire::test(StockList::class)
            ->call('increment', $stock->id);

        $this->assertSame(6, $stock->fresh()->quantity);

        Livewire::test(StockList::class)
            ->call('decrement', $stock->id);

        $this->assertSame(5, $stock->fresh()->quantity);
    }

    public function test_export_excel(): void
    {
        Stock::factory()->create(['user_id' => $this->member->id]);
        $this->actingAs($this->member);

        $response = Livewire::test(StockList::class)
            ->call('exportExcel');

        $this->assertNotNull($response);
    }

    public function test_import_excel(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['No', 'Name', 'SKU', 'Quantity', 'Unit', 'Price', 'Min Stock', 'Status'],
            [1, 'Barang Import', 'IMP-01', 30, 'box', 12000, 5, 'available'],
        ]);

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test_import_' . uniqid() . '.xlsx';
        (new Xlsx($spreadsheet))->save($tempPath);

        $uploaded = UploadedFile::fake()->createWithContent('import.xlsx', (string) file_get_contents($tempPath));

        $this->actingAs($this->member);

        Livewire::test(StockList::class)
            ->call('openImportModal')
            ->set('importFile', $uploaded)
            ->call('importExcel')
            ->assertHasNoErrors()
            ->assertSet('showImportModal', false);

        $this->assertDatabaseHas('stocks', [
            'name'    => 'Barang Import',
            'user_id' => $this->member->id,
        ]);

        @unlink($tempPath);
    }

    public function test_ai_stock_generator_modal_and_saving(): void
    {
        $aiStub = $this->createStub(AiClientInterface::class);
        $aiStub->method('complete')->willReturn(json_encode([
            [
                'name'        => 'AI Item 1',
                'sku'         => 'AI-01',
                'description' => 'Desc 1',
                'quantity'    => 10,
                'unit'        => 'pcs',
                'price'       => 5000,
                'min_stock'   => 5,
            ],
            [
                'name'        => 'AI Item 2',
                'sku'         => 'AI-02',
                'description' => 'Desc 2',
                'quantity'    => 20,
                'unit'        => 'pcs',
                'price'       => 10000,
                'min_stock'   => 5,
            ],
        ]));

        $this->app->instance(AiClientInterface::class, $aiStub);

        $this->actingAs($this->member);

        Livewire::test(StockList::class)
            ->call('openAiModal')
            ->assertSet('showAiModal', true)
            ->set('aiTheme', 'Elektronik')
            ->call('generateAi')
            ->assertCount('aiItems', 2)
            ->set('aiSelected.1', false) // Unselect second item, keep first item selected
            ->call('saveAiItems')
            ->assertSet('showAiModal', false);

        $this->assertDatabaseHas('stocks', [
            'name'    => 'AI Item 1',
            'user_id' => $this->member->id,
        ]);
        $this->assertDatabaseMissing('stocks', [
            'name'    => 'AI Item 2',
            'user_id' => $this->member->id,
        ]);

        // Test AI exception handling
        $failingStub = $this->createStub(AiClientInterface::class);
        $failingStub->method('complete')->willThrowException(new \RuntimeException('Service unavailable'));
        $this->app->instance(AiClientInterface::class, $failingStub);

        Livewire::test(StockList::class)
            ->call('openAiModal')
            ->set('aiTheme', 'Gagal')
            ->call('generateAi')
            ->assertSet('aiError', 'Service unavailable');
    }
}
