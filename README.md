# Web-Apps-POS (VarStore Atelier)

**Web-Apps-POS** adalah aplikasi modern Point of Sale (POS) dan e-commerce fashion boutique yang dibangun menggunakan **Laravel 13**, **Tailwind CSS v4**, dan **Alpine/Vanilla JS**. Dirancang dengan fokus pada efisiensi operasional kasir, integritas data inventaris secara real-time, serta pengalaman belanja pelanggan yang intuitif dan profesional tanpa *AI Slop*.

---

## 🌟 Fitur Utama Sistem

### 1. 🖥️ Terminal Kasir POS Interaktif (`/admin/pos`)
* **Pencarian Cepat & Filter Kategori:** Cari produk berdasarkan nama, kode warna, atau scan barcode dengan respon instan.
* **Shortcut Keyboard Kasir:**
  * `[F2]` : Fokus ke pencarian produk.
  * `[F4]` : Buka modal konfirmasi pembayaran.
  * `[ESC]` : Tutup dialog / batalkan transaksi.
* **Kalkulator Uang & Kembalian Real-time:** Pilihan pembayaran Tunai (*Cash*) dengan tombol pecahan uang cepat (*Uang Pas*, Rp 50.000, Rp 100.000, Rp 200.000, Rp 500.000) dan kalkulasi otomatis kembalian.
* **Multi-Metode Pembayaran:** Mendukung Uang Tunai, QRIS Dinamis Standar Nasional, dan Transfer Bank (BCA & Mandiri).
* **Pengurangan Stok Otomatis:** Pemotongan stok barang secara atomik (*database transaction*) mencegah terjadinya *overselling*.

### 2. 🖨️ Struk Kasir Thermal & Faktur Komersial Resmi
* **Struk Thermal Kasir (58mm / 80mm):** Format struk POS kasir dengan barcode simulation, rincian produk, nomor invoice unik, kasir penanggung jawab, dan ketentuan penukaran barang.
* **Faktur Pajak / Invoice Komersial A4 (`/admin/orders/{id}/invoice`):** Lembar tagihan resmi A4 dengan kop surat butik, tabel rincian transaksi, dan kolom tanda tangan pengesahan.
* **Packing Slip Gudang (`/admin/orders/{id}/packing-slip`):** Lembar verifikasi pengemasan pesanan untuk logistik dan kurir pengiriman.

### 3. 📦 Manajemen Pesanan & Transaksi (`/admin/orders`)
* **Filter Komprehensif:** Filter pesanan berdasarkan status (*Menunggu*, *Diproses*, *Selesai*, *Dibatalkan*) dan kanal transaksi (*🏬 POS Kasir* vs *🌐 Online WA*).
* **Modal Detail Transaksi:** Tampilan pop-up instan berisi rincian item, catatan kasir, alamat pengiriman, dan tautan langsung WhatsApp pelanggan.
* **Pembaruan Status Cepat:** Ubah status pesanan secara langsung melalui tabel administrasi.

### 4. 🏷️ Manajemen Voucher & Kupon Promosi (`/admin/discounts`)
* **Tipe Potongan Fleksibel:** Mendukung persentase (`%`) maupun nominal tetap (`Rp`).
* **Pembatas Transaksi:** Pengaturan batas minimal belanja (*min spend*) dan batas maksimal diskon (*max discount*).
* **Masa Berlaku & Status Aktif:** Jadwal kadaluarsa kupon serta tombol *toggle* aktif/nonaktif dalam 1-klik.

### 5. 👗 Manajemen Katalog & Inventaris (`/admin/products`)
* **Kalkulator Diskon & Harga Jual:** Menghitung potongan dan harga jual akhir secara live saat mengisi formulir.
* **Live Image Preview:** Validasi visual gambar produk secara langsung saat URL dimasukkan.
* **Peringatan Stok Rendah:** Notifikasi visual produk yang stoknya menipis (≤ 5 pcs) untuk mempermudah restock.

### 6. 🔍 Pelacakan Pesanan Pelanggan (`/tracking`)
* Portal publik bagi pembeli untuk melacak status pesanan menggunakan Nomor Invoice (`#POS-XXXX` / `#INV-XXXX`) atau Nomor WhatsApp.
* Dilengkapi *stepper* 3 tahap pemrosesan (*Pesanan Diterima* → *Diproses Kasir* → *Selesai & Dikirim*).

### 7. 🛍️ Storefront Modern & Navigasi Mobile
* Desain butik minimalis dengan responsivitas tinggi pada perangkat *desktop*, *tablet*, maupun *smartphone*.
* **Mobile Bottom Bar:** Navigasi bawah untuk akses cepat ke Beranda, Koleksi, Lacak Pesanan, Tas Belanja, dan Terminal POS.
* **Checkout WhatsApp Concierge:** Sinkronisasi tas belanja langsung ke pesan WhatsApp kurator toko.

---

## 🛠️ Stack Teknologi

* **Backend:** PHP 8.5+ & Laravel 13
* **Frontend:** Blade Templating, Tailwind CSS v4, JavaScript (ES6+)
* **Database:** SQLite (Testing / Dev) & MySQL / MariaDB (Production)
* **Code Formatter:** Laravel Pint (PSR-12 Standard)
* **Testing:** PHPUnit 12

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

* **Administrator / Kasir:**
  * **Email:** `admin@varstore`
  * **Password:** `password`
  * **Akses:** Terminal Kasir POS, Manajemen Produk, Pesanan, dan Voucher Diskon.

* **Pelanggan Demo:**
  * **Email:** `test@example.com`
  * **Password:** `password`
  * **Akses:** Belanja Koleksi, Tas Belanja, dan Pelacakan Pesanan.

---

## 🧪 Menjalankan Pengujian (Tests)

Uji fungsionalitas modul POS, keranjang, otentikasi, dan voucher:

```bash
php artisan test
```

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah lisensi open-source [MIT License](LICENSE).
