<?php

declare(strict_types=1);

namespace App\Features\Stock\Livewire;

use App\Features\Stock\Models\Stock;
use App\Features\Stock\Services\StockService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class StockDetail extends Component
{
    use WithFileUploads;

    public Stock  $stock;
    public string $name        = '';
    public string $sku         = '';
    public string $description = '';
    public int    $quantity    = 0;
    public string $unit        = 'pcs';
    public ?float $price       = null;
    public int    $minStock    = 5;
    public        $cover       = null;
    public bool   $isReadOnly  = false;
    public bool   $showEditModal = false;

    public function mount(Stock|int|string $stock, StockService $service): void
    {
        $stockModel = $stock instanceof Stock ? $stock : Stock::findOrFail((int) $stock);
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            $this->stock      = $stockModel;
            $this->isReadOnly = true;
        } else {
            $this->stock      = $service->findForUser((int) Auth::id(), $stockModel->id);
            $this->isReadOnly = false;
        }

        $this->fillFromStock();
    }

    private function fillFromStock(): void
    {
        $this->name        = $this->stock->name;
        $this->sku         = $this->stock->sku ?? '';
        $this->description = $this->stock->description ?? '';
        $this->quantity    = $this->stock->quantity;
        $this->unit        = $this->stock->unit;
        $this->price       = $this->stock->price !== null ? (float) $this->stock->price : null;
        $this->minStock    = $this->stock->min_stock;
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

    public function openEditModal(): void
    {
        $this->fillFromStock();
        $this->cover = null;
        $this->showEditModal = true;
    }

    public function save(StockService $service): void
    {
        if ($this->isReadOnly) {
            abort(403);
        }

        $this->authorize('update', $this->stock);
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

        $this->stock = $service->update($this->stock, $data, $this->cover);
        $this->fillFromStock();
        $this->cover = null;
        $this->showEditModal = false;

        session()->flash('success', 'Stok berhasil diperbarui.');
    }

    public function updateCover(StockService $service): void
    {
        if ($this->isReadOnly) {
            abort(403);
        }
        $this->authorize('update', $this->stock);
        $this->validate(['cover' => ['required', 'image', 'max:2048']]);

        $this->stock = $service->update($this->stock, [], $this->cover);
        $this->cover = null;
        session()->flash('success', 'Cover berhasil diperbarui.');
    }

    public function removeCover(): void
    {
        if ($this->isReadOnly) {
            abort(403);
        }
        $this->authorize('update', $this->stock);
        if ($this->stock->cover_image_path) {
            Storage::disk('public')->delete($this->stock->cover_image_path);
            $this->stock->cover_image_path = null;
            $this->stock->save();
        }
        $this->cover = null;
        session()->flash('success', 'Cover berhasil dihapus.');
    }

    public function increment(StockService $service): void
    {
        if ($this->isReadOnly) {
            abort(403);
        }
        $this->authorize('update', $this->stock);
        $this->stock = $service->increment($this->stock);
        $this->fillFromStock();
    }

    public function decrement(StockService $service): void
    {
        if ($this->isReadOnly) {
            abort(403);
        }
        $this->authorize('update', $this->stock);
        $this->stock = $service->decrement($this->stock);
        $this->fillFromStock();
    }

    public function delete(StockService $service): void
    {
        if ($this->isReadOnly) {
            abort(403);
        }
        $this->authorize('delete', $this->stock);
        $service->delete($this->stock);
        session()->flash('success', 'Stok berhasil dihapus.');
        $this->redirectRoute('stocks.index', navigate: true);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.stock.stock-detail');
    }
}
