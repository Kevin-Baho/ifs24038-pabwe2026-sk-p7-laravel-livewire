<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full space-y-8">
        <h2 class="text-center text-3xl font-extrabold text-gray-900">Daftar Akun Baru</h2>
        <form wire:submit="register" class="mt-8 space-y-4 bg-white py-8 px-6 shadow rounded-lg">
            @foreach(['name' => 'Nama Lengkap', 'username' => 'Username', 'email' => 'Email', 'password' => 'Password', 'password_confirmation' => 'Konfirmasi Password'] as $field => $label)
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                    <input
                        type="{{ in_array($field, ['password', 'password_confirmation']) ? 'password' : 'text' }}"
                        wire:model="{{ $field }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                    >
                    @error($field) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            @endforeach
            <button type="submit"
                    class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                Daftar
            </button>
            <p class="text-center text-sm text-gray-500">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Masuk</a>
            </p>
        </form>
    </div>
</div>
