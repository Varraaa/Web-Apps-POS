# Web-Apps-POS (VarStore Atelier)

**Web-Apps-POS** adalah aplikasi modern Point of Sale (POS) dan e-commerce fashion boutique yang dibangun menggunakan **Laravel 13**, **Tailwind CSS v4**, dan **Vanilla JS**. Dirancang dengan fokus pada efisiensi operasional kasir retail, integritas data inventaris secara real-time, serta pengalaman belanja butik yang profesional dan elegan tanpa *AI Slop*.

---

## 🌟 Fitur Utama Sistem

### 1. 🖥️ Terminal Kasir POS Interaktif & Cepat (`/admin/pos`)
* **Pencarian Cepat & Filter Kategori:** Cari artikel pakaian berdasarkan nama, varian warna, SKU, atau scan barcode instan.
* **Integrasi Hardware Barcode Scanner:** Listener otomatis mendeteksi pemindaian scanner USB/Bluetooth fisik (keystroke buffer `< 75ms` berakhiran Enter), mencocokkan kode EAN/SKU, dan menambahkan item ke tiket kasir.
* **Shortcut Keyboard Kasir:**
  * `[F2]` : Fokus langsung ke pencarian produk / barcode input.
  * `[F4]` : Buka modal konfirmasi pembayaran.
  * `[ESC]` : Tutup dialog pembayaran / struk.
* **Kalkulator Kasir & Kembalian Real-time:** Pilihan pembayaran Tunai (*Cash*) dengan preset pecahan cepat (*Uang Pas*, Rp 50.000, Rp 100.000, Rp 200.000, Rp 500.000) dan kalkulasi otomatis uang kembalian.
* **Multi-Metode Pembayaran:** Tunai (*Cash*), QRIS Dinamis Standar Nasional, dan Transfer Bank.
* **Pengurangan Stok Atomik:** Menggunakan *database transactions* dengan penguncian baris (*pessimistic lock*) untuk mencegah terjadinya *overselling*.

### 2. 👥 CRM Keanggotaan & Poin Loyalitas (`/admin/customers`)
* **Tingkatan Membership (Tiers):** Bronze Member, Silver VIP, dan Gold Royale dengan perolehan poin otomatis (1 poin per pembelanjaan Rp 10.000).
* **Pencarian Member Cepat di POS:** Autocomplete nomor HP/nama member, kartu ringkasan saldo poin di kasir, dan penukaran poin langsung (1 poin = diskon Rp 100).
* **Modal Registrasi Instan:** Daftarkan member baru langsung dari layar terminal kasir tanpa perlu berpindah halaman.

### 3. 🅿️ Parkir Transaksi (Hold & Recall Bills)
* **Tunda Tagihan Pelanggan:** Simpan transaksi berjalan saat pelanggan ingin menambah barang atau ke fitting room tanpa membatalkan antrean.
* **Buka / Hapus Tagihan Kapan Saja:** Daftar tagihan tersimpan dengan catatan referensi, waktu parkir, dan opsi kembalikan ke keranjang aktif dalam 1-klik.

### 4. 💼 Manajemen Shift Kasir & Laporan Tutup Kasir (Z-Report)
* **Buka Shift & Modal Kasir (Float):** Catat uang modal awal di laci kasir saat pergantian shift.
* **Penutupan Shift & Settlement Real-time:** Kasir memasukkan uang fisik di laci dan sistem langsung mengkalkulasi selisih kas (*variance*: pas, lebih, atau kurang).
* **Cetak Struk Z-Report Thermal (80mm):** Rekapitulasi transaksi shift, total omzet tunai vs non-tunai, rincian pembayaran per metode, dan kolom tanda tangan kasir serta supervisor.

### 5. 🏷️ Label Hangtag Pakaian & SKU Barcode (`/admin/products/{id}/barcode-label`)
* Setiap produk dilengkapi kode **SKU** unik (misal `VAR-OUT-K9F2`) dan **Barcode EAN** yang digenerate otomatis.
* **Printable Hangtag Label:** Template label gantung pakaian siap cetak dengan logo butik, nama artikel, varian ukuran/warna, harga normal, harga diskon, dan barcode.

### 6. 📊 Dashboard Analisis & Rekap Finansial POS (`/admin/reports`)
* **Filter Periode Lengkap:** Hari Ini, Kemarin, Minggu Ini, Bulan Ini, dan 30 Hari Terakhir.
* **Kartu Metrik KPI:** Total Omzet Bersih, Total Transaksi Bon, Rata-rata Nilai Belanja (*Average Order Value* / AOV), dan Total Diskon Diberikan.
* **Tabel Insight & Visualisasi:** Produk paling laris (*Top 8 Sellers*), proporsi metode pembayaran (*Cash vs QRIS vs Transfer*), dan sebaran kanal (*POS Kasir vs Web Storefront*).
* **📥 Ekspor Laporan CSV:** Unduh spreadsheet penjualan lengkap format UTF-8 dengan 1-klik.

