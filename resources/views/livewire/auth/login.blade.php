<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                📦 Stock Inventory
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">Masuk ke akun Anda</p>
        </div>
        <form wire:submit="login" class="mt-8 space-y-6 bg-white py-8 px-6 shadow rounded-lg">
            @if($errors->any())
                <div class="p-3 rounded-md bg-red-50 border border-red-200">
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="login-username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input
                        id="login-username"
                        type="text"
                        wire:model="username"
                        autocomplete="username"
                        placeholder="admin atau member"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                    >
                    @error('username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="login-password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input
                        id="login-password"
                        type="password"
                        wire:model="password"
                        autocomplete="current-password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border"
                    >
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center">
                    <input id="remember" wire:model="remember" type="checkbox"
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">Ingat saya</label>
                </div>
            </div>

            <div>
                <button
                    id="login-submit"
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Masuk</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>

            <p class="text-center text-xs text-gray-500 mt-4">
                Demo: <strong>admin</strong>/admin &nbsp;|&nbsp; <strong>member</strong>/member
            </p>
        </form>
    </div>
</div>
