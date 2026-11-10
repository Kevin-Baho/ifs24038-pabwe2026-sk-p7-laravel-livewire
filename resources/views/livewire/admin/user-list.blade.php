<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-100">Manajemen Pengguna &amp; Inventaris</h1>
            <p class="text-xs text-slate-300 mt-1">Daftar seluruh pengguna terdaftar beserta ringkasan macam stok inventaris masing-masing.</p>
        </div>
        <span class="px-3 py-1 rounded-lg text-xs font-mono uppercase bg-indigo-950/50 text-indigo-300 border border-indigo-500/30">
            Admin Workspace
        </span>
    </div>

    <!-- Filter / Search -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-xl p-4 shadow-sm">
        <div class="relative max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-300 text-sm" aria-hidden="true">🔍</span>
            <label for="admin-user-search" class="sr-only">Cari pengguna berdasarkan nama atau username</label>
            <input type="text"
                   id="admin-user-search"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari berdasarkan nama atau username..."
                   class="w-full bg-slate-950 border border-slate-700 rounded-lg pl-9 pr-3 py-2 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-400" tabindex="0" role="region" aria-label="Tabel daftar pengguna (dapat digulir)">
            <table class="w-full text-left text-sm">
                <caption class="sr-only">Daftar pengguna beserta total stok dan estimasi aset</caption>
                <thead class="bg-slate-950/80 border-b border-slate-800 text-[11px] font-mono uppercase tracking-wider text-slate-300">
                    <tr>
                        <th scope="col" class="py-3.5 px-4 text-center w-12">No</th>
                        <th scope="col" class="py-3.5 px-4">Pengguna</th>
                        <th scope="col" class="py-3.5 px-4">Email</th>
                        <th scope="col" class="py-3.5 px-4 text-center">Role</th>
                        <th scope="col" class="py-3.5 px-4 text-right">Total Macam Stok</th>
                        <th scope="col" class="py-3.5 px-4 text-right">Estimasi Aset</th>
                        <th scope="col" class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($users as $i => $user)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-4 text-center font-mono text-xs text-slate-300 font-medium">{{ $i + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->name }}"
                                         class="w-8 h-8 rounded-full object-cover ring-1 ring-slate-800 flex-shrink-0">
                                    <div>
                                        <div class="font-medium text-slate-100">{{ $user->name }}</div>
                                        <div class="text-[11px] font-mono text-slate-300">@<span>{{ $user->username }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-300">{{ $user->email }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-0.5 text-xs font-mono uppercase rounded-md border {{ $user->role === 'admin' ? 'bg-purple-950/60 text-purple-300 border-purple-800/60' : 'bg-slate-800 text-slate-300 border-slate-700' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono tabular-nums text-slate-200">
                                <span class="font-bold">{{ $user->stocks_count ?? 0 }}</span>
                                <span class="text-xs text-slate-300 font-semibold ml-1">macam</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono tabular-nums text-slate-200">
                                <span class="text-xs text-slate-300 font-semibold mr-1">Rp</span>{{ number_format((float) ($user->stocks_sum_price ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.users.stocks', $user->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white transition shadow-sm">
                                    <span>Lihat Stok</span>
                                    <span>&rarr;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-slate-300 font-mono">
                                Tidak ada pengguna yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