### 7. 📦 Audit Penyesuaian Stok Fisik / Opname Gudang (`/admin/products`)
* Tombol cepat **"📦 Opname"** pada setiap produk di tabel katalog.
* **Jenis Penyesuaian Inventaris:** Stok Masuk (+), Stok Keluar (-), Barang Cacat/Reject (-), Barang Hilang (-), dan Koreksi Opname Fisik Aktual (=).
* **Log Audit Permanen:** Mencatat waktu, staf penanggung jawab, kuantitas sebelum dan sesudah, serta alasan penyesuaian untuk keperluan akuntansi.

### 8. ⚙️ Pengaturan Butik & Live Thermal Receipt Customizer (`/admin/settings`)
* Kelola identitas brand: Nama Toko, Tagline, Alamat Butik, No. Telepon, WhatsApp Concierge, dan Akun Instagram.
* **Kustomisasi Struk Kasir:** Header sambutan dan ketentuan retur penukaran barang pada struk.
* **Live Interactive Thermal Preview:** Tampilan visual struk thermal 80mm di samping formulir yang berubah secara real-time saat teks diedit.

### 9. 🏷️ Skema Diskon Pembelian Grosir (Wholesale Bundling)
* Pengaturan harga bertingkat: Minimal kuantitas grosir (misal min. 3 pcs) dan harga satuan grosir khusus.
* Otomatis terdeteksi dan dihitung pada keranjang POS kasir maupun tas belanja storefront online saat kuantitas memenuhi syarat.

### 10. 🤍 Wishlist & Simpan Busana Favorit (`/wishlist`)
* Ikon hati interaktif pada kartu katalog storefront dengan animasi live toggle.
* Halaman koleksi tersimpan (*Wishlist*) dengan tombol 1-klik **"+ Pindah ke Tas Belanja"**.

### 11. 🌙 Mode Gelap OLED High-Contrast untuk Kasir
* Palet warna gelap kontras tinggi dirancang khusus untuk kenyamanan mata kasir yang bekerja pada pencahayaan butik temaram.
* Dilengkapi *script anti-flicker* dan toggle instan di bilah atas POS serta menu navigasi sidebar.

### 12. 🔊 Tactile Web Audio API Feedback Synthesizer
* Suara instan terintegrasi berbasis browser Web Audio API murni (tanpa ketergantungan file MP3 eksternal):
  * **Beep (1760Hz):** Indikator scan barcode sukses dan penambahan produk ke tiket.
  * **Chime (Harmonik C6-E6-G6):** Nada konfirmasi transaksi selesai dan laci kasir dibuka.
  * **Warning Buzz:** Peringatan stok habis atau nominal pembayaran tunai kurang.
  * **Pop:** Feedback audio saat menghapus item dari tiket kasir.
  * Tombol **Mute/Unmute** audio langsung pada bilah atas POS.

---

## 🛠️ Stack Teknologi

* **Backend Framework:** PHP 8.5+ & Laravel 13
* **Frontend:** Blade Templating, Tailwind CSS v4, Vanilla JavaScript (ES6+), Web Audio API
* **Database:** SQLite (In-Memory Testing) & MySQL / MariaDB (Production)
* **Code Formatter:** Laravel Pint (PSR-12 Standard)
* **Automated Testing:** PHPUnit 12 (22 feature tests green)

---

## 🚀 Panduan Instalasi Lokal

1. **Clone Repositori:**
   ```bash
   git clone https://github.com/Varraaa/Web-Apps-POS.git
   cd Web-Apps-POS
   ```

2. **Pasang Dependensi PHP & JavaScript:**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Jalankan Migrasi & Seeder Database:**
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi:**
   ```bash
   # Jalankan asset bundling
   npm run build # atau npm run dev

   # Jalankan server lokal Laravel
   php artisan serve
   ```

Aplikasi dapat diakses melalui peramban di `http://localhost:8000`.

---

## 🔑 Kredensial Demo Akun

* **Administrator Studio & Shift Kasir:**
  * **Email:** `admin@varstore.id`
  * **Password:** `password`
  * **Akses:** Terminal Kasir POS, Manajemen Member, Laporan Keuangan, Pengaturan Toko, Opname Stok, Voucher, dan Pesanan.

* **Kasir Shift Pagi:**
  * **Email:** `kasir@varstore.id`
  * **Password:** `password`
  * **Akses:** Terminal Kasir POS, Penjualan, Buka/Tutup Register Kasir.

* **Pelanggan Demo:**
  * **Email:** `test@example.com`
  * **Password:** `password`
  * **Akses:** Belanja Koleksi, Wishlist Favorit, dan Pelacakan Pesanan.

---

## 🧪 Menjalankan Pengujian (Tests)

Uji seluruh fungsionalitas modul POS, CRM membership, shift register, kalkulasi grosir, laporan, dan keranjang:

```bash
php artisan test --compact
```
*(Hasil: 22 tests passed, 80 assertions, 100% green)*

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah lisensi open-source [MIT License](LICENSE).
