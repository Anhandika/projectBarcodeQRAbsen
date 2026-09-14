# Absen Digital — SMK BINA UTAMA KENDAL

Aplikasi absensi QR dinamis berbasis Laravel 12 untuk uji coba tiga peran pengguna:

- Admin sekolah
- Guru
- Siswa

Desain antarmuka menggunakan konsep **Digital Plinth**: sidebar gelap, kartu statistik, QR monitor berlapis, scanner mobile, dan panel hasil validasi yang eksplisit.

## Fitur tahap awal

- Login berbasis session Laravel dengan role-aware redirect.
- Dashboard admin sekolah.
- Monitor QR dinamis dengan token aktif 8 detik.
- QR tetap tajam dan diperbarui melalui endpoint server.
- Scanner mobile dengan `html5-qrcode`.
- Pembacaan koordinat melalui Browser Geolocation API.
- Validasi berlapis di server:
  1. Token QR masih aktif.
  2. Koordinat perangkat berada dalam radius sekolah.
  3. Pengguna belum memiliki catatan pada tanggal yang sama.
  4. Catatan kehadiran dibuat secara transaksional.
- Status hasil: berhasil, QR kedaluwarsa, di luar area, dan sudah tercatat.
- Test unit untuk jarak koordinat dan test fitur untuk role serta alur absensi.

## Stack

- PHP 8.3+
- Laravel 12
- PostgreSQL 14+
- Eloquent ORM
- Blade + Alpine.js
- Tailwind CSS 4 + Vite
- `html5-qrcode`

## Persiapan PostgreSQL

Buat database dan user PostgreSQL sebelum menjalankan migrasi:

```sql
CREATE USER absen_app WITH PASSWORD 'ganti-password-kuat';
CREATE DATABASE absen_barcode OWNER absen_app;
```

Sesuaikan `.env` lokal:

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=absen_barcode
DB_USERNAME=absen_app
DB_PASSWORD=ganti-password-kuat
```

Pastikan ekstensi PHP `pdo_pgsql` aktif:

```bash
php -m | grep -E "pgsql|pdo_pgsql"
```

Pada Windows/Laragon, aktifkan `extension=pdo_pgsql` dan `extension=pgsql` pada `php.ini`, lalu restart PHP/web server.

## Menjalankan proyek

```bash
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
```

Untuk build produksi:

```bash
npm run build
php artisan optimize
```

## Akun demo

Semua akun menggunakan kata sandi `password`:

| Peran | Email | Identifier |
| --- | --- | --- |
| Admin sekolah | `adminsekolah@example.test` | `ADM-001` |
| Guru | `guru@example.test` | `GURU-001` |
| Siswa | `siswa@example.test` | `2403107` |

Admin diarahkan ke `/dashboard`. Guru dan siswa diarahkan ke `/scan`.

## Konfigurasi absensi

Nilai default berada di `.env`:

```dotenv
ATTENDANCE_QR_TTL_SECONDS=8
ATTENDANCE_SCHOOL_RADIUS_METERS=80
ATTENDANCE_MAX_ACCURACY_METERS=150
ATTENDANCE_TIMEZONE=Asia/Jakarta
```

Seeder memakai koordinat **demo** untuk SMK BINA UTAMA KENDAL. Ganti nilai `latitude`, `longitude`, dan `radius_meters` pada `DatabaseSeeder` sebelum dipakai di lingkungan sekolah sebenarnya.

## Verifikasi

```bash
composer validate --strict
php artisan route:list
php artisan test
npm run build
```

Migrasi dan test fitur membutuhkan PostgreSQL yang aktif serta database sesuai `.env`.
