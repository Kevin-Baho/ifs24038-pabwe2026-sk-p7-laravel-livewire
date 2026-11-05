<x-layouts.app>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Kembali ke Daftar Pengguna</a>
                <h1 class="text-2xl font-bold text-gray-800 mt-1">Stok Milik: {{ $targetUser->name }}</h1>
            </div>
        </div>

        <div class="mb-4">
            <input type="text" wire:model.live="search" placeholder="Cari nama / SKU..."
                   class="border border-gray-300 rounded-md px-3 py-2 text-sm w-80 focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stocks as $i => $stock)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $stock->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $stock->sku }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">{{ $stock->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $stock->unit }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">
                                @if($stock->price !== null)
                                    Rp {{ number_format((float)$stock->price, 0, ',', '.') }}
                                @else —
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $stock->status->badgeClass() }}">
                                    {{ $stock->status->label() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada stok.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
