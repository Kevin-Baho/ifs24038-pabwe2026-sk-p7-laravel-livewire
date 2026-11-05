# Aplikasi Manajemen Stok Barang (Stock Inventory)

> **Studi Kasus P7**: Aplikasi Manajemen Stok Barang berbasis **Laravel 13**, **Livewire 4**, **PHP 8.3+**, **Tailwind CSS**, dan **PhpSpreadsheet** menggunakan arsitektur **Feature-First / Modular Architecture** dengan pengujian unit & feature test **100% Code Coverage** pada direktori `app/` serta lolos audit keamanan (`composer audit`).

---

## 1. Arsitektur & Struktur Direktori (Feature-First)

Seluruh logika bisnis dikelompokkan ke dalam modul domain di bawah `app/Features/`:

```
app/
├── Features/
│   ├── Admin/
│   │   ├── Livewire/
│   │   │   ├── AdminUserStockList.php   # Pratinjau stok pengguna (read-only)
│   │   │   └── UserList.php             # Tabel metrik pengguna (total stok & aset)
│   │   └── Services/
│   │       └── AdminUserService.php     # Query agregasi pengguna & stok
│   ├── Ai/
│   │   ├── Contracts/
│   │   │   └── AiClientInterface.php    # Kontrak klien AI generatif
│   │   ├── DTO/
│   │   │   └── GeneratedStockItem.php   # DTO data hasil generasi AI
│   │   └── Services/
│   │       └── OpenAiStockGeneratorService.php # Service prompt & parser JSON AI
│   ├── Auth/
│   │   ├── Actions/
│   │   │   ├── LoginUserAction.php      # Action autentikasi login
│   │   │   └── RegisterUserAction.php   # Action pendaftaran akun member
│   │   └── Livewire/
│   │       ├── Login.php                # Komponen form login
│   │       └── Register.php             # Komponen form register
│   ├── Profile/
│   │   ├── Livewire/
│   │   │   └── Profile.php              # Komponen pengelolaan profil & avatar
│   │   └── Services/
│   │       └── ProfileService.php       # Service pembaruan data & foto profil
│   └── Stock/
│       ├── Contracts/
│       │   └── StockRepositoryInterface.php # Kontrak repositori stok
│       ├── Enums/
│       │   └── StockStatus.php          # Enum status stok (Available, LowStock, OutOfStock)
│       ├── Livewire/
│       │   ├── StockDetail.php          # Halaman detail & edit stok
│       │   └── StockList.php            # Halaman daftar stok & modal aksi
│       ├── Models/
│       │   └── Stock.php                # Model Eloquent stok barang
│       ├── Policies/
│       │   └── StockPolicy.php          # Kebijakan otorisasi data stok
│       ├── Repositories/
│       │   └── StockRepository.php      # Implementasi query repositori
│       └── Services/
│           ├── StockExcelService.php    # Service Import/Export Excel via PhpSpreadsheet
│           └── StockService.php         # Service logika stok & penyesuaian kuantitas
├── Http/
│   └── Middleware/
│       ├── EnsureRole.php               # Middleware pembatasan role admin/member
│       └── EnsureStockOwnership.php     # Middleware verifikasi kepemilikan data stok
├── Models/
│   └── User.php                         # Model pengguna
└── Providers/
    └── AppServiceProvider.php           # Dependency injection & HTTPS enforcement
```

---

## 2. Kredensial Pengguna Bawaan (Database Seeder)

| Role | Username | Email | Password | Hak Akses |
|---|---|---|---|---|
| **Admin** | `admin` | `admin@example.com` | `admin` | Akses global seluruh stok (Read-Only), tabel metrik pengguna |
| **Member** | `member` | `member@example.com` | `member` | Akses penuh CRUD stok milik sendiri, Generator AI, Import/Export |

---

## 3. Fitur Utama

1. **Dashboard & Manajemen Stok:**
   - Pencarian instan berdasarkan Nama atau SKU.
   - Filter berdasarkan status stok (`Tersedia`, `Stok Menipis`, `Habis`).
   - Tombol **Quick Adjust Quantity**: Aksi instan increment (`+1`) dan decrement (`-1`) langsung memperbarui status stok secara real-time.
   - Pagination dan sorting dinamis (Nama, SKU, Qty, Harga).
