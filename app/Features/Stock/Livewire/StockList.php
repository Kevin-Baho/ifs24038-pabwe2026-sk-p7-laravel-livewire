<?php

declare(strict_types=1);

namespace App\Features\Stock\Livewire;

use App\Features\Ai\Contracts\AiClientInterface;
use App\Features\Ai\DTO\GeneratedStockItem;
use App\Features\Ai\Services\OpenAiStockGeneratorService;
use App\Features\Stock\Enums\StockStatus;
use App\Features\Stock\Models\Stock;
use App\Features\Stock\Services\StockExcelService;
use App\Features\Stock\Services\StockService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockList extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public string $search = '';
    public string $sortBy = 'name';
    public string $sortDir = 'asc';
    public string $statusFilter = '';
    public int $perPage = 10;

    // Modal - Add/Edit
    public bool $showFormModal = false;
    public ?int $editingStockId = null;
    public string $name = '';
    public string $sku = '';
    public string $description = '';
    public int $quantity = 0;
    public string $unit = 'pcs';
    public ?float $price = null;
    public int $minStock = 5;
    public $cover = null;

    // Modal - Delete Confirmation
    public bool $showDeleteModal = false;
    public ?int $deletingStockId = null;
    public string $deletingStockName = '';

    // Import
    public bool $showImportModal = false;
    public $importFile = null;

    // AI Generator
    public bool $showAiModal = false;
    public string $aiTheme = '';
    public bool $aiLoading = false;
    public string $aiError = '';
    /** @var array<int, array<string, mixed>> */
    public array $aiItems = [];
    /** @var array<int, bool> */
    public array $aiSelected = [];

    protected function queryString(): array
    {
        return [
            'search'       => ['except' => ''],
            'sortBy'       => ['except' => 'name'],
            'sortDir'      => ['except' => 'asc'],
            'statusFilter' => ['except' => ''],
        ];
    }

    protected function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'sku'         => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'unit'        => ['required', 'string', 'max:50'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'minStock'    => ['required', 'integer', 'min:0'],
            'cover'       => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $field;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal  = true;
        $this->editingStockId = null;
    }

    public function openEditModal(int $stockId, StockService $service): void
    {
        $stock = $service->findForUser((int) Auth::id(), $stockId);
        $this->editingStockId = $stockId;
        $this->name           = $stock->name;
        $this->sku            = $stock->sku ?? '';
        $this->description    = $stock->description ?? '';
        $this->quantity       = $stock->quantity;
        $this->unit           = $stock->unit;
        $this->price          = $stock->price !== null ? (float) $stock->price : null;
        $this->minStock       = $stock->min_stock;
        $this->cover          = null;
        $this->showFormModal  = true;
    }

    public function saveStock(StockService $service): void
    {
        $this->validate();

        $data = [
            'name'        => $this->name,
            'sku'         => $this->sku ?: null,
            'description' => $this->description ?: null,
            'quantity'    => $this->quantity,
            'unit'        => $this->unit,
            'price'       => $this->price,
            'min_stock'   => $this->minStock,
        ];

        if ($this->editingStockId !== null) {
            $stock = $service->findForUser((int) Auth::id(), $this->editingStockId);
            $this->authorize('update', $stock);
            $service->update($stock, $data, $this->cover);
            session()->flash('success', 'Stok berhasil diperbarui.');
        } else {
            $this->authorize('create', Stock::class);
            $service->create((int) Auth::id(), $data, $this->cover);
            session()->flash('success', 'Stok berhasil ditambahkan.');
        }

        $this->closeFormModal();
    }

    public function confirmDelete(int $stockId, StockService $service): void
    {
        $stock = $service->findForUser((int) Auth::id(), $stockId);
        $this->deletingStockId = $stockId;
        $this->deletingStockName = $stock->name;
        $this->showDeleteModal = true;
    }

    public function executeDelete(StockService $service): void
    {
        if ($this->deletingStockId !== null) {
            $this->deleteStock($this->deletingStockId, $service);
            $this->showDeleteModal = false;
            $this->deletingStockId = null;
            $this->deletingStockName = '';
        }
    }

    public function deleteStock(int $stockId, StockService $service): void
    {
        $stock = $service->findForUser((int) Auth::id(), $stockId);
        $this->authorize('delete', $stock);
        $service->delete($stock);
        session()->flash('success', 'Stok berhasil dihapus.');
    }

    public function increment(int $stockId, StockService $service): void
    {
        $stock = $service->findForUser((int) Auth::id(), $stockId);
        $this->authorize('update', $stock);
        $service->adjustQuantity($stock, 1);
    }

    public function decrement(int $stockId, StockService $service): void
    {
        $stock = $service->findForUser((int) Auth::id(), $stockId);
        $this->authorize('update', $stock);
        $service->adjustQuantity($stock, -1);
    }

    public function exportExcel(StockService $stockService, StockExcelService $excelService): StreamedResponse
    {
        $stocks = $stockService->allForUser((int) Auth::id());
        return $excelService->export($stocks);
    }

    public function openImportModal(): void
    {
        $this->importFile      = null;
        $this->showImportModal = true;
    }

    public function importExcel(StockService $stockService, StockExcelService $excelService): void
    {
        $this->validate(['importFile' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120']]);

        $path  = $this->importFile->getRealPath();
        $items = $excelService->import($path);

        foreach ($items as $item) {
            $item['user_id'] = Auth::id();
            $stockService->create((int) Auth::id(), $item);
        }

        $this->showImportModal = false;
        $this->importFile      = null;
        session()->flash('success', count($items) . ' stok berhasil diimpor.');
    }

    public function openAiModal(): void
    {
        $this->aiTheme    = '';
        $this->aiItems    = [];
        $this->aiSelected = [];
        $this->aiError    = '';
        $this->showAiModal = true;
    }

    public function generateAi(OpenAiStockGeneratorService $generator): void
    {
        $this->validate(['aiTheme' => ['required', 'string', 'min:3']]);
        $this->aiLoading = true;
        $this->aiError   = '';

        try {
            $items = $generator->generate($this->aiTheme, 5);
            $this->aiItems    = array_map(fn (GeneratedStockItem $i): array => $i->toArray(), $items);
            $this->aiSelected = array_fill(0, count($this->aiItems), true);
        } catch (\Throwable $e) {
            $this->aiError = $e->getMessage();
        } finally {
            $this->aiLoading = false;
        }
    }

    public function saveAiItems(StockService $service): void
    {
        foreach ($this->aiItems as $i => $item) {
            if (empty($this->aiSelected[$i])) {
                continue;
            }
            $service->create((int) Auth::id(), $item);
        }

        $this->showAiModal = false;
        session()->flash('success', 'Item AI berhasil disimpan.');
    }

    private function resetForm(): void
    {
        $this->name        = '';
        $this->sku         = '';
        $this->description = '';
        $this->quantity    = 0;
        $this->unit        = 'pcs';
        $this->price       = null;
        $this->minStock    = 5;
        $this->cover       = null;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal  = false;
        $this->editingStockId = null;
        $this->resetForm();
    }

    public function getMetricsProperty(): array
    {
        $stocks = Stock::where('user_id', Auth::id())->get();

        $totalItems = $stocks->count();
        $totalUnits = (int) $stocks->sum('quantity');
        $totalValuation = (float) $stocks->sum(fn ($s) => ($s->quantity ?? 0) * ((float) ($s->price ?? 0)));
        $lowStockCount = $stocks->filter(fn ($s) => $s->quantity <= $s->min_stock)->count();

        return [
            'totalItems'     => $totalItems,
            'totalUnits'     => $totalUnits,
            'totalValuation' => $totalValuation,
            'lowStockCount'  => $lowStockCount,
        ];
    }

    public function getStocks(StockService $service): LengthAwarePaginator
    {
        $status = $this->statusFilter !== '' ? StockStatus::from($this->statusFilter) : null;

        return $service->listForUser(
            (int) Auth::id(),
            $this->search,
            $status,
            $this->sortBy,
            $this->sortDir,
            $this->perPage,
        );
    }

    public function render(StockService $service): \Illuminate\View\View
    {
        return view('livewire.stock.stock-list', [
            'stocks'        => $this->getStocks($service),
            'statusOptions' => StockStatus::cases(),
            'metrics'       => $this->metrics,
        ]);
    }
}
