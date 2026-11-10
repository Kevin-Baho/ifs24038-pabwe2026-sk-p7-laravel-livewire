# StockPro — Sistem Manajemen Stok & Inventaris Barang
> **Repository:** `ifs24038-pabwe2026-sk-p7-laravel-livewire`  
> **Username / Identitas:** `ifs24038`  
> **Mata Kuliah:** Pengembangan Aplikasi Berbasis Web (PABWE) 2026 — Studi Kasus P7

Sistem manajemen inventaris berbasis web modern yang dibangun dengan arsitektur **Feature-First**, mengadopsi tema gelap elegan (**Sleek Linear/Vercel Dark Theme**), ditenagai oleh **Laravel 13**, **Livewire 4**, **MySQL**, **PhpSpreadsheet**, **OpenAI**, dan **Tailwind CSS**.

---

## 🚀 Fitur Unggulan

### 1. 🎨 Sleek Dark Theme (Linear / Vercel Style)
- **Deep Slate/Zinc Dark Palette**: Latar belakang `bg-slate-950` dengan kontainer kartu `bg-slate-900/90` dan border `border-slate-800`.
- **Aksen Fungsional Dinamis**:
  - Tersedia: `bg-emerald-950/60 text-emerald-400 border-emerald-800/60`
  - Stok Menipis: `bg-amber-950/60 text-amber-400 border-amber-800/60`
  - Habis: `bg-rose-950/60 text-rose-400 border-rose-800/60`
- **Monospace Typography**: SKU, kuantitas, harga, dan timestamp menggunakan font mono/tabular-nums presisi.
- **Toast UI Editor & Viewer (Dark Mode)**: Editor Markdown dengan integrasi skema warna gelap untuk deskripsi spesifikasi produk.

### 2. 📦 Manajemen Stok & Inventaris (Member)
- **Dark Metric Dashboard**: Total Macam Barang, Total Fisik Unit, Estimasi Valuasi Aset (Rp), dan Peringatan Stok Menipis.
- **Pencarian Realtime**: Pencarian debounce 300ms dan filter status stok.
- **Tombol Cepat Kuantitas**: Penyesuaian kuantitas realtime (+1 dan -1) langsung pada tabel.
- **Detail Produk**: Hero cover image, kartu spesifikasi, stepper stok besar, Toast UI Viewer, dan manajemen cover.
- **Import & Export Spreadsheet (PhpSpreadsheet)**: Export rapi dengan header gelap (`#0F172A`) dan import otomatis batch.
- **AI Stock Generator (OpenAI)**: Generator spesifikasi barang berbasis prompt pengadaan dengan checklist batch save.

### 3. 👥 Area Administrator
- **Daftar Pengguna**: Ringkasan pengguna, email, role badge, dan total inventaris.
- **Read-Only Stock Viewer**: Admin dapat melihat detail inventaris pengguna lain secara aman tanpa izin mutasi (403 forbidden).
- **Export Excel Pengguna**: Export data stok spesifik milik pengguna tertentu.

### 4. 👤 Profil Pengguna & Cropper.js
- Ubah nama, username, email, dan kata sandi.
- Upload foto profil dengan Cropper.js rasio 1:1, diproses di backend menggunakan ekstensi **GD** menjadi 400x400 PNG di tengah.

---

## 🏗️ Struktur Arsitektur Feature-First

```
app/Features/
├── Admin/
│   ├── Livewire/AdminUserStockList.php
│   ├── Livewire/UserList.php
│   └── Services/AdminUserService.php
├── Ai/
│   ├── Contracts/AiClientInterface.php
│   ├── DTO/GeneratedStockItem.php
│   └── Services/OpenAiStockGeneratorService.php
├── Auth/
│   ├── Actions/LoginUserAction.php
│   ├── Actions/RegisterUserAction.php
│   ├── Livewire/Login.php
│   └── Livewire/Register.php
├── Profile/
│   ├── Livewire/Profile.php
│   └── Services/ProfileService.php
└── Stock/
    ├── Contracts/StockRepositoryInterface.php
    ├── Enums/StockStatus.php
    ├── Livewire/StockDetail.php
    ├── Livewire/StockList.php
    ├── Models/Stock.php
    ├── Policies/StockPolicy.php
    ├── Repositories/StockRepository.php
    ├── Services/StockExcelService.php
    └── Services/StockService.php
```

---

## 🧪 Pengujian Otomatis (100% Code Coverage)

Suite pengujian mencakup 78 unit test dan feature test dengan **100% Code Coverage**:

```bash
# Menjalankan seluruh pengujian
php vendor/phpunit/phpunit/phpunit

# Menjalankan dengan laporan clover coverage
php vendor/phpunit/phpunit/phpunit --coverage-clover clover.xml
```

---

## ⚙️ CI/CD (Jenkinsfile)
Alur pipeline otomatis terintegrasi:
1. **Checkout** sumber kode
2. **Composer Install & Audit** (`composer audit --no-interaction`)
3. **Frontend Build** (`npm ci && npm run build`)
4. **PHPUnit Test & Enforcing 100% Clover Coverage**
5. **Trivy Security Scan**
6. **Package Application** (`latest-app.zip`)
7. **Publish & Deploy**
