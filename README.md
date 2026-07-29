# 🍱 Bleatz — Aplikasi Kantin Online UBL

Sistem pemesanan makanan & minuman online untuk lingkungan Kampus.

---

## ✨ Fitur Utama

Bleatz mendukung 3 peran (role) pengguna dengan hak akses masing-masing:

### 👤 Pembeli (Buyer)
- Jelajahi daftar kantin & menu makanan/minuman
- Filter menu berdasarkan kategori & pencarian
- Tambahkan menu ke **favorit**
- Keranjang belanja dengan dukungan **multi-kantin** (satu checkout → transaksi terpisah per kantin)
- Kustomisasi topping per item
- Checkout & pembayaran via **Midtrans Sandbox** (QRIS, VA, E-Wallet, dll.)
- Pantau status pesanan secara real-time
- Ajukan pembatalan pesanan
- Beri **ulasan & rating** untuk pesanan yang sudah selesai
- Lihat riwayat transaksi & statistik pengeluaran

### 🏪 Penjual (Seller)
- Kelola data kantin (nama, deskripsi, jam operasional/hari buka)
- CRUD menu beserta status ketersediaan
- CRUD topping master & assign topping ke menu
- Terima / tolak pesanan masuk
- Update status pemrosesan: `processing → ready → done`
- Approve / reject permintaan pembatalan dari pembeli
- Laporan keuangan per kantin + **export CSV**
- Notifikasi real-time untuk pesanan baru & perubahan status

### 🛠️ Manager
- **Manajemen User**: lihat daftar pengguna, promote buyer → seller, aktif/nonaktifkan akun, reset password
- **Manajemen Kantin**: CRUD kantin + assign penjual ke kantin
- Monitoring semua menu & topping yang terdaftar di sistem
- Laporan keseluruhan sistem + export CSV

---

## 🛠️ Tech Stack

| Kategori | Teknologi |
|---|---|
| **Backend** | Laravel 13 (PHP 8.3), Blade Template |
| **Frontend** | PostCSS, Vite, Custom CSS Neobrutalist, Alpine.js |
| **Database** | SQLite (default) / MySQL |
| **Auth** | Laravel Breeze (session-based) |
| **Payment Gateway** | Midtrans PHP SDK (Sandbox) |
| **Testing** | Pest PHP |
| **Dev Tools** | Laravel Pail, Laravel Pint, Laravel PAO |

---

## 🚀 Instalasi & Setup

### Prasyarat
- PHP 8.3+
- Composer
- Node.js 20+ & NPM
- Ekstensi PHP: `mbstring`, `openssl`, `pdo`, `sqlite3` (atau `pdo_mysql`)

### Langkah-langkah

1. **Clone repository**
   ```bash
   git clone <repo-url>
   cd bleatz
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   > Secara default DB pakai **SQLite** (`database/database.sqlite`). Jika mau pakai MySQL, ubah bagian `DB_CONNECTION` di `.env`.

4. **Jalankan migrasi & seeder**
   ```bash
   # Jalankan semua migrasi
   php artisan migrate

   # (Opsional) Seeder data dummy kantin, menu, topping
   php artisan db:seed --class=LandingPageSeeder

   # (Opsional) Buat akun test untuk 3 role
   php artisan db:seed --class=TestAccountSeeder
   ```

5. **Build asset frontend**
   ```bash
   # Development mode (hot reload)
   npm run dev

   # Production build
   npm run build
   ```

6. **Jalankan server**
   ```bash
   # Opsi 1: server bawaan
   php artisan serve

   # Opsi 2: pakai composer script (server + vite + queue + logs sekaligus)
   composer run dev
   ```

   Akses app di [http://localhost:8000](http://localhost:8000)

---

## 🔑 Akun Test

Setelah menjalankan `TestAccountSeeder`, kamu bisa login dengan akun berikut:

| Role | Email | Username | Password |
|---|---|---|---|
| **Manager** | `manager@bleatz.test` | `manager_test` | `password123` |
| **Seller** | `seller@bleatz.test` | `seller_test` | `password123` |
| **Buyer** | `buyer@bleatz.test` | `buyer_test` | `password123` |

---

## 💳 Konfigurasi Midtrans (Sandbox)

1. Daftar akun di [Midtrans Sandbox](https://dashboard.sandbox.midtrans.com/)
2. Copy **Server Key** & **Client Key** dari menu `Settings → Access Keys`
3. Paste ke file `.env`:

   ```env
   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxx
   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxx
   MIDTRANS_IS_PRODUCTION=false
   ```

4. **Simulasi pembayaran**:
   - Pilih metode apapun di Midtrans Snap
   - Gunakan [simulator Midtrans](https://simulator.sandbox.midtrans.com/) untuk menyelesaikan transaksi
   - Status akan otomatis di-sync via polling (durasi: 5 menit)

5. **Webhook (Opsional untuk testing lokal)**:
   - Endpoint webhook: `POST /api/webhook/midtrans`
   - Untuk expose local server, gunakan **ngrok**:
     ```bash
     ngrok http 8000
     ```
   - Masukkan URL ngrok + `/api/webhook/midtrans` ke dashboard Midtrans (`Settings → Configuration`)

---

## 📂 Struktur Folder Penting

```
bleatz/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Buyer/        # Logic buat pembeli (cart, transaksi, review)
│   │   │   ├── Seller/       # Logic dashboard penjual
│   │   │   ├── Manager/      # Logic dashboard manager
│   │   │   ├── Api/          # API status transaksi (polling)
│   │   │   └── Webhook/      # Handler webhook Midtrans
│   │   └── Middleware/
│   │       └── RoleMiddleware.php   # Custom middleware role:manager / role:seller
│   ├── Models/               # 15+ model (User, Canteen, Menu, Transaction, dll.)
│   ├── Notifications/        # Notifikasi perubahan status via database
│   └── Policies/
│       └── TransactionPolicy.php  # Policy otorisasi transaksi & review
├── database/
│   ├── migrations/           # 20+ tabel
│   └── seeders/              # LandingPageSeeder, TestAccountSeeder
├── resources/
│   ├── css/
│   │   ├── components/       # Styling modular per komponen
│   │   │   ├── landingpage/  # UI untuk pembeli (style Neobrutalist)
│   │   │   └── admin/        # UI dashboard manager/seller
│   │   └── pages/
│   └── views/
│       ├── components/       # Reusable Blade components
│       └── pages/
│           ├── landingpage/  # Halaman buyer
│           └── admin/        # Halaman manager & seller
└── routes/
    └── web.php               # Semua route aplikasi
```

---

## 🧪 Testing

Pake Pest PHP untuk testing:
```bash
composer run test
# atau
php artisan test
```

---

## 📝 Catatan Penting

- **Checkout multi-kantin**: Item keranjang dikelompokkan otomatis berdasarkan `canteen_id` → menghasilkan transaksi terpisah per kantin dalam sekali checkout.
- **Ulasan**: Hanya bisa diberikan sekali per transaksi dengan status `done`.
- **Pemisahan pesanan**: `Pesanan Aktif` (pending, accepted, paid, processing, ready) dipisah dari `Riwayat Transaksi` (done, cancelled, rejected).
- **Race condition pembayaran**: Frontend melakukan polling status ke backend selama 5 menit untuk antisipasi keterlambatan settlement Midtrans.
- **Design System**: Konsisten pakai border hitam tebal (2.5px-3px), shadow keras (5px-8px), aksen warna teal/cyan, & font monospace untuk label teknis.

---

## 📜 License

MIT License — untuk keperluan tugas akademik.
