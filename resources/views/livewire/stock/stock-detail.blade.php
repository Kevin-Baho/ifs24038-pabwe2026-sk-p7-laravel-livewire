<div>
    <!-- Back Navigation & Actions -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <a href="{{ route('stocks.index') }}"
           class="inline-flex items-center gap-2 text-xs font-mono text-slate-300 hover:text-indigo-400 transition">
            <span>&larr;</span>
            <span>Kembali ke Inventaris</span>
        </a>

        @if(!$isReadOnly)
            <div class="flex items-center gap-2">
                <button wire:click="openEditModal"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-sm transition flex items-center gap-1.5">
                    <span>✏️</span>
                    <span>Edit Informasi Barang</span>
                </button>
                <button wire:click="delete"
                        wire:confirm="Yakin ingin menghapus stok '{{ addslashes($stock->name) }}'?"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium bg-rose-950/40 hover:bg-rose-900/50 text-rose-300 border border-rose-900/50 transition">
                    Hapus
                </button>
            </div>
        @else
            <span class="px-2.5 py-1 rounded-md text-xs font-mono uppercase bg-amber-950/40 text-amber-300 border border-amber-800/50">
                Mode Read-Only (Admin)
            </span>
        @endif
    </div>

    <!-- MAIN GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- LEFT: Hero Cover Image & Quick Stepper -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Cover Hero Container -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-slate-950 border border-slate-800 flex items-center justify-center group">
                    @if($stock->cover_image_path)
                        <img src="{{ $stock->coverImageUrl() ?? asset('storage/'.$stock->cover_image_path) }}" alt="{{ $stock->name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="text-center p-6">
                            <span class="text-5xl">📦</span>
                            <p class="mt-2 text-xs text-slate-300 font-mono">Belum ada foto cover</p>
                        </div>
                    @endif

                    @if(!$isReadOnly && $stock->cover_image_path)
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                            <button wire:click="removeCover"
                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-600 hover:bg-rose-500 text-white transition">
                                Hapus Cover
                            </button>
                        </div>
                    @endif
                </div>

                @if(!$isReadOnly)
                    <div class="pt-2 border-t border-slate-800/80">
                        <label for="detail-cover-input" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-2">
                            {{ $stock->cover_image_path ? 'Ganti Cover Barang' : 'Unggah Cover Barang' }}
                        </label>
                        <div class="flex items-center gap-2">
                            <input id="detail-cover-input" type="file" wire:model="cover" accept="image/*" aria-label="Unggah cover barang"
                                   class="text-xs text-slate-300 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer w-full">
                        </div>
                        @if($cover)
                            <button wire:click="updateCover"
                                    class="mt-2 w-full py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-sm transition">
                                Simpan Foto Baru
                            </button>
                        @endif
                        @error('cover') <p class="mt-1.5 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>

            <!-- Big Stepper Card -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-300 uppercase tracking-wider">Kuantitas Fisik</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $stock->status->badgeClass() }}">
                        {{ $stock->status->label() }}
                    </span>
                </div>

                <div class="flex items-center justify-between gap-4 py-2">
                    @if(!$isReadOnly)
                        <button wire:click="decrement"
                                title="Kurangi Kuantitas (-1)"
                                aria-label="Kurangi kuantitas {{ $stock->name }}"
                                class="w-12 h-12 rounded-xl bg-slate-800 hover:bg-rose-950/60 hover:text-rose-400 hover:border-rose-800 text-slate-200 border border-slate-700 flex items-center justify-center text-xl font-bold transition shadow-sm">
                            &minus;
                        </button>
                    @endif

                    <div class="text-center flex-1">
                        <span class="text-4xl font-extrabold font-mono tabular-nums text-slate-100">
                            {{ $stock->quantity }}
                        </span>
                        <span class="block text-xs font-mono text-slate-300 mt-1">
                            {{ $stock->unit }}
                        </span>
                    </div>

                    @if(!$isReadOnly)
                        <button wire:click="increment"
                                title="Tambah Kuantitas (+1)"
                                aria-label="Tambah kuantitas {{ $stock->name }}"
                                class="w-12 h-12 rounded-xl bg-slate-800 hover:bg-emerald-950/60 hover:text-emerald-400 hover:border-emerald-800 text-slate-200 border border-slate-700 flex items-center justify-center text-xl font-bold transition shadow-sm">
                            &#43;
                        </button>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-300 font-mono">
                    <span>Ambang Batas Minimum:</span>
                    <span class="font-bold text-slate-100">{{ $stock->min_stock }} {{ $stock->unit }}</span>
                </div>
            </div>
        </div>

        <!-- RIGHT: Product Specifications & Markdown Viewer -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Header Spec Card -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                <div>
                    <span class="text-[11px] font-mono uppercase tracking-wider text-indigo-400 font-semibold">Detail Spesifikasi</span>
                    <h1 class="text-2xl font-bold text-slate-100 mt-1">{{ $stock->name }}</h1>
                </div>

                <!-- 4 Spec Cards -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                    <div class="p-3.5 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-slate-300 block">Kode SKU</span>
                        <span class="text-xs font-mono font-bold text-slate-100 mt-1 block truncate">{{ $stock->sku ?: 'N/A' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-slate-300 block">Satuan</span>
                        <span class="text-xs font-mono font-bold text-slate-100 mt-1 block uppercase">{{ $stock->unit }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-slate-300 block">Harga Satuan</span>
                        <span class="text-xs font-mono font-bold text-slate-100 mt-1 block truncate">
                            @if($stock->price !== null)
                                Rp {{ number_format((float) $stock->price, 0, ',', '.') }}
                            @else
                                —
                            @endif
                        </span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-950/80 border border-slate-800">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-slate-300 block">Total Nilai Aset</span>
                        <span class="text-xs font-mono font-bold text-emerald-300 mt-1 block truncate">
                            Rp {{ number_format(((float) ($stock->price ?? 0)) * $stock->quantity, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Markdown Description Viewer (Toast UI Viewer) -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h2 class="text-xs font-mono uppercase tracking-wider text-slate-300 font-semibold">Deskripsi &amp; Catatan Teknis (Markdown)</h2>
                    <span class="text-[10px] font-mono text-slate-300 font-medium">Toast UI Viewer</span>
                </div>

                <div x-data="{
                        markdown: @js($stock->description ?? ''),
                        initViewer() {
                            const el = this.$refs.viewerContainer;
                            if (!el) return;
                            el.innerHTML = '';
                            if (!this.markdown) return;
                            toastui.Editor.factory({
                                el: el,
                                viewer: true,
                                height: 'auto',
                                theme: 'dark',
                                initialValue: this.markdown
                            });
                        }
                     }"
                     x-init="$nextTick(() => initViewer())"
                     class="min-h-32 text-slate-200 text-sm leading-relaxed">
                    @if($stock->description)
                        <div wire:ignore x-ref="viewerContainer" class="toastui-editor-contents"></div>
                    @else
                        <div class="py-8 text-center text-slate-300 text-xs italic font-mono">
                            Belum ada deskripsi spesifikasi markdown untuk barang ini.
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between text-[11px] font-mono text-slate-300">
                    <span>Dibuat: {{ $stock->created_at?->format('d M Y, H:i') }}</span>
                    <span>Diperbarui: {{ $stock->updated_at?->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT INFORMASI BARANG LENGKAP -->
    @if($showEditModal && !$isReadOnly)
    <div class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl max-h-[92vh] flex flex-col shadow-2xl text-slate-100"
             x-data="{
                description: @entangle('description'),
                initEditor() {
                    const el = this.$refs.detailEditorContainer;
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
                    <span class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center text-sm font-bold">✏️</span>
                    <h2 class="text-base font-bold text-slate-100">Edit Informasi Barang</h2>
                </div>
                <button wire:click="$set('showEditModal', false)" aria-label="Tutup modal edit" class="text-slate-300 hover:text-white text-xl font-mono leading-none">&times;</button>
            </div>

            <form wire:submit="saveEdit" class="p-6 space-y-4 overflow-y-auto flex-1">
                <div>
                    <label for="edit-stock-name" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Nama Produk *</label>
                    <input id="edit-stock-name" type="text" wire:model="name"
                           class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('name') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="edit-stock-sku" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">SKU</label>
                        <input id="edit-stock-sku" type="text" wire:model="sku"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label for="edit-stock-unit" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Satuan Unit *</label>
                        <input id="edit-stock-unit" type="text" wire:model="unit"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('unit') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="edit-stock-quantity" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Kuantitas Fisik *</label>
                        <input id="edit-stock-quantity" type="number" wire:model="quantity" min="0"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('quantity') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="edit-stock-minStock" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Batas Min. Stok *</label>
                        <input id="edit-stock-minStock" type="number" wire:model="minStock" min="0"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('minStock') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="edit-stock-price" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Harga Satuan (Rp)</label>
                        <input id="edit-stock-price" type="number" wire:model="price" min="0" step="0.01"
                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2 text-sm font-mono text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">
                        Deskripsi Spesifikasi (Toast UI Editor - Markdown)
                    </label>
                    <div wire:ignore class="rounded-lg overflow-hidden border border-slate-700">
                        <div x-ref="detailEditorContainer"></div>
                    </div>
                    <textarea wire:model="description" class="hidden"></textarea>
                </div>

                <div>
                    <label for="edit-stock-cover" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Ganti Foto Cover</label>
                    <input id="edit-stock-cover" type="file" wire:model="cover" accept="image/*" aria-label="Ganti foto cover"
                           class="block w-full text-xs text-slate-300 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" wire:click="$set('showEditModal', false)"
                            class="px-4 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