2. **Detail Stok & Markdown Editor:**
   - Pratinjau gambar cover stok barang.
   - Render deskripsi berformat Markdown.
   - Form pembaruan field lengkap & aksi hapus data.
3. **Import & Export Excel (PhpSpreadsheet):**
   - Format header standar: `No`, `Name`, `SKU`, `Quantity`, `Unit`, `Price`, `Min Stock`, `Status`.
   - Styling otomatis: background header, auto-size column, border tipis, dan parsing otomatis status enum saat import.
4. **AI Stock Generator (OpenAI Integration):**
   - Masukkan tema/kategori inventaris (contoh: *Alat Tulis Kantor*, *Sparepart Motor*).
   - Menghasilkan daftar rekomendasi item stok dalam format JSON terstruktur.
   - Pratinjau modal dengan opsi checkbox pilihan untuk disimpan secara massal ke database.
5. **Admin Panel (Read-Only):**
   - Ringkasan metrik setiap user: total item stok dan total aset inventaris (Rp).
   - Melihat daftar stok milik user tertentu secara read-only tanpa tombol aksi mutasi.
6. **Autentikasi & Autorisasi:**
   - Input HTML form login memiliki ID wajib: `id="login-username"`, `id="login-password"`, `id="login-submit"`.
   - Middleware `role:{role}` dan `owns.stock`.
   - Rate limiting `throttle:1000,1` pada rute terproteksi.
   - HTTPS scheme enforcement otomatis pada environment production atau `FORCE_HTTPS=true`.

---

## 4. Panduan Menjalankan Aplikasi

### Persyaratan Sistem
- PHP >= 8.3
- Composer >= 2.x
- Ekstensi PHP: `pdo_sqlite` / `pdo_mysql`, `mbstring`, `gd`, `zip`, `xml`

### Instalasi
```bash
# Salin konfigurasi environment
cp .env.example .env

# Generate Application Key
php artisan key:generate

# Jalankan migrasi dan seeder
php artisan migrate:fresh --seed

# Jalankan server pengembangan
php artisan serve
```

Akses aplikasi pada peramban: `http://localhost:8000`

---

## 5. Pengujian & Code Coverage (100% Target)

Suite pengujian mencakup 66 skenario Feature Test dan Unit Test dengan **100.0% coverage** pada direktori `app/`:

```bash
# Menjalankan seluruh test suite dengan tabel code coverage
php artisan test --coverage
```

Output:
```
 Features/Admin/Livewire\AdminUserStockList . 100.0% 
 Features/Admin/Livewire\UserList .......... 100.0% 
 Features/Admin/Services\AdminUserService .. 100.0% 
 Features/Ai/Contracts\AiClientInterface ... 100.0% 
 Features/Ai/DTO\GeneratedStockItem ........ 100.0% 
 Features/Ai/Services\OpenAiStockGenerator.. 100.0% 
 Features/Auth/Actions\LoginUserAction ..... 100.0% 
 Features/Auth/Actions\RegisterUserAction .. 100.0% 
 Features/Auth/Livewire\Login .............. 100.0% 
 Features/Auth/Livewire\Register ........... 100.0% 
 Features/Profile/Livewire\Profile ......... 100.0% 
 Features/Profile/Services\ProfileService .. 100.0% 
 Features/Stock/Contracts\StockRepository... 100.0% 
 Features/Stock/Enums\StockStatus .......... 100.0% 
 Features/Stock/Livewire\StockDetail ....... 100.0% 
 Features/Stock/Livewire\StockList ......... 100.0% 
 Features/Stock/Models\Stock ............... 100.0% 
 Features/Stock/Policies\StockPolicy ....... 100.0% 
 Features/Stock/Repositories\StockRepository 100.0% 
 Features/Stock/Services\StockExcelService . 100.0% 
 Features/Stock/Services\StockService ...... 100.0% 
 Http/Middleware\EnsureRole ................ 100.0% 
 Http/Middleware\EnsureStockOwnership ...... 100.0% 
 Models\User ............................... 100.0% 
 Providers\AppServiceProvider .............. 100.0% 
 
 Total: 100.0 % (66/66 tests passed)
```

### Audit Keamanan Dependensi
```bash
composer audit
```
Output:
```
No security vulnerability advisories found.
```
