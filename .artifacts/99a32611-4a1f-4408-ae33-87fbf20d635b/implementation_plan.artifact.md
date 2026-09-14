# Overhaul Dashboard Admin, Perbaikan Sesi, Hapus Map, dan Optimasi Railway

Proyek ini akan mengalami perbaikan besar-besaran untuk mengatasi bug sesi, performa (dump load), dan penyesuaian fitur sesuai permintaan user (menghapus peta dan optimasi dashboard).

## User Review Required

> [!IMPORTANT]
> **Hapus Maps Firebase:** Berdasarkan permintaan "hapus maps firebase", saya akan menghapus integrasi **Google Maps** di panel admin lokasi, karena Firebase sendiri tidak memiliki fitur Maps yang terintegrasi langsung di Laravel kecuali melalui Google Cloud. Fitur radius lokasi tetap dipertahankan secara logika di backend untuk validasi absensi, namun UI peta akan dihilangkan.

## Open Questions

- Apakah "Hapus Maps Firebase" juga berarti menonaktifkan pengiriman data real-time ke Firestore? Saat ini pengiriman dilakukan secara sinkron yang menyebabkan "dump load". Saya akan mengubahnya menjadi **Asynchronous (Queue)** untuk performa maksimal.

## Proposed Changes

### [Backend] Optimasi Performa & Sesi

#### [MODIFY] [AttendanceValidationService.php](file:///C:/laragon/www/AbsenBarcode-Sekolah12/app/Services/AttendanceValidationService.php)
- Mengubah alur pengiriman ke Firebase menjadi opsional dan lebih efisien.

#### [NEW] [PublishAttendanceToFirebase.php](file:///C:/laragon/www/AbsenBarcode-Sekolah12/app/Jobs/PublishAttendanceToFirebase.php)
- Job baru untuk menangani publikasi ke Firestore secara background (Queue) agar tidak membebani request utama siswa saat absen.

#### [MODIFY] [AdminDashboardController.php](file:///C:/laragon/www/AbsenBarcode-Sekolah12/app/Http/Controllers/AdminDashboardController.php)
- Optimasi query statistik (menggunakan caching singkat atau query tunggal) untuk mencegah server 500 saat data banyak.

#### [MODIFY] [config/session.php](file:///C:/laragon/www/AbsenBarcode-Sekolah12/config/session.php)
- Penyesuaian konfigurasi cookie `secure` dan `same_site` untuk kompatibilitas Railway (HTTPS).

---

### [Admin Dashboard] UI Overhaul

#### [MODIFY] [dashboard.blade.php](file:///C:/laragon/www/AbsenBarcode-Sekolah12/resources/views/admin/dashboard.blade.php)
- Desain ulang dashboard admin agar lebih modern, informatif, dan ringan.

#### [MODIFY] [edit.blade.php](file:///C:/laragon/www/AbsenBarcode-Sekolah12/resources/views/admin/location/edit.blade.php)
- Menghapus Google Maps SDK dan UI Peta. Menggantinya dengan input koordinat manual yang bersih.

---

### [Deployment] Railway & Database Configuration

#### [MODIFY] [.env.example](file:///C:/laragon/www/AbsenBarcode-Sekolah12/.env.example)
- Menambahkan panduan konfigurasi `DATABASE_URL`, `SESSION_DRIVER`, dan `QUEUE_CONNECTION` untuk Railway.

#### [MODIFY] [railway.toml](file:///C:/laragon/www/AbsenBarcode-Sekolah12/railway.toml)
- Optimasi `startCommand` untuk menjalankan queue worker dan memastikan database siap sebelum app jalan.

## Verification Plan

### Automated Tests
- `php artisan test` (jika tersedia).
- Cek log Laravel `storage/logs/laravel.log` untuk memastikan tidak ada Server 500.

### Manual Verification
- Login admin dan pastikan dashboard tampil dengan statistik yang benar.
- Buka pengaturan lokasi dan pastikan peta sudah hilang namun koordinat tetap bisa diubah.
- Test absensi siswa dan pastikan response cepat (karena Firebase sudah di-queue).
- Cek sesi apakah masih sering logout sendiri atau error.
