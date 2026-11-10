<div>
    <!-- PAGE TITLE (H1 for WCAG Accessibility) -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-100">Daftar Stok &amp; Inventaris</h1>
        <p class="text-sm text-slate-300 mt-1">Kelola data inventaris produk, ketersediaan, dan pergerakan stok</p>
    </div>

    <!-- METRIC BAR (Dark Theme) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Metric 1: Total Macam Barang -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-300 uppercase tracking-wider">Total Macam Barang</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-sm font-bold">📦</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold font-mono tabular-nums text-slate-100">{{ number_format($metrics['totalItems'] ?? 0) }}</span>
                <span class="text-xs text-slate-300 font-mono font-medium">SKU</span>
            </div>
        </div>

        <!-- Metric 2: Total Fisik Unit -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-300 uppercase tracking-wider">Total Fisik Unit</span>
                <span class="w-8 h-8 rounded-lg bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center text-sm font-bold">📊</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold font-mono tabular-nums text-slate-100">{{ number_format($metrics['totalUnits'] ?? 0) }}</span>
                <span class="text-xs text-slate-300 font-mono font-medium">Unit</span>
            </div>
        </div>

        <!-- Metric 3: Estimasi Valuasi Aset -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-300 uppercase tracking-wider">Estimasi Valuasi Aset</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm font-bold">💰</span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-xs text-slate-300 font-mono font-medium">Rp</span>
                <span class="text-2xl font-bold font-mono tabular-nums text-emerald-400">{{ number_format($metrics['totalValuation'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Metric 4: Peringatan Stok Menipis -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-300 uppercase tracking-wider">Peringatan Stok Menipis</span>
                <span class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-sm font-bold">⚠️</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold font-mono tabular-nums text-amber-400">{{ number_format($metrics['lowStockCount'] ?? 0) }}</span>
                <span class="text-xs text-slate-300 font-mono font-medium">Perlu Restock</span>
            </div>
        </div>
    </div>

    <!-- ACTION HEADER & TOOLBAR -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 mb-6 shadow-sm">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <!-- Search & Filters -->
            <div class="flex flex-1 flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-sm">🔍</span>
                    <label for="stock-search" class="sr-only">Cari nama produk atau SKU</label>
                    <input id="stock-search"
                           type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Cari nama produk, SKU..."
                           aria-label="Cari nama produk atau SKU"
                           class="w-full bg-slate-950 border border-slate-700 rounded-lg pl-9 pr-3 py-2 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <label for="statusFilter" class="sr-only">Filter Status Stok</label>
                <select id="statusFilter"
                        wire:model.live="statusFilter"
                        aria-label="Filter status stok barang"
                        class="bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                    @endforeach
                </select>

                <label for="perPage" class="sr-only">Jumlah item per halaman</label>
                <select id="perPage"
                        wire:model.live="perPage"
                        aria-label="Jumlah barang per halaman"
                        class="bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <option value="10">10 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="openAiModal"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-purple-950/50 hover:bg-purple-900/60 text-purple-300 border border-purple-800/60 transition shadow-sm">
                    <span>✨</span>
                    <span>AI Generator</span>
                </button>

                <button wire:click="openImportModal"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-amber-950/40 hover:bg-amber-900/50 text-amber-300 border border-amber-800/60 transition shadow-sm">
                    <span>📂</span>
                    <span>Import Excel</span>
                </button>

                <button wire:click="exportExcel"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-emerald-950/40 hover:bg-emerald-900/50 text-emerald-300 border border-emerald-800/60 transition shadow-sm">
                    <span>📥</span>
                    <span>Export Excel</span>
                </button>

                <button wire:click="openCreateModal"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition">
                    <span>+</span>
                    <span>Tambah Stok</span>
                </button>
            </div>
        </div>
    </div>

    <!-- STOCK TABLE -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-950/80 border-b border-slate-800 text-[11px] font-mono uppercase tracking-wider text-slate-300">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4 cursor-pointer hover:text-white transition select-none" wire:click="sortBy('name')">
                            Barang &amp; Spesifikasi @if($sortBy==='name') <span class="text-indigo-400">{{ $sortDir==='asc'?'↑':'↓' }}</span> @endif
                        </th>
                        <th class="py-3.5 px-4 cursor-pointer hover:text-white transition select-none" wire:click="sortBy('sku')">
                            SKU @if($sortBy==='sku') <span class="text-indigo-400">{{ $sortDir==='asc'?'↑':'↓' }}</span> @endif
                        </th>
                        <th class="py-3.5 px-4 text-right cursor-pointer hover:text-white transition select-none" wire:click="sortBy('quantity')">
                            Kuantitas Unit @if($sortBy==='quantity') <span class="text-indigo-400">{{ $sortDir==='asc'?'↑':'↓' }}</span> @endif
                        </th>
                        <th class="py-3.5 px-4">Satuan</th>
                        <th class="py-3.5 px-4 text-right cursor-pointer hover:text-white transition select-none" wire:click="sortBy('price')">
                            Harga Satuan @if($sortBy==='price') <span class="text-indigo-400">{{ $sortDir==='asc'?'↑':'↓' }}</span> @endif
                        </th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-slate-800/30 transition group">
                            <td class="py-3.5 px-4 text-center font-mono text-xs text-slate-300 font-medium">
                                {{ $loop->iteration + ($stocks->currentPage() - 1) * $stocks->perPage() }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    @if($stock->cover_image_path)
                                        <img src="{{ $stock->coverImageUrl() ?? asset('storage/'.$stock->cover_image_path) }}" alt="{{ $stock->name }}"
                                             class="w-9 h-9 rounded-lg object-cover ring-1 ring-slate-800 flex-shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-lg bg-slate-800 border border-slate-700/60 flex items-center justify-center text-slate-300 text-xs font-mono flex-shrink-0">
                                            📦
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('stocks.detail', $stock->id) }}"
                                           class="font-medium text-slate-100 hover:text-indigo-400 transition leading-snug">
                                            {{ $stock->name }}
                                        </a>
                                        @if($stock->description)
                                            <p class="text-xs text-slate-300 truncate max-w-xs mt-0.5">{{ Str::limit(strip_tags($stock->description), 50) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-300">
                                {{ $stock->sku ?: '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5 font-mono tabular-nums">
                                    <button wire:click="decrement({{ $stock->id }})"
                                            title="Kurangi stok (-1)"
                                            aria-label="Kurangi stok {{ $stock->name }}"
                                            class="w-6 h-6 rounded-md bg-slate-800 hover:bg-rose-950/60 hover:text-rose-400 text-slate-200 border border-slate-700 flex items-center justify-center text-xs font-bold transition">
                                        −
                                    </button>
                                    <span class="min-w-10 text-center font-semibold text-slate-100">{{ $stock->quantity }}</span>
                                    <button wire:click="increment({{ $stock->id }})"
                                            title="Tambah stok (+1)"
                                            aria-label="Tambah stok {{ $stock->name }}"
                                            class="w-6 h-6 rounded-md bg-slate-800 hover:bg-emerald-950/60 hover:text-emerald-400 text-slate-200 border border-slate-700 flex items-center justify-center text-xs font-bold transition">
                                        +
                                    </button>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-300">
                                {{ $stock->unit }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono tabular-nums text-slate-100">
                                @if($stock->price !== null)
                                    <span class="text-xs text-slate-300 font-semibold mr-1">Rp</span>{{ number_format((float) $stock->price, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full {{ $stock->status->badgeClass() }}">
                                    {{ $stock->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEditModal({{ $stock->id }})"
                                            aria-label="Edit {{ $stock->name }}"
                                            class="px-2.5 py-1 rounded-md text-xs font-medium text-slate-200 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                                        Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $stock->id }})"
                                            aria-label="Hapus {{ $stock->name }}"
                                            class="px-2.5 py-1 rounded-md text-xs font-medium text-rose-300 hover:text-rose-200 bg-rose-950/40 hover:bg-rose-900/50 border border-rose-900/50 transition">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto text-center">
                                    <span class="text-4xl">📦</span>
                                    <h2 class="mt-3 text-base font-semibold text-slate-200">Belum ada barang di inventaris</h2>
                                    <p class="mt-1 text-xs text-slate-300">Mulai tambahkan produk baru, impor file spreadsheet, atau buat otomatis menggunakan AI Generator.</p>
                                    <button wire:click="openCreateModal" class="mt-4 px-4 py-2 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-sm transition">
                                        + Tambah Stok Sekarang
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="px-4 py-3 bg-slate-950/60 border-t border-slate-800/80">
            {{ $stocks->links() }}
        </div>
    </div>

    <!-- MODAL 1: FORM CREATE / EDIT (With Toast UI Editor) -->
    @if($showFormModal)
    <div class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl max-h-[92vh] flex flex-col shadow-2xl text-slate-100"
             x-data="{
                description: @entangle('description'),
                initEditor() {
                    const el = this.$refs.editorContainer;
                    if (!el) return;
                    el.innerHTML = '';
                    const editor = new toastui.Editor({
                        el: el,
                        height: '240px',
                        initialEditType: 'markdown',
                        previewStyle: 'vertical',
                        theme: 'dark',
                        initialValue: this.description || '',
                        events: {
                            change: () => {
                                this.description = editor.getMarkdown();
                            }
                        }
                    });
                }
             }"
             x-init="$nextTick(() => initEditor())">
            <div class="flex items-center justify-between p-5 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-sm font-bold">
                        {{ $editingStockId ? '✏️' : '➕' }}
                    </span>
                    <h2 class="text-base font-bold text-slate-100">
                        {{ $editingStockId ? 'Perbarui Informasi Stok' : 'Tambah Stok Barang Baru' }}
                    </h2>
                </div>
                <button wire:click="closeFormModal" aria-label="Tutup modal form" class="text-slate-300 hover:text-white text-xl font-mono leading-none">&times;</button>
            </div>

            <form wire:submit="saveStock" class="p-6 space-y-4 overflow-y-auto flex-1">
                <div>
                    <label for="stock-form-name" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Nama Produk *</label>
                    <input id="stock-form-name" type="text" wire:model="name" placeholder="Contoh: MacBook Pro M3"
                           class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('name') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="stock-form-sku" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">SKU (Stock Keeping Unit)</label>
                        <input id="stock-form-sku" type="text" wire:model="sku" placeholder="Contoh: MBP-M3-01"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label for="stock-form-unit" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Satuan Unit *</label>
                        <input id="stock-form-unit" type="text" wire:model="unit" placeholder="pcs, unit, kg, box"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('unit') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="stock-form-quantity" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Kuantitas Fisik *</label>
                        <input id="stock-form-quantity" type="number" wire:model="quantity" min="0"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('quantity') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="stock-form-min-stock" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Batas Min. Stok *</label>
                        <input id="stock-form-min-stock" type="number" wire:model="minStock" min="0"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('minStock') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="stock-form-price" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Harga Satuan (Rp)</label>
                        <input id="stock-form-price" type="number" wire:model="price" min="0" step="0.01" placeholder="0"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('price') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">
                        Deskripsi Spesifikasi (Toast UI Editor - Markdown)
                    </label>
                    <div wire:ignore class="rounded-lg overflow-hidden border border-slate-700">
                        <div x-ref="editorContainer"></div>
                    </div>
                    <!-- Hidden textarea fallback for Livewire binding -->
                    <textarea wire:model="description" class="hidden"></textarea>
                </div>

                <div>
                    <label for="cover" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Foto Cover Barang</label>
                    <input id="cover" type="file" wire:model="cover" accept="image/*" aria-label="Upload gambar cover produk"
                           class="block w-full text-xs text-slate-300 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                    @error('cover') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" wire:click="closeFormModal"
                            class="px-4 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Simpan Barang</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL 2: IMPORT EXCEL (.xlsx, .xls, .csv) -->
    @if($showImportModal)
    <div class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md shadow-2xl text-slate-100 p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-sm font-bold">📂</span>
                    <h2 class="text-base font-bold">Import Data Spreadsheet</h2>
                </div>
                <button wire:click="$set('showImportModal', false)" aria-label="Tutup modal import" class="text-slate-300 hover:text-white text-xl font-mono leading-none">&times;</button>
            </div>

            <form wire:submit="importExcel" class="mt-5 space-y-4">
                <div>
                    <label for="import-file-input" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-2">Pilih File (.xlsx, .xls, .csv)</label>
                    <div class="p-6 border-2 border-dashed border-slate-700 rounded-xl bg-slate-950/60 text-center hover:border-slate-600 transition">
                        <input id="import-file-input" type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" aria-label="Pilih File Spreadsheet" class="w-full text-xs text-slate-300 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                    </div>
                    @error('importFile') <p class="mt-2 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="p-3 rounded-lg bg-slate-950 border border-slate-800 text-[11px] font-mono text-slate-300 space-y-1">
                    <p class="text-slate-100 font-semibold uppercase">Format Kolom Terdukung:</p>
                    <p>No | Name | SKU | Description | Quantity | Unit | Price | Min Stock | Status</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" wire:click="$set('showImportModal', false)"
                            class="px-4 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg text-xs font-semibold bg-amber-600 hover:bg-amber-500 text-white shadow-md shadow-amber-600/20 transition"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Proses Import</span>
                        <span wire:loading>Mengunggah...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL 3: AI STOCK GENERATOR -->
    @if($showAiModal)
    <div class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl max-h-[92vh] flex flex-col shadow-2xl text-slate-100">
            <div class="flex items-center justify-between p-5 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center text-sm font-bold">✨</span>
                    <h2 class="text-base font-bold">AI Stock Generator (OpenAI)</h2>
                </div>
                <button wire:click="$set('showAiModal', false)" aria-label="Tutup modal AI" class="text-slate-300 hover:text-white text-xl font-mono leading-none">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                @if(!count($aiItems))
                    <div class="space-y-4">
                        <p class="text-xs text-slate-300">
                            Masukkan kategori inventaris atau kebutuhan pengadaan barang. Asisten AI akan merumuskan spesifikasi lengkap, estimasi kuantitas, harga, dan SKU secara otomatis.
                        </p>
                        <div>
                            <label for="ai-theme-input" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">
                                Kategori / Kebutuhan Pengadaan *
                            </label>
                            <input id="ai-theme-input"
                                   type="text"
                                   wire:model="aiTheme"
                                   placeholder="Contoh: Perangkat Jaringan Kantor, Suku Cadang Mesin Pabrik..."
                                   class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            @error('aiTheme') <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p> @enderror
                        </div>

                        @if($aiError)
                            <div class="p-3 rounded-lg bg-rose-950/60 border border-rose-800/60 text-rose-300 text-xs">
                                {{ $aiError }}
                            </div>
                        @endif

                        <button wire:click="generateAi"
                                class="w-full py-2.5 px-4 rounded-lg bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs shadow-md shadow-purple-600/20 transition flex items-center justify-center gap-2"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>Generate Item Stok</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                AI Sedang Menghasilkan...
                            </span>
                        </button>
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="flex items-center justify-between text-xs text-slate-300">
                            <span>Pratinjau Hasil AI: Beri centang pada item yang ingin ditambahkan ke stok.</span>
                            <span class="font-mono text-purple-300 font-semibold">{{ count($aiItems) }} item</span>
                        </div>

                        <div class="space-y-2.5 max-h-[50vh] overflow-y-auto pr-1">
                            @foreach($aiItems as $i => $item)
                                <label class="flex items-start gap-3 p-3.5 rounded-xl border {{ !empty($aiSelected[$i]) ? 'bg-purple-950/20 border-purple-800/60' : 'bg-slate-950/60 border-slate-800' }} cursor-pointer hover:border-slate-700 transition">
                                    <input type="checkbox" wire:model="aiSelected.{{ $i }}"
                                           aria-label="Pilih item {{ $item['name'] ?? '' }}"
                                           class="mt-1 h-4 w-4 rounded border-slate-700 bg-slate-900 text-purple-600 focus:ring-purple-500">
                                    <div class="flex-1 text-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-100 text-sm">{{ $item['name'] ?? '' }}</span>
                                            <span class="font-mono text-slate-300">{{ $item['sku'] ?? 'N/A' }}</span>
                                        </div>
                                        @if(!empty($item['description']))
                                            <p class="text-slate-300 mt-1 line-clamp-2">{{ $item['description'] }}</p>
                                        @endif
                                        <div class="mt-2 flex items-center gap-4 text-slate-200 font-mono text-[11px]">
                                            <span>Qty: <strong class="text-white">{{ $item['quantity'] ?? 0 }}</strong> {{ $item['unit'] ?? 'pcs' }}</span>
                                            <span>Min: {{ $item['min_stock'] ?? 5 }}</span>
                                            <span class="text-emerald-300 font-semibold">Rp {{ number_format((float)($item['price'] ?? 0), 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                            <button wire:click="$set('aiItems', [])"
                                    class="px-4 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                                &larr; Generate Ulang
                            </button>
                            <button wire:click="saveAiItems"
                                    class="px-5 py-2 rounded-lg text-xs font-semibold bg-purple-600 hover:bg-purple-500 text-white shadow-md shadow-purple-600/20 transition">
                                Simpan Item Terpilih
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL 4: KONFIRMASI HAPUS BARANG -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md shadow-2xl text-slate-100 p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-rose-950/60 border border-rose-800/60 text-rose-400 flex items-center justify-center text-lg flex-shrink-0">
                    ⚠️
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-100">Konfirmasi Hapus Barang</h2>
                    <p class="mt-2 text-xs text-slate-300 leading-relaxed">
                        Apakah Anda yakin ingin menghapus barang <strong class="text-white">"{{ $deletingStockName }}"</strong>? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-800">
                <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="px-4 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                    Batal
                </button>
                <button type="button" wire:click="executeDelete"
                        class="px-5 py-2 rounded-lg text-xs font-semibold bg-rose-600 hover:bg-rose-500 text-white shadow-md shadow-rose-600/20 transition">
                    Hapus Permanen
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
