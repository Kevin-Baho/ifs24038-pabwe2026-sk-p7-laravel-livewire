<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <!-- Back link -->
        <div class="mb-4">
            <a href="{{ route('stocks.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Kembali ke Daftar Stok</a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        @if($isReadOnly)
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-blue-700 text-sm">
                👁️ Mode baca saja (Admin)
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left: Cover Image + Status -->
            <div class="md:col-span-1 space-y-4">
                <!-- Cover Image -->
                <div class="bg-white shadow rounded-lg p-4">
                    @if($stock->cover_image_path)
                        <img src="{{ Storage::url($stock->cover_image_path) }}"
                             alt="{{ $stock->name }}"
                             class="w-full h-48 object-cover rounded-lg">
                    @else
                        <div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-4xl">
                            📦
                        </div>
                    @endif
                </div>

                <!-- Status Card -->
                <div class="bg-white shadow rounded-lg p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Status</span>
                        <span class="px-3 py-1 text-sm rounded-full {{ $stock->status->badgeClass() }}">
                            {{ $stock->status->label() }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Jumlah</span>
                        <div class="flex items-center gap-2">
                            @if(!$isReadOnly)
                                <button wire:click="decrement"
                                        class="w-7 h-7 rounded bg-red-100 hover:bg-red-200 text-red-600 font-bold flex items-center justify-center">−</button>
                            @endif
                            <span class="text-xl font-bold text-gray-800">{{ $stock->quantity }}</span>
                            @if(!$isReadOnly)
                                <button wire:click="increment"
                                        class="w-7 h-7 rounded bg-green-100 hover:bg-green-200 text-green-600 font-bold flex items-center justify-center">+</button>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-600">Unit</span>
                        <span class="text-gray-800">{{ $stock->unit }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-gray-600">Min Stok</span>
                        <span class="text-gray-800">{{ $stock->min_stock }}</span>
                    </div>
                    @if($stock->price !== null)
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-gray-600">Harga</span>
                            <span class="text-gray-800 font-semibold">Rp {{ number_format((float)$stock->price, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Delete button -->
                @if(!$isReadOnly)
                    <button wire:click="delete"
                            wire:confirm="Yakin ingin menghapus stok '{{ addslashes($stock->name) }}'?"
                            class="w-full py-2 text-sm bg-red-600 text-white rounded-md hover:bg-red-700">
                        🗑️ Hapus Stok
                    </button>
                @endif
            </div>

            <!-- Right: Edit Form -->
            <div class="md:col-span-2 bg-white shadow rounded-lg p-6">
                <h1 class="text-xl font-bold text-gray-800 mb-4">{{ $stock->name }}</h1>

                @if($stock->sku)
                    <p class="text-sm text-gray-500 mb-4">SKU: <span class="font-mono text-gray-700">{{ $stock->sku }}</span></p>
                @endif

                @if(!$isReadOnly)
                    <form wire:submit="save" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Produk *</label>
                            <input type="text" wire:model="name"
                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">SKU</label>
                                <input type="text" wire:model="sku"
                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Unit</label>
                                <input type="text" wire:model="unit"
                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jumlah</label>
                                <input type="number" wire:model="quantity" min="0"
                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Min Stok</label>
                                <input type="number" wire:model="minStock" min="0"
                                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga</label>
                            <input type="number" wire:model="price" min="0" step="0.01"
                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Deskripsi (Markdown)</label>
                            <textarea wire:model="description" rows="5"
                                      class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm font-mono focus:border-blue-500 focus:ring-blue-500">{{ $description }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ganti Cover Image</label>
                            <input type="file" wire:model="cover" accept="image/*" class="mt-1 text-sm text-gray-600">
                            @error('cover') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium"
                                    wire:loading.attr="disabled">
                                💾 Simpan Perubahan
                            </button>
                        </div>
                    </form>
                @else
                    @if($stock->description)
                        <div class="prose prose-sm max-w-none text-gray-700 mt-4">
                            {!! nl2br(e($stock->description)) !!}
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic mt-4">Tidak ada deskripsi.</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
