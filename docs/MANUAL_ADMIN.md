# Manual Administrator — Broadcast

Dokumen ini untuk **tim IT / administrator** yang men-deploy server, mengoperasikan antrian dan jadwal, mengelola pengguna & peran, serta backup. Pengguna akhir yang hanya mengoperasikan fitur broadcast harus merujuk ke [MANUAL_OPERATOR.md](MANUAL_OPERATOR.md).

---

## Daftar isi

1. [Ringkasan peran admin](#1-ringkasan-peran-admin)
2. [Prasyarat infrastruktur](#2-prasyarat-infrastruktur)
3. [Instalasi aplikasi](#3-instalasi-aplikasi)
4. [Konfigurasi WAHA di lingkungan](#4-konfigurasi-waha-di-lingkungan)
5. [Queue worker](#5-queue-worker)
6. [Laravel Scheduler dan cron](#6-laravel-scheduler-dan-cron)
7. [Pengguna, peran, dan izin](#7-pengguna-peran-dan-izin)
8. [Backup dan restore](#8-backup-dan-restore)
9. [Lisensi](#9-lisensi)
10. [Pemantauan dan troubleshooting server](#10-pemantauan-dan-troubleshooting-server)
11. [FAQ administrator](#11-faq-administrator)
12. [Referensi](#12-referensi)

---

## 1. Ringkasan peran admin

Administrator bertanggung jawab atas:

- Menyiapkan **server aplikasi** (PHP, database, web server), **build aset**, dan file **`.env`**.
- Memastikan **WAHA** dapat dijangkau dari host Laravel (jaringan, TLS).
- Menjalankan **queue worker** (antrian `messages`) agar pesan tidak tertahan **pending**.
- Mengaktifkan **cron** untuk `php artisan schedule:run` agar jadwal otomatis dan perintah terjadwal lain berjalan.
- Mengelola **Users** dan **Roles** serta izin **Spatie Permission**.
- Mengonfigurasi **backup** (dan restore bila diperlukan).
- Menangani isu **lisensi** jika fitur tersebut dipakai.

Konfigurasi URL/API Key WAHA untuk penggunaan aplikasi disimpan **per pengguna di database** (halaman `/waha`); admin tetap harus memastikan infrastruktur dan kebijakan akses aman.

---

## 2. Prasyarat infrastruktur

| Komponen | Keterangan |
|----------|------------|
| **PHP** | 8.2+ |
| **Laravel** | 12 (acuan: `composer.json`; jika README menyebut versi lain, ikuti proyek) |
| **Database** | MySQL, PostgreSQL, atau SQLite |
| **Node.js** | Untuk build front-end (Vite) |
| **WAHA** | Instance terpisah yang dapat diakses dari server aplikasi |
| **Proses latar belakang** | Worker antrian + pemanggilan scheduler setiap menit |

---

## 3. Instalasi aplikasi

1. Clone repositori dan masuk ke folder proyek.
2. `composer install`
3. `npm install`
4. `cp .env.example .env` — sesuaikan `APP_URL`, `APP_TIMEZONE`, koneksi database, dll.
5. `php artisan key:generate`
6. `php artisan migrate` dan bila perlu `php artisan db:seed`
7. `npm run build` (produksi) atau `npm run dev` (pengembangan)
8. Layani aplikasi dengan `php artisan serve` atau Nginx/Apache.

**Keamanan:** Nilai contoh **superadmin** dan **perusahaan** di `.env.example` hanya untuk pengembangan — **ganti di produksi** dan jangan menyebarluaskan kredensial.

---

## 4. Konfigurasi WAHA di lingkungan

- Runtime aplikasi memuat **URL dan API Key WAHA dari database** (`Config` per `user_id`), diisi pengguna lewat **Setup → WAHA Configuration** (`/waha`).
- Pastikan dari server Laravel, endpoint WAHA dapat dijangkau (DNS, firewall, sertifikat HTTPS).
- Dokumentasi README kadang menyebut variabel `WAHA_*` di `.env`; **sumber kebenaran untuk koneksi API di kode** adalah konfigurasi per user di DB (lihat `WahaService` / `HasWahaConfig`).

---

## 5. Queue worker

Pengiriman pesan memakai antrian Laravel, queue **`messages`**. Tanpa worker, pesan tetap **pending**.

```bash
# Pengembangan
php artisan queue:listen --queue=messages

# Produksi (contoh)
php artisan queue:work --queue=messages --tries=3 --timeout=120
```

Pantau kegagalan: `php artisan queue:failed`; retry sesuai kebutuhan.

---

## 6. Laravel Scheduler dan cron

Di `routes/console.php` terdaftar antara lain `schedule:process` (setiap menit) untuk jadwal pesan, serta perintah terkait lisensi.

Pasang **satu entri cron** (Linux/macOS):

```bash
* * * * * cd /path/ke/proyek && php artisan schedule:run >> /dev/null 2>&1
```

Di **Windows**, gunakan Task Scheduler dengan pemanggilan setara `php artisan schedule:run`.

Detail: [SCHEDULE_USAGE.md](SCHEDULE_USAGE.md).

---

## 7. Pengguna, peran, dan izin

- Menu **Access Control**: **Users** (`/users`), **Roles** (`/roles`).
- Izin didefinisikan di seeder (mis. `PermissionSeeder`): `company.*`, `waha.*`, `session.*`, `contact.*`, `group.*`, `template.*`, `message.*`, `schedule.*`, `backup-restore.*`, dll.

**Selisih penting:** Route halaman **Messages** memakai middleware `message.view|message.send|message.audit`, sedangkan seed default memuat `message.create` (bukan `message.send`). Jika menu tidak muncul atau akses ditolak, **tambahkan izin `message.send`** ke tabel `permissions` dan peran yang sesuai, atau selaraskan route dengan nama izin di seed.

---

## 8. Backup dan restore

- **Tool → Backup and Restore** (`/backup-restore`) — bergantung pada Spatie Laravel Backup dan konfigurasi disk.
- Izin contoh: `backup-restore.view`, `backup-restore.create`, `backup-restore.download`, `backup-restore.restore`, `backup-restore.delete`.
- Jadwal backup otomatis dapat diatur dari UI (sesuai fitur yang tersedia).

---

## 9. Lisensi

Jika lisensi perusahaan kedaluwarsa, pengguna dapat diarahkan ke **`/license-expired`**. Kebijakan perpanjangan di luar cakupan teknis dokumen ini.

---

## 10. Pemantauan dan troubleshooting server

| Gejala | Tindakan admin |
|--------|----------------|
| Banyak pesan **pending** | Pastikan worker queue `messages` berjalan; periksa log dan `queue:failed`. |
| Jadwal tidak jalan | Pastikan cron `schedule:run` setiap menit; pastikan worker antrian aktif. |
| Error koneksi WAHA dari aplikasi | Uji konektivitas dari host Laravel ke URL WAHA; periksa TLS dan API key (diisi user di `/waha`). |
| Beban tinggi | Sesuaikan jumlah worker, timeout, dan infrastruktur WAHA. |

Untuk gejala di sisi **pengguna** (format file, template), lihat [MANUAL_OPERATOR.md](MANUAL_OPERATOR.md).

---

## 11. FAQ administrator

**T: Versi Laravel di README beda dengan `composer.json`?**  
J: Ikuti **`composer.json`** (proyek ini Laravel 12).

**T: Apakah cukup set `WAHA_*` di `.env`?**  
J: Aplikasi utama memuat kredensial dari **database per user**; pastikan pengguna dengan hak **WAHA** mengisi `/waha`.

**T: Bagaimana memberi akses menu Messages?**  
J: Pastikan peran memiliki salah satu dari `message.view`, `message.send`, atau `message.audit` sesuai route; tambahkan `message.send` jika belum ada di seed.

---

## 12. Referensi

| Dokumen | Isi |
|---------|-----|
| [README.md](../README.md) | Instalasi panjang, contoh environment |
| [SCHEDULE_USAGE.md](SCHEDULE_USAGE.md) | Scheduler, `schedule:process`, cron |
| [API_DOCUMENTATION.md](../API_DOCUMENTATION.md) | API HTTP untuk integrasi sistem |
| [MANUAL_OPERATOR.md](MANUAL_OPERATOR.md) | Panduan pengguna akhir |

---

*Dokumen ini disusun dari struktur kode dan dokumentasi di repositori. Sesuaikan dengan kebijakan TI organisasi Anda.*
