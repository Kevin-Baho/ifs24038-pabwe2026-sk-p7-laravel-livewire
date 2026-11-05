<x-layouts.app>
    <div>
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Manajemen Pengguna</h1>

        <div class="mb-4">
            <input type="text" wire:model.live="search" placeholder="Cari nama / username..."
                   class="border border-gray-300 rounded-md px-3 py-2 text-sm w-80 focus:border-blue-500 focus:ring-blue-500">
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Stok</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $i => $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->username }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">{{ $user->stocks_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">
                                Rp {{ number_format($user->stocks_sum_price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.users.stocks', $user->id) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Lihat Stok
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
