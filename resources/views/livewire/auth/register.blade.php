<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 text-2xl mb-3 shadow-lg shadow-indigo-500/10">
                ✨
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-100">
                Registrasi Akun Member
            </h2>
            <p class="mt-1 text-sm text-slate-400">
                Mulai kelola inventaris barang secara terpusat dan modern
            </p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl backdrop-blur-sm">
            <form wire:submit="register" class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" wire:model="name" placeholder="John Doe"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('name') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 uppercase tracking-wider mb-1.5">Username</label>
                    <input type="text" wire:model="username" placeholder="johndoe"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('username') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-300 uppercase tracking-wider mb-1.5">Email</label>
                    <input type="email" wire:model="email" placeholder="john@example.com"
                           class="w-full bg-slate-950/80 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('email') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
                        <input type="password" wire:model="password" placeholder="••••••••"
                               class="w-full bg-slate-950/80 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                        @error('password') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 uppercase tracking-wider mb-1.5">Konfirmasi</label>
                        <input type="password" wire:model="password_confirmation" placeholder="••••••••"
                               class="w-full bg-slate-950/80 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition flex items-center justify-center gap-2"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Daftar Sekarang</span>
                        <span wire:loading>Mendaftarkan...</span>
                    </button>
                </div>

                <div class="pt-2 border-t border-slate-800/80 text-center text-xs text-slate-400">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium ml-1">
                        Masuk di sini
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
