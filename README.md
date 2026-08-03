# WO-IT

Aplikasi manajemen work order berbasis Laravel.

## Persyaratan Sistem

- PHP 8.2 atau lebih tinggi
- Composer
- Web server (Apache / Nginx / Laragon)
- Database MySQL / MariaDB / SQLite
- Node.js dan npm (opsional untuk build frontend)

## Instalasi

1. Clone repository:

   ```bash
   git clone https://github.com/KrishnDwi/wo-it.git
   cd wo-it
   ```

2. Install dependensi PHP:

   ```bash
   composer install
   ```

3. Salin file environment:

   ```bash
   copy .env.example .env
   ```

4. Konfigurasikan database di `.env`:

   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_anda
   DB_USERNAME=nama_user
   DB_PASSWORD=password_anda
   ```

   Jika menggunakan SQLite, set `DB_CONNECTION=sqlite` dan buat file `database/database.sqlite`.

5. Generate aplikasi key:

   ```bash
   php artisan key:generate
   ```

6. Jalankan migrasi dan seeder:

   ```bash
   php artisan migrate --seed
   ```

7. (Opsional) Install asset frontend dan build:

   ```bash
   npm install
   npm run build
   ```

## Menjalankan Aplikasi

Jalankan server local:

```bash
php artisan serve
```

Lalu buka:

```text
http://127.0.0.1:8000
```

Jika menggunakan Laragon, taruh proyek di `C:/laragon/www/wo-it` dan akses melalui:

```text
http://localhost/wo-it
```

## Fitur Utama

- Kelola work order
- Assign petugas sebelum accept work order
- Kelola departemen dan jenis masalah
- Filter laporan berdasarkan petugas dan status
- Export laporan CSV dan PDF per work order

## Konfigurasi Tambahan

- Pastikan folder `storage` dan `bootstrap/cache` dapat ditulis.
- Jika ingin menggunakan library PDF, package `barryvdh/laravel-dompdf` sudah tersedia.
- Jika perlu membersihkan cache config/view, jalankan:

  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

## License

Proyek ini dilisensikan di bawah MIT License.
