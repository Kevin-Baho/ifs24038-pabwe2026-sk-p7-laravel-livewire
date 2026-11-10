<div>
    <!-- PAGE TITLE (Heading Level 1) -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-slate-100">Daftar Stok &amp; Inventaris</h1>
        <p class="text-sm text-slate-200 mt-1">Kelola data inventaris produk, ketersediaan, dan pergerakan stok</p>
    </div>

    <!-- METRIC BAR (Dark Theme Kontras Tinggi) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Metric 1 -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-200 uppercase tracking-wider">Total Macam Barang</span>
                <span class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 flex items-center justify-center text-sm font-bold">📦</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold font-mono tabular-nums text-slate-100">{{ number_format($metrics['totalItems'] ?? 0) }}</span>
                <span class="text-xs text-slate-200 font-mono font-bold">SKU</span>
            </div>
        </div>

        <!-- Metric 2 -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-200 uppercase tracking-wider">Total Fisik Unit</span>
                <span class="w-8 h-8 rounded-lg bg-sky-500/10 border border-sky-500/20 text-sky-300 flex items-center justify-center text-sm font-bold">📊</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold font-mono tabular-nums text-slate-100">{{ number_format($metrics['totalUnits'] ?? 0) }}</span>
                <span class="text-xs text-slate-200 font-mono font-bold">Unit</span>
            </div>
        </div>

        <!-- Metric 3 -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-200 uppercase tracking-wider">Estimasi Valuasi Aset</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 flex items-center justify-center text-sm font-bold">💰</span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-xs text-slate-200 font-mono font-bold mr-1">Rp</span>
                <span class="text-2xl font-bold font-mono tabular-nums text-emerald-300">{{ number_format($metrics['totalValuation'] ?? 0, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Metric 4 -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-200 uppercase tracking-wider">Peringatan Stok Menipis</span>
                <span class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 flex items-center justify-center text-sm font-bold">⚠️</span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-2xl font-bold font-mono tabular-nums text-amber-300">{{ number_format($metrics['lowStockCount'] ?? 0) }}</span>
                <span class="text-xs text-slate-200 font-mono font-bold">Perlu Restock</span>
            </div>
        </div>
    </div>

    <!-- ACTION HEADER & TOOLBAR -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 mb-6 shadow-sm">
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <!-- Search & Filters -->
            <div class="flex flex-1 flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-300 text-sm">🔍</span>
                    <label for="stock-search" class="sr-only">Cari nama produk atau SKU</label>
                    <input id="stock-search"
                           type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Cari nama produk, SKU..."
                           class="w-full bg-slate-950 border border-slate-700 rounded-lg pl-9 pr-3 py-2 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>

                <label for="statusFilter" class="sr-only">Filter Status Stok</label>
                <select id="statusFilter"
                        wire:model.live="statusFilter"
                        class="bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $s)
                        <option value="{{ $s->value }}">{{ $s->label() }}</option>
                    @endforeach
                </select>

                <label for="perPage" class="sr-only">Jumlah item per halaman</label>
                <select id="perPage"
                        wire:model.live="perPage"
                        class="bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <option value="10">10 / halaman</option>
                    <option value="25">25 / halaman</option>
                    <option value="50">50 / halaman</option>
                </select>
            </div>

            <!-- Action Buttons (Tanpa aria-label yang konflik dengan teks di dalam tombol) -->
            <div class="flex flex-wrap items-center gap-2">
                <button wire:click="openAiModal"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-purple-950/70 hover:bg-purple-900/80 text-purple-200 border border-purple-700 transition shadow-sm">
                    <span>✨</span>
                    <span>AI Generator</span>
                </button>

                <button wire:click="openImportModal"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-amber-950/70 hover:bg-amber-900/80 text-amber-200 border border-amber-700 transition shadow-sm">
                    <span>📂</span>
                    <span>Import Excel</span>
                </button>

                <button wire:click="exportExcel"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-emerald-950/70 hover:bg-emerald-900/80 text-emerald-200 border border-emerald-700 transition shadow-sm">
                    <span>📥</span>
                    <span>Export Excel</span>
                </button>

                <button wire:click="openCreateModal"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-600/20 transition">
                    <span>+</span>
                    <span>Tambah Stok</span>
                </button>
            </div>
        </div>
    </div>

    <!-- STOCK TABLE -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-400" tabindex="0" role="region" aria-label="Tabel daftar stok (dapat digulir)">
            <table class="w-full text-left text-sm">
                <caption class="sr-only">Daftar stok dan inventaris barang</caption>
                <thead class="bg-slate-950 border-b border-slate-800 text-[11px] font-mono uppercase tracking-wider text-slate-200">
                    <tr>
                        <th scope="col" class="py-3.5 px-4 text-center w-12">No</th>
                        <th scope="col" class="py-3.5 px-4" aria-sort="{{ $sortBy==='name' ? ($sortDir==='asc' ? 'ascending' : 'descending') : 'none' }}">
                            <button type="button" wire:click="sortBy('name')" class="font-mono uppercase tracking-wider hover:text-white transition rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                                Barang &amp; Spesifikasi @if($sortBy==='name') <span aria-hidden="true" class="text-indigo-400">{{ $sortDir==='asc'?'↑':'↓' }}</span> @endif
                            </button>
                        </th>
                        <th scope="col" class="py-3.5 px-4" aria-sort="{{ $sortBy==='sku' ? ($sortDir==='asc' ? 'ascending' : 'descending') : 'none' }}">
                            <button type="button" wire:click="sortBy('sku')" class="font-mono uppercase tracking-wider hover:text-white transition rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                                SKU @if($sortBy==='sku') <span aria-hidden="true" class="text-indigo-400">{{ $sortDir==='asc'?'↑':'↓' }}</span> @endif
                            </button>
                        </th>
                        <th scope="col" class="py-3.5 px-4 text-right" aria-sort="{{ $sortBy==='quantity' ? ($sortDir==='asc' ? 'ascending' : 'descending') : 'none' }}">
                            <button type="button" wire:click="sortBy('quantity')" class="font-mono uppercase tracking-wider hover:text-white transition rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                                Kuantitas Unit @if($sortBy==='quantity') <span aria-hidden="true" class="text-indigo-400">{{ $sortDir==='asc'?'↑':'↓' }}</span> @endif
                            </button>
                        </th>
                        <th scope="col" class="py-3.5 px-4">Satuan</th>
                        <th scope="col" class="py-3.5 px-4 text-right" aria-sort="{{ $sortBy==='price' ? ($sortDir==='asc' ? 'ascending' : 'descending') : 'none' }}">
                            <button type="button" wire:click="sortBy('price')" class="font-mono uppercase tracking-wider hover:text-white transition rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                                Harga Satuan @if($sortBy==='price') <span aria-hidden="true" class="text-indigo-400">{{ $sortDir==='asc'?'↑':'↓' }}</span> @endif
                            </button>
                        </th>
                        <th scope="col" class="py-3.5 px-4 text-center">Status</th>
                        <th scope="col" class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-slate-800/40 transition group">
                            <td class="py-3.5 px-4 text-center font-mono text-xs text-slate-200 font-medium">
                                {{ $loop->iteration + ($stocks->currentPage() - 1) * $stocks->perPage() }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    @if($stock->cover_image_path)
                                        <img src="{{ $stock->coverImageUrl() ?? asset('storage/'.$stock->cover_image_path) }}" alt="{{ $stock->name }}"
                                             class="w-9 h-9 rounded-lg object-cover ring-1 ring-slate-800 flex-shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-200 text-xs font-mono flex-shrink-0">
                                            📦
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('stocks.detail', $stock->id) }}"
                                           class="font-medium text-slate-100 hover:text-indigo-300 transition leading-snug">
                                            {{ $stock->name }}
                                        </a>
                                        @if($stock->description)
                                            <p class="text-xs text-slate-200 truncate max-w-xs mt-0.5">{{ Str::limit(strip_tags($stock->description), 50) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-200">
                                {{ $stock->sku ?: '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5 font-mono tabular-nums">
                                    <button wire:click="decrement({{ $stock->id }})"
                                            title="Kurangi stok (-1)"
                                            aria-label="Kurangi stok {{ $stock->name }}"
                                            class="w-6 h-6 rounded-md bg-slate-800 hover:bg-rose-950/80 hover:text-rose-200 text-slate-100 border border-slate-700 flex items-center justify-center text-xs font-bold transition">
                                        −
                                    </button>
                                    <span class="min-w-10 text-center font-bold text-slate-100">{{ $stock->quantity }}</span>
                                    <button wire:click="increment({{ $stock->id }})"
                                            title="Tambah stok (+1)"
                                            aria-label="Tambah stok {{ $stock->name }}"
                                            class="w-6 h-6 rounded-md bg-slate-800 hover:bg-emerald-950/80 hover:text-emerald-200 text-slate-100 border border-slate-700 flex items-center justify-center text-xs font-bold transition">
                                        +
                                    </button>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-200">
                                {{ $stock->unit }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono tabular-nums text-slate-100">
                                @if($stock->price !== null)
                                    <span class="text-xs text-slate-200 font-semibold mr-1">Rp</span>{{ number_format((float) $stock->price, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full {{ $stock->status->badgeClass() }}">
                                    {{ $stock->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEditModal({{ $stock->id }})"
                                            aria-label="Edit {{ $stock->name }}"
                                            class="px-2.5 py-1 rounded-md text-xs font-semibold text-slate-100 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                                        Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $stock->id }})"
                                            aria-label="Hapus {{ $stock->name }}"
                                            class="px-2.5 py-1 rounded-md text-xs font-semibold text-rose-200 hover:text-rose-100 bg-rose-950/60 hover:bg-rose-900/70 border border-rose-800 transition">
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
                                    <p class="mt-1 text-xs text-slate-200">Mulai tambahkan produk baru, impor file spreadsheet, atau buat otomatis menggunakan AI Generator.</p>
                                    <button wire:click="openCreateModal" class="mt-4 px-4 py-2 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
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
        <div class="px-4 py-3 bg-slate-950 border-t border-slate-800">
            {{ $stocks->links() }}
        </div>
    </div>

    <!-- MODAL 1: FORM CREATE / EDIT (With Toast UI Editor) -->
    @if($showFormModal)
    <div class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div role="dialog" aria-modal="true" aria-labelledby="form-modal-title" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl max-h-[92vh] flex flex-col shadow-2xl text-slate-100"
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
                    <h2 id="form-modal-title" class="text-base font-bold text-slate-100">
                        {{ $editingStockId ? 'Perbarui Informasi Stok' : 'Tambah Stok Barang Baru' }}
                    </h2>
                </div>
                <button wire:click="closeFormModal" aria-label="Tutup modal form" class="text-slate-200 hover:text-white text-xl font-mono leading-none">&times;</button>
            </div>

            <form wire:submit="saveStock" class="p-6 space-y-4 overflow-y-auto flex-1">
                <div>
                    <label for="stock-form-name" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-1.5">Nama Produk *</label>
                    <input id="stock-form-name" type="text" wire:model="name" placeholder="Contoh: MacBook Pro M3"
                           class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('name') <p class="mt-1 text-xs text-rose-300 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="stock-form-sku" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-1.5">SKU (Stock Keeping Unit)</label>
                        <input id="stock-form-sku" type="text" wire:model="sku" placeholder="Contoh: MBP-M3-01"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label for="stock-form-unit" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-1.5">Satuan Unit *</label>
                        <input id="stock-form-unit" type="text" wire:model="unit" placeholder="pcs, unit, kg, box"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('unit') <p class="mt-1 text-xs text-rose-300 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="stock-form-quantity" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-1.5">Kuantitas Fisik *</label>
                        <input id="stock-form-quantity" type="number" wire:model="quantity" min="0"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('quantity') <p class="mt-1 text-xs text-rose-300 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="stock-form-min-stock" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-1.5">Batas Min. Stok *</label>
                        <input id="stock-form-min-stock" type="number" wire:model="minStock" min="0"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('minStock') <p class="mt-1 text-xs text-rose-300 font-medium">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="stock-form-price" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-1.5">Harga Satuan (Rp)</label>
                        <input id="stock-form-price" type="number" wire:model="price" min="0" step="0.01" placeholder="0"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('price') <p class="mt-1 text-xs text-rose-300 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <span id="editor-desc-label" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-1.5">
                        Deskripsi Spesifikasi (Toast UI Editor - Markdown)
                    </span>
                    <div wire:ignore role="group" aria-labelledby="editor-desc-label" class="rounded-lg overflow-hidden border border-slate-700">
                        <div x-ref="editorContainer"></div>
                    </div>
                    <textarea wire:model="description" class="hidden" aria-hidden="true" tabindex="-1" aria-label="Deskripsi (disinkronkan dari editor)"></textarea>
                </div>

                <div>
                    <label for="cover" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-1.5">Foto Cover Barang</label>
                    <input id="cover" type="file" wire:model="cover" accept="image/*"
                           class="block w-full text-xs text-slate-200 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-100 hover:file:bg-slate-700 cursor-pointer">
                    @error('cover') <p class="mt-1 text-xs text-rose-300 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" wire:click="closeFormModal"
                            class="px-4 py-2 rounded-lg text-xs font-medium text-slate-200 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-600/20 transition"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Simpan Barang</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL 2: IMPORT EXCEL -->
    @if($showImportModal)
    <div class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div role="dialog" aria-modal="true" aria-labelledby="import-modal-title" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md shadow-2xl text-slate-100 p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-sm font-bold">📂</span>
                    <h2 id="import-modal-title" class="text-base font-bold">Import Data Spreadsheet</h2>
                </div>
                <button wire:click="$set('showImportModal', false)" aria-label="Tutup modal import" class="text-slate-200 hover:text-white text-xl font-mono leading-none">&times;</button>
            </div>

            <form wire:submit="importExcel" class="mt-5 space-y-4">
                <div>
                    <label for="import-file-input" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-2">Pilih File (.xlsx, .xls, .csv)</label>
                    <div class="p-6 border-2 border-dashed border-slate-700 rounded-xl bg-slate-950/60 text-center hover:border-slate-600 transition">
                        <input id="import-file-input" type="file" wire:model="importFile" accept=".xlsx,.xls,.csv" class="w-full text-xs text-slate-200 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-100 hover:file:bg-slate-700 cursor-pointer">
                    </div>
                    @error('importFile') <p class="mt-2 text-xs text-rose-300 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="p-3 rounded-lg bg-slate-950 border border-slate-800 text-[11px] font-mono text-slate-200 space-y-1">
                    <p class="text-white font-bold uppercase">Format Kolom Terdukung:</p>
                    <p>No | Name | SKU | Description | Quantity | Unit | Price | Min Stock | Status</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" wire:click="$set('showImportModal', false)"
                            class="px-4 py-2 rounded-lg text-xs font-medium text-slate-200 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg text-xs font-semibold bg-amber-700 hover:bg-amber-800 text-white shadow-md shadow-amber-700/20 transition"
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
        <div role="dialog" aria-modal="true" aria-labelledby="ai-modal-title" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl max-h-[92vh] flex flex-col shadow-2xl text-slate-100">
            <div class="flex items-center justify-between p-5 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-400 flex items-center justify-center text-sm font-bold">✨</span>
                    <h2 id="ai-modal-title" class="text-base font-bold">AI Stock Generator (OpenAI)</h2>
                </div>
                <button wire:click="$set('showAiModal', false)" aria-label="Tutup modal AI" class="text-slate-200 hover:text-white text-xl font-mono leading-none">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                @if(!count($aiItems))
                    <div class="space-y-4">
                        <p class="text-xs text-slate-200">
                            Masukkan kategori inventaris atau kebutuhan pengadaan barang. Asisten AI akan merumuskan spesifikasi lengkap, estimasi kuantitas, harga, dan SKU secara otomatis.
                        </p>
                        <div>
                            <label for="ai-theme-input" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-1.5">
                                Kategori / Kebutuhan Pengadaan *
                            </label>
                            <input id="ai-theme-input"
                                   type="text"
                                   wire:model="aiTheme"
                                   placeholder="Contoh: Perangkat Jaringan Kantor, Suku Cadang Mesin Pabrik..."
                                   class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition">
                            @error('aiTheme') <p class="mt-1.5 text-xs text-rose-300 font-medium">{{ $message }}</p> @enderror
                        </div>

                        @if($aiError)
                            <div class="p-3 rounded-lg bg-rose-950/80 border border-rose-500 text-rose-100 text-xs">
                                {{ $aiError }}
                            </div>
                        @endif

                        <button wire:click="generateAi"
                                aria-label="Generate item stok menggunakan AI"
                                class="w-full py-2.5 px-4 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs shadow-md shadow-purple-600/20 transition flex items-center justify-center gap-2"
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
                        <div class="flex items-center justify-between text-xs text-slate-200">
                            <span>Pratinjau Hasil AI: Beri centang pada item yang ingin ditambahkan ke stok.</span>
                            <span class="font-mono text-purple-300 font-bold">{{ count($aiItems) }} item</span>
                        </div>

                        <div class="space-y-2.5 max-h-[50vh] overflow-y-auto pr-1">
                            @foreach($aiItems as $i => $item)
                                <label class="flex items-start gap-3 p-3.5 rounded-xl border {{ !empty($aiSelected[$i]) ? 'bg-purple-950/30 border-purple-500' : 'bg-slate-950 border-slate-800' }} cursor-pointer hover:border-slate-700 transition">
                                    <input type="checkbox" wire:model="aiSelected.{{ $i }}"
                                           aria-label="Pilih item {{ $item['name'] ?? '' }}"
                                           class="mt-1 h-4 w-4 rounded border-slate-700 bg-slate-900 text-purple-600 focus:ring-purple-500">
                                    <div class="flex-1 text-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-100 text-sm">{{ $item['name'] ?? '' }}</span>
                                            <span class="font-mono text-slate-200 font-semibold">{{ $item['sku'] ?? 'N/A' }}</span>
                                        </div>
                                        @if(!empty($item['description']))
                                            <p class="text-slate-200 mt-1 line-clamp-2">{{ $item['description'] }}</p>
                                        @endif
                                        <div class="mt-2 flex items-center gap-4 text-slate-200 font-mono text-[11px]">
                                            <span>Qty: <strong class="text-white font-bold">{{ $item['quantity'] ?? 0 }}</strong> {{ $item['unit'] ?? 'pcs' }}</span>
                                            <span>Min: {{ $item['min_stock'] ?? 5 }}</span>
                                            <span class="text-emerald-300 font-bold">Rp {{ number_format((float)($item['price'] ?? 0), 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                            <button wire:click="$set('aiItems', [])"
                                    class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-200 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                                &larr; Generate Ulang
                            </button>
                            <button wire:click="saveAiItems"
                                    class="px-5 py-2 rounded-lg text-xs font-semibold bg-purple-600 hover:bg-purple-700 text-white shadow-md shadow-purple-600/20 transition">
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
        <div role="alertdialog" aria-modal="true" aria-labelledby="delete-modal-title" aria-describedby="delete-modal-desc" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md shadow-2xl text-slate-100 p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-rose-950/80 border border-rose-500 text-rose-300 flex items-center justify-center text-lg flex-shrink-0">
                    ⚠️
                </div>
                <div>
                    <h2 id="delete-modal-title" class="text-base font-bold text-slate-100">Konfirmasi Hapus Barang</h2>
                    <p id="delete-modal-desc" class="mt-2 text-xs text-slate-200 leading-relaxed">
                        Apakah Anda yakin ingin menghapus barang <strong class="text-white font-bold">"{{ $deletingStockName }}"</strong>? Tindakan ini bersifat permanen dan tidak dapat dibatalkan.
                        </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-800">
                <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-200 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                    Batal
                </button>
                <button type="button" wire:click="executeDelete"
                        class="px-5 py-2 rounded-lg text-xs font-semibold bg-rose-600 hover:bg-rose-700 text-white shadow-md shadow-rose-600/20 transition">
                    Hapus Permanen
                </button>
            </div>
        </div>
    </div>
    @endif
</div>