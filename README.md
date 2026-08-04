# WO-IT — Work Order Management System

Aplikasi manajemen work order berbasis Laravel, dibangun untuk kebutuhan operasional **Harris Hotel Seminyak**. Karyawan bisa melaporkan kendala (kerusakan, maintenance, dll) lengkap dengan foto, lalu tim engineering mengelola, mengerjakan, dan menutup laporan tersebut lewat panel admin.

## Fitur Utama

- Form pelaporan kendala publik (per departemen, jenis masalah, lokasi, deskripsi, foto)
- Dashboard admin: daftar & detail work order, assign staff, ubah status (Pending → On Progress → Completed)
- Edit detail laporan terpisah dari alur perubahan status (status hanya bisa diubah lewat halaman detail)
- Export PDF per work order (termasuk foto yang diupload pelapor)
- Halaman laporan analisis (total, completion rate, rata-rata waktu penyelesaian, kinerja staff, lokasi paling sering bermasalah) — bisa diexport ke PDF & Excel
- Notifikasi otomatis ke grup Telegram saat ada laporan baru
- Pengaturan master data (departemen, jenis masalah, staff) lewat menu Settings

## Persyaratan Sistem

- PHP 8.2 atau lebih tinggi (dengan ekstensi standar Laravel: `mbstring`, `openssl`, `pdo`, `pdo_sqlite`/`pdo_mysql`, `fileinfo`, `gd`)
- Composer
- Web server lokal — direkomendasikan **Laragon** (Windows), bisa juga XAMPP atau `php artisan serve` langsung
- Database SQLite (default, paling mudah) atau MySQL/MariaDB
- Node.js dan npm (opsional, hanya kalau ingin build ulang asset frontend)

## Instalasi (Urutan Ini Penting)

### 1. Clone repository

```bash
git clone https://github.com/KrishnDwi/wo-it.git
cd wo-it
```

### 2. Install dependensi PHP

```bash
composer install
```

### 3. Salin file environment

```bash
copy .env.example .env
```
*(di Linux/Mac pakai `cp .env.example .env`)*

### 4. Siapkan database

**Opsi A — SQLite (paling simpel, default project ini):**

```bash
# Windows PowerShell tidak punya perintah `touch`, pakai salah satu ini:
New-Item database\database.sqlite -ItemType File
# atau
type nul > database\database.sqlite

# Linux/Mac/Git Bash:
touch database/database.sqlite
```

Pastikan di `.env` sudah:
```ini
DB_CONNECTION=sqlite
```
(baris `DB_DATABASE`, `DB_HOST`, dll boleh tetap ter-comment)

**Opsi B — MySQL/MariaDB:**

Buat database kosong dulu, lalu isi `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=nama_user
DB_PASSWORD=password_anda
```

### 5. Generate application key

```bash
php artisan key:generate
```

### 6. Jalankan migration & seeder

```bash
php artisan migrate
php artisan db:seed
php artisan db:seed --class=OptionsSeeder
```

> ⚠️ **Penting:** `php artisan db:seed` (tanpa `--class`) hanya membuat 1 akun user contoh, **tidak** mengisi data departemen & jenis masalah. Seeder `OptionsSeeder` wajib dijalankan terpisah, kalau tidak, dropdown Departemen/Jenis Masalah di form akan kosong.

### 7. Buat symbolic link storage (WAJIB — untuk foto & PDF)

```bash
php artisan storage:link
```

Tanpa langkah ini, foto yang diupload pelapor **tidak akan tampil** di halaman detail maupun di PDF work order, karena file disimpan di `storage/app/public` dan diakses lewat `public/storage`.

### 8. (Opsional) Konfigurasi notifikasi Telegram

Isi di `.env`:
```ini
TELEGRAM_BOT_TOKEN=token_bot_telegram_anda
TELEGRAM_CHAT_ID=id_grup_telegram_anda
```

> 🔒 **Catatan keamanan:** `.env.example` di repo ini sempat berisi token & chat ID asli. Sebaiknya buat bot token baru lewat [@BotFather](https://t.me/BotFather) dan jangan commit token asli ke repository publik.

### 9. (Opsional) Install & build asset frontend

Hanya perlu kalau ada perubahan di asset JS/CSS sumber (bukan yang di `public/css`, `public/js` langsung):

```bash
npm install
npm run build
```

### 10. Tambah data staff (teknisi)

Belum ada seeder untuk staff. Setelah aplikasi jalan, tambahkan staff lewat menu **Admin → Settings → Staff**, supaya bisa di-assign ke work order.

## Menjalankan Aplikasi

**Cara cepat (built-in server):**

```bash
php artisan serve
```

Buka:
```text
http://127.0.0.1:8000
```

**Pakai Laragon (Windows):**

Taruh folder project di `C:/laragon/www/wo-it`, lalu akses:
```text
http://wo-it.test
```
atau
```text
http://localhost/wo-it/public
```

Jika ingin jalan bareng beberapa app Laravel lain di satu Laragon, atur virtual host / port terpisah lewat konfigurasi Nginx/Apache Laragon per project.

## Akses Aplikasi

| Halaman | URL | Keterangan |
|---|---|---|
| Form Laporan (publik) | `/` | Karyawan lapor kendala |
| Dashboard Admin | `/admin` | Ringkasan & work order terbaru |
| Daftar Work Order | `/admin/orders` | Semua laporan |
| Laporan Analisis | `/admin/report` | Statistik + export PDF/Excel |
| Settings | `/admin/settings` | Kelola departemen, jenis masalah, staff |

> Catatan: saat ini halaman `/admin/*` belum memiliki sistem login. Jika akan dipakai di jaringan yang lebih luas (bukan hanya jaringan hotel lokal), sebaiknya tambahkan middleware auth sebelum deploy ke internet publik.

## Troubleshooting

- **Error `could not find driver` saat migrate:** ekstensi PHP untuk SQLite/MySQL belum aktif. Cek `php.ini`, aktifkan `extension=pdo_sqlite` atau `extension=pdo_mysql`.
- **Foto tidak muncul / broken image:** jalankan ulang `php artisan storage:link`, pastikan folder `storage/app/public` ada dan writable.
- **Dropdown Departemen/Jenis Masalah kosong:** jalankan `php artisan db:seed --class=OptionsSeeder`.
- **Halaman blank / 500 error:** cek permission folder `storage` dan `bootstrap/cache` — harus bisa ditulis oleh web server.
- **Notifikasi Telegram tidak terkirim:** pastikan `TELEGRAM_BOT_TOKEN` dan `TELEGRAM_CHAT_ID` benar, dan bot sudah jadi member di grup Telegram tujuan.
- **Bersihkan cache setelah pindah environment / ubah `.env`:**

  ```bash
  php artisan config:clear
  php artisan cache:clear
  php artisan route:clear
  php artisan view:clear
  ```

## License

Proyek ini dilisensikan di bawah MIT License.
