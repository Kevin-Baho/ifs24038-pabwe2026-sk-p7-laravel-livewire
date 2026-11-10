<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-100">Pengaturan Profil Pengguna</h1>
            <p class="text-xs text-slate-300 mt-1">Perbarui identitas akun, kredensial keamanan, dan avatar profil.</p>
        </div>
        <span class="px-2.5 py-1 rounded-md text-xs font-mono uppercase bg-indigo-950/60 text-indigo-300 border border-indigo-500/40 font-semibold">
            {{ $user->role }}
        </span>
    </div>

    @if(session('status'))
        <div class="p-4 rounded-xl bg-emerald-950/60 border border-emerald-800/60 text-emerald-300 text-sm flex items-center gap-2.5 shadow-sm">
            <span class="text-emerald-400">✓</span>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <form wire:submit="save" class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
        <!-- Avatar & Cropper Section -->
        <div>
            <label for="profile-photo" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-3">Foto Profil (Rasio 1:1 &bull; 400x400 PNG)</label>
            <div class="flex flex-col sm:flex-row items-center gap-6"
                 x-data="{
                    cropper: null,
                    setupCropper(event) {
                        const file = event.target.files[0];
                        if (!file) return;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const img = this.$refs.cropPreview;
                            img.src = e.target.result;
                            img.classList.remove('hidden');
                            if (this.cropper) {
                                this.cropper.destroy();
                            }
                            this.cropper = new Cropper(img, {
                                aspectRatio: 1,
                                viewMode: 1,
                                autoCropArea: 1,
                            });
                        };
                        reader.readAsDataURL(file);
                    }
                 }">
                <div class="relative w-24 h-24 rounded-full overflow-hidden ring-2 ring-indigo-500/40 bg-slate-950 flex-shrink-0 shadow-lg">
                    <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->name }}"
                         class="w-full h-full object-cover">
                </div>

                <div class="flex-1 w-full space-y-2">
                    <input id="profile-photo" type="file" wire:model="photo" accept="image/*" @change="setupCropper($event)" aria-label="Upload foto profil"
                           class="block w-full text-xs text-slate-300 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                    <p class="text-[11px] text-slate-300 font-mono">
                        Format didukung: JPG, PNG, WebP (Maks. 2MB). Foto akan dipotong rasio 1:1 tepat di tengah menjadi 400x400 PNG.
                    </p>
                    @error('photo') <p class="text-xs text-rose-400">{{ $message }}</p> @enderror

                    <!-- Cropper Preview Area -->
                    <div class="mt-2 max-w-xs overflow-hidden rounded-lg border border-slate-800 bg-slate-950">
                        <img x-ref="cropPreview" class="hidden max-h-48 w-full object-contain" alt="Crop Preview">
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800/80 grid grid-cols-1 gap-4">
            <div>
                <label for="profile-name" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Nama Lengkap *</label>
                <input id="profile-name" type="text" wire:model="name"
                       class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                @error('name') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="profile-username" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Username *</label>
                    <input id="profile-username" type="text" wire:model="username"
                           class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm font-mono text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('username') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="profile-email" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Email Akun *</label>
                    <input id="profile-email" type="email" wire:model="email"
                           class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('email') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Security / Password -->
        <div class="pt-4 border-t border-slate-800/80 space-y-4">
            <h2 class="text-xs font-mono uppercase tracking-wider text-slate-300 font-semibold">Keamanan &amp; Ubah Password</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="profile-password" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Password Baru</label>
                    <input id="profile-password" type="password" wire:model="password" placeholder="Kosongkan jika tidak diubah"
                           class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    @error('password') <p class="mt-1 text-xs text-rose-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="profile-password-confirmation" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Konfirmasi Password Baru</label>
                    <input id="profile-password-confirmation" type="password" wire:model="password_confirmation" placeholder="Ulangi password baru"
                           class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3.5 py-2.5 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800/80 flex items-center justify-end gap-3">
            <button type="submit"
                    class="px-5 py-2.5 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-md shadow-indigo-600/20 transition flex items-center gap-2"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Simpan Perubahan</span>
                <span wire:loading>Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
