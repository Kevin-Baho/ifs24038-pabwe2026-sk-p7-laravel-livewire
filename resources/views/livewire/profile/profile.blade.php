<x-layouts.app>
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Profil Saya</h1>

        <form wire:submit="save" class="bg-white shadow rounded-lg p-6 space-y-4">
            @if(session('status'))
                <div class="p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">{{ session('status') }}</div>
            @endif

            <!-- Avatar -->
            <div class="flex items-center space-x-4">
                @if($user->profile_photo_path)
                    <img src="{{ Storage::url($user->profile_photo_path) }}" class="h-16 w-16 rounded-full object-cover">
                @else
                    <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <input type="file" wire:model="photo" accept="image/*" class="text-sm text-gray-600">
                    @error('photo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            @foreach(['name' => 'Nama', 'username' => 'Username', 'email' => 'Email'] as $field => $label)
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                    <input type="text" wire:model="{{ $field }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error($field) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <div>
                <label class="block text-sm font-medium text-gray-700">Password Baru (kosongkan jika tidak diubah)</label>
                <input type="password" wire:model="password"
                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" wire:model="password_confirmation"
                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
