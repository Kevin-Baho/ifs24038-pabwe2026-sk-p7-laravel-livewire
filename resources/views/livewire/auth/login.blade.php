<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-600/20 border border-indigo-500/30 text-indigo-400 text-2xl mb-3 shadow-lg shadow-indigo-500/10">
                ⚡
            </div>
            <!-- Heading Level 1 Wajib -->
            <h1 class="text-2xl font-bold text-slate-100">Masuk ke Akun Anda</h1>
            <p class="mt-1 text-sm text-slate-200">
                Kelola stok dan inventaris barang dengan efisiensi tinggi
            </p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl backdrop-blur-sm">
            <form wire:submit="login" class="space-y-5">
                @if($errors->any())
                    <div class="p-3.5 rounded-lg bg-rose-950/80 border border-rose-500 text-rose-100 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <p class="flex items-center gap-2">
                                <span class="text-rose-300 font-bold">●</span>
                                <span>{{ $error }}</span>
                            </p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label for="login-username" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-1.5">
                        Username atau Email
                    </label>
                    <input
                        id="login-username"
                        type="text"
                        wire:model="username"
                        autocomplete="username"
                        placeholder="admin atau member"
                        class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    >
                    @error('username') <p class="mt-1.5 text-xs text-rose-300 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="login-password" class="block text-xs font-semibold text-slate-200 uppercase tracking-wider mb-1.5">
                        Password
                    </label>
                    <input
                        id="login-password"
                        type="password"
                        wire:model="password"
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                    >
                    @error('password') <p class="mt-1.5 text-xs text-rose-300 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember" class="flex items-center gap-2 cursor-pointer text-xs text-slate-200 select-none">
                        <input id="remember" wire:model="remember" type="checkbox"
                               class="rounded border-slate-700 bg-slate-950 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-900">
                        <span>Ingat sesi saya</span>
                    </label>
                </div>

                <div>
                    <button
                        id="login-submit"
                        type="submit"
                        aria-label="Masuk ke Akun"
                        class="w-full py-2.5 px-4 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900 transition flex items-center justify-center gap-2"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Masuk ke Akun</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memverifikasi...
                        </span>
                    </button>
                </div>

                <div class="pt-2 border-t border-slate-800 text-center text-xs text-slate-200">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-indigo-300 hover:text-white underline underline-offset-2 font-semibold ml-1 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                        Daftar sebagai Member
                    </a>
                </div>
            </form>
        </div>

        <!-- Kredensial Kontras Tinggi (Lolos WCAG 4.5:1) -->
        <div class="text-center font-mono text-xs text-slate-200">
            Kredensial Bawaan: <span class="text-white font-bold">admin / admin</span> &nbsp;•&nbsp; <span class="text-white font-bold">member / member</span>
        </div>
    </div>
</div>