<x-layouts.app>
    <div>
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Stok</h1>
            <div class="flex gap-2">
                <button wire:click="openAiModal"
                        class="px-3 py-2 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700 font-medium">
                    🤖 AI Generator
                </button>
                <button wire:click="openImportModal"
                        class="px-3 py-2 text-sm bg-yellow-500 text-white rounded-md hover:bg-yellow-600 font-medium">
                    📂 Import
                </button>
                <button wire:click="exportExcel"
                        class="px-3 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 font-medium">
                    📥 Export
                </button>
                <button wire:click="openCreateModal"
                        class="px-3 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                    + Tambah Stok
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-3 mb-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / SKU..."
                   class="border border-gray-300 rounded-md px-3 py-2 text-sm flex-1 min-w-48 focus:border-blue-500 focus:ring-blue-500">
            <select wire:model.live="statusFilter"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $s)
                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                @endforeach
            </select>
            <select wire:model.live="perPage"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="10">10 / hal</option>
                <option value="25">25 / hal</option>
                <option value="50">50 / hal</option>
            </select>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        <!-- Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase text-xs">No</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase text-xs cursor-pointer hover:text-gray-700" wire:click="sortBy('name')">
                            Nama @if($sortBy==='name') {{ $sortDir==='asc'?'↑':'↓' }} @endif
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase text-xs">SKU</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase text-xs cursor-pointer hover:text-gray-700" wire:click="sortBy('quantity')">
                            Qty @if($sortBy==='quantity') {{ $sortDir==='asc'?'↑':'↓' }} @endif
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase text-xs">Unit</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase text-xs cursor-pointer hover:text-gray-700" wire:click="sortBy('price')">
                            Harga @if($sortBy==='price') {{ $sortDir==='asc'?'↑':'↓' }} @endif
                        </th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase text-xs">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase text-xs">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-500">{{ $loop->iteration + ($stocks->currentPage() - 1) * $stocks->perPage() }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                <a href="{{ route('stocks.detail', $stock->id) }}" class="hover:text-blue-600">{{ $stock->name }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $stock->sku }}</td>
                            <td class="px-4 py-3 text-right text-gray-800 font-semibold">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="decrement({{ $stock->id }})"
                                            class="w-6 h-6 rounded bg-red-100 hover:bg-red-200 text-red-600 font-bold text-xs flex items-center justify-center">−</button>
                                    <span class="min-w-8 text-center">{{ $stock->quantity }}</span>
                                    <button wire:click="increment({{ $stock->id }})"
                                            class="w-6 h-6 rounded bg-green-100 hover:bg-green-200 text-green-600 font-bold text-xs flex items-center justify-center">+</button>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $stock->unit }}</td>
                            <td class="px-4 py-3 text-right text-gray-600">
                                @if($stock->price !== null)
                                    Rp {{ number_format((float)$stock->price, 0, ',', '.') }}
                                @else —
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $stock->status->badgeClass() }}">
                                    {{ $stock->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button wire:click="openEditModal({{ $stock->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</button>
                                    <button wire:click="deleteStock({{ $stock->id }})"
                                            wire:confirm="Yakin hapus stok '{{ addslashes($stock->name) }}'?"
                                            class="text-red-600 hover:text-red-800 text-xs font-medium">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-6 text-center text-gray-500">Belum ada stok. Tambahkan sekarang!</td></tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $stocks->links() }}
            </div>
        </div>

        <!-- FORM MODAL -->
        @if($showFormModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $editingStockId ? 'Edit Stok' : 'Tambah Stok Baru' }}
                    </h3>
                    <button wire:click="closeFormModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                </div>
                <form wire:submit="saveStock" class="p-4 space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Produk *</label>
                        <input type="text" wire:model="name" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SKU</label>
                            <input type="text" wire:model="sku" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit *</label>
                            <input type="text" wire:model="unit" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah *</label>
                            <input type="number" wire:model="quantity" min="0" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Min Stok *</label>
                            <input type="number" wire:model="minStock" min="0" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Harga</label>
                        <input type="number" wire:model="price" min="0" step="0.01" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi (Markdown)</label>
                        <textarea wire:model="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cover Image</label>
                        <input type="file" wire:model="cover" accept="image/*" class="mt-1 text-sm text-gray-600">
                        @error('cover') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeFormModal"
                                class="px-4 py-2 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                wire:loading.attr="disabled">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- IMPORT MODAL -->
        @if($showImportModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold">Import Excel</h3>
                    <button wire:click="$set('showImportModal', false)" class="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                </div>
                <form wire:submit="importExcel" class="p-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">File Excel (.xlsx / .xls)</label>
                        <input type="file" wire:model="importFile" accept=".xlsx,.xls"
                               class="mt-1 block w-full text-sm text-gray-600">
                        @error('importFile') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <p class="text-xs text-gray-500">Format kolom: No | Name | SKU | Quantity | Unit | Price | Min Stock | Status</p>
                    <div class="flex justify-end gap-2">
                        <button type="button" wire:click="$set('showImportModal', false)"
                                class="px-4 py-2 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 text-sm bg-yellow-500 text-white rounded-md hover:bg-yellow-600"
                                wire:loading.attr="disabled">Import</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- AI MODAL -->
        @if($showAiModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-semibold">🤖 AI Stock Generator</h3>
                    <button wire:click="$set('showAiModal', false)" class="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                </div>
                <div class="p-4">
                    @if(!count($aiItems))
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tema / Kategori Inventaris</label>
                                <input type="text" wire:model="aiTheme" placeholder="Contoh: Alat Tulis Kantor, Sparepart Motor"
                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('aiTheme') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            @if($aiError)
                                <div class="p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">{{ $aiError }}</div>
                            @endif
                            <button wire:click="generateAi"
                                    class="w-full py-2 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>✨ Generate</span>
                                <span wire:loading>Generating...</span>
                            </button>
                        </div>
                    @else
                        <div class="space-y-2 mb-4">
                            @foreach($aiItems as $i => $item)
                                <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox" wire:model="aiSelected.{{ $i }}"
                                           class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    <div class="flex-1 text-sm">
                                        <div class="font-medium text-gray-900">{{ $item['name'] ?? '' }} <span class="text-gray-500 text-xs">({{ $item['sku'] ?? '' }})</span></div>
                                        <div class="text-gray-600 text-xs mt-0.5">Qty: {{ $item['quantity'] ?? 0 }} {{ $item['unit'] ?? 'pcs' }} | Harga: Rp {{ number_format((float)($item['price'] ?? 0), 0, ',', '.') }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="$set('aiItems', [])"
                                    class="flex-1 py-2 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                ← Kembali
                            </button>
                            <button wire:click="saveAiItems"
                                    class="flex-1 py-2 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700">
                                💾 Simpan Item Terpilih
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</x-layouts.app>

