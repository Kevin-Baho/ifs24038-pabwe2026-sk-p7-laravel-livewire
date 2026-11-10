<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 text-2xl mb-3 shadow-lg shadow-indigo-500/10">
                ✨
            </div>
            <!-- Heading Level 1 Wajib -->
            <h1 class="text-2xl font-bold text-slate-100">Buat Akun Member</h1>
            <p class="mt-1 text-sm text-slate-300">
                Mulai kelola inventaris barang secara terpusat dan modern
            </p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl backdrop-blur-sm">
            <form wire:submit="register" class="space-y-4">
                <div>
                    <label for="register-name" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input id="register-name" type="text" wire:model="name" aria-label="Nama Lengkap" placeholder="John Doe"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('name') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="register-username" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Username</label>
                    <input id="register-username" type="text" wire:model="username" aria-label="Username" placeholder="johndoe"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('username') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="register-email" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Email</label>
                    <input id="register-email" type="email" wire:model="email" aria-label="Email" placeholder="john@example.com"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('email') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="register-password" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Password</label>
                        <input id="register-password" type="password" wire:model="password" aria-label="Password" placeholder="••••••••"
                               class="w-full bg-slate-950/80 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('password') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="register-password-confirmation" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Konfirmasi</label>
                        <input id="register-password-confirmation" type="password" wire:model="password_confirmation" aria-label="Konfirmasi Password" placeholder="••••••••"
                               class="w-full bg-slate-950/80 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        aria-label="Daftar Sekarang"
                        class="w-full py-2.5 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition flex items-center justify-center gap-2"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Daftar Sekarang</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mendaftarkan...
                        </span>
                    </button>
                </div>

                <div class="pt-2 border-t border-slate-800/80 text-center text-xs text-slate-300">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium ml-1">
                        Masuk di sini
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>