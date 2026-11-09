<?php

declare(strict_types=1);

namespace Tests\Feature\Stock;

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

    public function test_stock_list_renders_for_member(): void
    {
        Stock::factory()->count(3)->create(['user_id' => $this->member->id]);

        $this->actingAs($this->member)
            ->get(route('stocks.index'))
            ->assertOk()
            ->assertSeeLivewire(StockList::class);
    }

    public function test_metrics_bar_calculation(): void
    {
        Stock::factory()->create([
            'user_id'  => $this->member->id,
            'quantity' => 10,
            'price'    => 50000,
            'min_stock'=> 5,
        ]);
        Stock::factory()->create([
            'user_id'  => $this->member->id,
            'quantity' => 2,
            'price'    => 100000,
            'min_stock'=> 5,
        ]);

        Livewire::actingAs($this->member)
            ->test(StockList::class)
            ->assertSet('metrics.totalItems', 2)
            ->assertSet('metrics.totalUnits', 12)
            ->assertSet('metrics.totalValuation', 700000.0)
            ->assertSet('metrics.lowStockCount', 1);
    }

    public function test_search_and_filter_and_sorting(): void
    {
        Stock::factory()->create([
            'user_id' => $this->member->id,
            'name'    => 'Keyboard Mechanical',
            'sku'     => 'KB-01',
            'status'  => StockStatus::AVAILABLE->value,
        ]);
        Stock::factory()->create([
            'user_id' => $this->member->id,
            'name'    => 'Mouse Wireless',
            'sku'     => 'MS-02',
            'status'  => StockStatus::LOW_STOCK->value,
        ]);

        Livewire::actingAs($this->member)
            ->test(StockList::class)
            ->set('search', 'Keyboard')
            ->assertSee('Keyboard Mechanical')
            ->assertDontSee('Mouse Wireless')
            ->set('search', '')
            ->set('statusFilter', 'low_stock')
            ->assertSee('Mouse Wireless')
            ->assertDontSee('Keyboard Mechanical')
            ->call('sortBy', 'sku')
            ->assertSet('sortBy', 'sku')
            ->assertSet('sortDir', 'asc')
            ->call('sortBy', 'sku')
            ->assertSet('sortDir', 'desc');
    }

    public function test_create_and_edit_stock(): void
    {
        Storage::fake('public');

        $cover = UploadedFile::fake()->image('test.jpg');

        $component = Livewire::actingAs($this->member)
            ->test(StockList::class)
            ->call('openCreateModal')
            ->assertSet('showFormModal', true)
            ->set('name', 'Monitor Dell')
            ->set('sku', 'MON-01')
            ->set('quantity', 8)
            ->set('unit', 'unit')
            ->set('price', 3500000)
            ->set('minStock', 5)
            ->set('cover', $cover)
            ->call('saveStock')
            ->assertHasNoErrors()
            ->assertSet('showFormModal', false);

        $stock = Stock::where('sku', 'MON-01')->first();
        $this->assertNotNull($stock);
        $this->assertSame('Monitor Dell', $stock->name);

        // Edit
        $component->call('openEditModal', $stock->id)
            ->assertSet('showFormModal', true)
            ->assertSet('name', 'Monitor Dell')
            ->set('name', 'Monitor Dell UltraSharp')
            ->call('saveStock')
            ->assertHasNoErrors()
            ->assertSet('showFormModal', false);

        $this->assertSame('Monitor Dell UltraSharp', $stock->fresh()->name);
    }

    public function test_increment_decrement_and_delete(): void
    {
        $stock = Stock::factory()->create([
            'user_id'  => $this->member->id,
            'name'     => 'Webcam 4K',
            'quantity' => 10,
        ]);

        Livewire::actingAs($this->member)
            ->test(StockList::class)
            ->call('increment', $stock->id);

        $this->assertSame(11, $stock->fresh()->quantity);

        Livewire::actingAs($this->member)
            ->test(StockList::class)
            ->call('decrement', $stock->id);

        $this->assertSame(10, $stock->fresh()->quantity);

        // Delete confirmation modal and execution
        Livewire::actingAs($this->member)
            ->test(StockList::class)
            ->call('confirmDelete', $stock->id)
            ->assertSet('showDeleteModal', true)
            ->assertSet('deletingStockId', $stock->id)
            ->call('executeDelete')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseMissing('stocks', ['id' => $stock->id]);
    }

    public function test_export_and_import_excel(): void
    {
        Stock::factory()->count(2)->create(['user_id' => $this->member->id]);

        $component = Livewire::actingAs($this->member)->test(StockList::class);
        $response = $component->call('exportExcel');
        $this->assertNotNull($response);

        // Import
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['No', 'Name', 'SKU', 'Description', 'Quantity', 'Unit', 'Price', 'Min Stock', 'Status'],
            [1, 'Headset Sony', 'HS-01', 'Noise cancelling', 5, 'pcs', 1200000, 2, 'Tersedia'],
        ]);
        $uploadFile = UploadedFile::fake()->create('import_test.xlsx', 10, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $writer = new Xlsx($spreadsheet);
        $writer->save($uploadFile->getRealPath());

        $component->call('openImportModal')
            ->assertSet('showImportModal', true)
            ->set('importFile', $uploadFile)
            ->call('importExcel')
            ->assertHasNoErrors()
            ->assertSet('showImportModal', false);

        $this->assertDatabaseHas('stocks', ['name' => 'Headset Sony', 'user_id' => $this->member->id]);
    }

    public function test_ai_generator_modal_and_saving(): void
    {
        $stub = $this->createStub(AiClientInterface::class);
        $stub->method('complete')->willReturn(json_encode([
            [
                'name'        => 'Router WiFi 6',
                'sku'         => 'RT-001',
                'description' => 'Gigabit dual band',
                'quantity'    => 15,
                'unit'        => 'unit',
                'price'       => 850000,
                'min_stock'   => 3,
            ],
        ]));

        $this->app->instance(AiClientInterface::class, $stub);

        Livewire::actingAs($this->member)
            ->test(StockList::class)
            ->call('openAiModal')
            ->assertSet('showAiModal', true)
            ->set('aiTheme', 'Perangkat Jaringan')
            ->call('generateAi')
            ->assertCount('aiItems', 1)
            ->call('saveAiItems')
            ->assertSet('showAiModal', false);

        $this->assertDatabaseHas('stocks', ['name' => 'Router WiFi 6', 'user_id' => $this->member->id]);
    }

    public function test_ai_generator_handles_error_and_unselected_items(): void
    {
        $stub = $this->createStub(AiClientInterface::class);
        $stub->method('complete')->willThrowException(new \RuntimeException('API rate limit reached.'));
        $this->app->instance(AiClientInterface::class, $stub);

        Livewire::actingAs($this->member)
            ->test(StockList::class)
            ->set('aiTheme', 'Test Error')
            ->call('generateAi')
            ->assertSet('aiError', 'API rate limit reached.');

        Livewire::actingAs($this->member)
            ->test(StockList::class)
            ->set('aiItems', [
                ['name' => 'Selected Item', 'quantity' => 1, 'unit' => 'pcs', 'price' => 100, 'min_stock' => 1],
                ['name' => 'Unselected Item', 'quantity' => 1, 'unit' => 'pcs', 'price' => 100, 'min_stock' => 1],
            ])
            ->set('aiSelected', [0 => true, 1 => false])
            ->call('saveAiItems');

        $this->assertDatabaseHas('stocks', ['name' => 'Selected Item']);
        $this->assertDatabaseMissing('stocks', ['name' => 'Unselected Item']);
    }
}
