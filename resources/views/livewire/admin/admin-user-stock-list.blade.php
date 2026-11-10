<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <a href="{{ route('admin.users') }}"
               class="inline-flex items-center gap-1.5 text-xs font-mono text-slate-300 hover:text-indigo-400 transition mb-2">
                <span>&larr;</span>
                <span>Kembali ke Daftar Pengguna</span>
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-100 flex items-center gap-3">
                <span>Inventaris: {{ $targetUser->name }}</span>
                <span class="text-xs font-mono font-normal uppercase px-2 py-0.5 rounded bg-slate-800 text-slate-200 border border-slate-700">
                    Mode Read-Only
                </span>
            </h1>
            <p class="text-xs text-slate-300 mt-1">
                Username: <span class="font-mono text-slate-200">{{ $targetUser->username }}</span> &bull; Email: <span class="font-mono text-slate-200">{{ $targetUser->email }}</span>
            </p>
        </div>

        <button wire:click="exportExcel"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold bg-emerald-950/60 hover:bg-emerald-900/70 text-emerald-300 border border-emerald-800/60 shadow-sm transition">
            <span>📥</span>
            <span>Export Excel Stok Pengguna Ini</span>
        </button>
    </div>

    <!-- Search Toolbar -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 shadow-sm">
        <div class="relative max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-sm">🔍</span>
            <label for="admin-stock-search" class="sr-only">Cari nama barang atau SKU milik pengguna</label>
            <input id="admin-stock-search"
                   type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari nama barang atau SKU milik pengguna..."
                   aria-label="Cari nama barang atau SKU milik pengguna"
                   class="w-full bg-slate-950 border border-slate-700 rounded-lg pl-9 pr-3 py-2 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-950/80 border-b border-slate-800 text-[11px] font-mono uppercase tracking-wider text-slate-300">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Nama Produk</th>
                        <th class="py-3.5 px-4">SKU</th>
                        <th class="py-3.5 px-4 text-right">Kuantitas</th>
                        <th class="py-3.5 px-4">Satuan</th>
                        <th class="py-3.5 px-4 text-right">Harga Satuan</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($stocks as $i => $stock)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-4 text-center font-mono text-xs text-slate-300 font-medium">{{ $i + 1 }}</td>
                            <td class="py-3.5 px-4 font-medium text-slate-100">{{ $stock->name }}</td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-300">{{ $stock->sku ?: '—' }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold tabular-nums text-slate-100">{{ $stock->quantity }}</td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-300">{{ $stock->unit }}</td>
                            <td class="py-3.5 px-4 text-right font-mono tabular-nums text-slate-100">
                                @if($stock->price !== null)
                                    <span class="text-xs text-slate-300 font-semibold mr-1">Rp</span>{{ number_format((float) $stock->price, 0, ',', '.') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-medium rounded-full {{ $stock->status->badgeClass() }}">
                                    {{ $stock->status->label() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-slate-300 font-mono">
                                Pengguna ini belum memiliki data stok barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
