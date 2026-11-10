<div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

    <div class="mb-8">
        <!-- Level 1 Heading Wajib -->
        <h1 class="text-2xl font-bold text-slate-100">Pengaturan Profil Saya</h1>
        <p class="text-sm text-slate-300 mt-1">Kelola data identitas akun, foto profil rasio 1:1, dan kata sandi Anda</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Foto Profil -->
        <div class="bg-slate-900/90 p-6 rounded-2xl shadow-sm border border-slate-800 flex flex-col items-center text-center">
            <h2 class="text-base font-bold text-slate-100 mb-4 self-start">Foto Profil (1:1)</h2>

            <div class="relative w-36 h-36 rounded-2xl overflow-hidden border-2 border-slate-700 shadow-inner mb-4 bg-slate-950 flex items-center justify-center">
                @if ($user->profile_photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo_path))
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_photo_path) }}?v={{ time() }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-indigo-900 text-indigo-200 font-bold text-3xl">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
            </div>

            <div class="w-full space-y-2">
                <label for="profilePhotoInput" class="sr-only">Upload Foto Profil Baru</label>
                <input type="file" wire:model="photo" id="profilePhotoInput" aria-label="Upload Foto Profil Baru" accept="image/*" class="hidden">
                
                <button type="button" onclick="document.getElementById('profilePhotoInput').click()" aria-label="Upload Foto Baru" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Upload Foto Baru
                </button>

                @if ($user->profile_photo_path)
                    <button type="button" wire:click="confirmDeletePhoto" aria-label="Hapus Foto Profil" class="w-full py-2 px-4 bg-rose-950/60 hover:bg-rose-900/70 text-rose-200 text-xs font-semibold rounded-lg border border-rose-800 transition">
                        Hapus Foto Profil
                    </button>
                @endif
            </div>

            <p class="text-xs text-slate-300 mt-4">
                Mendukung format PNG, JPG, GIF hingga ukuran 5MB. Foto otomatis dipotong rasio 1:1.
            </p>
        </div>

        <!-- Form Identitas & Password -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-slate-900/90 p-6 rounded-2xl shadow-sm border border-slate-800">
                <h2 class="text-base font-bold text-slate-100 mb-4 pb-2 border-b border-slate-800">Ubah Identitas Diri</h2>

                <form wire:submit.prevent="updateProfileInformation" class="space-y-4">
                    <div>
                        <label for="profile-name" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input wire:model="name" type="text" id="profile-name" aria-label="Nama Lengkap" class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        @error('name') <span class="text-xs text-rose-300 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="profile-username" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Username</label>
                            <input wire:model="username" type="text" id="profile-username" aria-label="Username" class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-700 bg-slate-950 text-slate-100 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            @error('username') <span class="text-xs text-rose-300 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="profile-email" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Email</label>
                            <input wire:model="email" type="email" id="profile-email" aria-label="Alamat Email" class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            @error('email') <span class="text-xs text-rose-300 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" aria-label="Simpan Perubahan Identitas" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ubah Password (Heading Semantik H2) -->
            <div class="bg-slate-900/90 p-6 rounded-2xl shadow-sm border border-slate-800">
                <h2 class="text-base font-bold text-slate-100 mb-4 pb-2 border-b border-slate-800">Keamanan &amp; Ubah Password</h2>

                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label for="profile-current-password" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Password Saat Ini</label>
                        <input wire:model="current_password" type="password" id="profile-current-password" aria-label="Password Saat Ini" class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Password saat ini" required>
                        @error('current_password') <span class="text-xs text-rose-300 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="profile-new-password" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Password Baru</label>
                            <input wire:model="password" type="password" id="profile-new-password" aria-label="Password Baru" class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Minimal 8 karakter" required>
                            @error('password') <span class="text-xs text-rose-300 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="profile-password-confirmation" class="block text-xs font-medium text-slate-200 uppercase tracking-wider mb-1.5">Konfirmasi Password Baru</label>
                            <input wire:model="password_confirmation" type="password" id="profile-password-confirmation" aria-label="Konfirmasi Password Baru" class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-700 bg-slate-950 text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Ulangi password baru" required>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" aria-label="Perbarui Password Akun" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded-lg border border-slate-700 transition">
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>