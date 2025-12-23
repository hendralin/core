# Dokumentasi Penggunaan Artisan Schedule - Fitur Schedules

## Daftar Isi

1. [Pengenalan](#pengenalan)
2. [Setup dan Konfigurasi](#setup-dan-konfigurasi)
3. [Command yang Tersedia](#command-yang-tersedia)
4. [Konfigurasi Cron Job](#konfigurasi-cron-job)
5. [Cara Kerja](#cara-kerja)
6. [Timezone Handling](#timezone-handling)
7. [Troubleshooting](#troubleshooting)
8. [Best Practices](#best-practices)

---

## Pengenalan

Fitur **Schedules** memungkinkan Anda untuk menjadwalkan pengiriman pesan WhatsApp secara otomatis pada waktu yang telah ditentukan. Sistem ini menggunakan Laravel Scheduler untuk memproses jadwal pengiriman pesan secara berkala.

### Fitur Utama

- ✅ Pengiriman pesan terjadwal (Daily, Weekly, Monthly)
- ✅ Dukungan timezone per user
- ✅ Pengiriman ke Contact, Group, atau Phone Number
- ✅ Formatting pesan WhatsApp (*bold*, _italic_)
- ✅ Audit trail lengkap
- ✅ Status tracking (Active/Inactive)
- ✅ Usage count tracking

---

## Setup dan Konfigurasi

### 1. Pastikan Queue Worker Berjalan

Fitur Schedules menggunakan Laravel Queue untuk mengirim pesan secara asynchronous. Pastikan queue worker berjalan:

```bash
# Development
php artisan queue:work

# Production (dengan supervisor atau systemd)
php artisan queue:work --daemon
```

### 2. Verifikasi Konfigurasi Scheduler

Scheduler sudah dikonfigurasi di `routes/console.php`:

```php
// Process scheduled messages every minute
Schedule::command('schedule:process')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
```

Command ini akan berjalan setiap menit untuk memproses jadwal yang sudah waktunya dikirim.

---

## Command yang Tersedia

### 1. Memproses Semua Schedule

Memproses semua schedule aktif yang sudah waktunya dikirim:

```bash
php artisan schedule:process
```

**Output contoh:**
```
Processing schedule: Daily Morning Reminder
✓ Message queued for schedule 'Daily Morning Reminder' (Message ID: 123)
Processing schedule: Weekly Report
✓ Message queued for schedule 'Weekly Report' (Message ID: 124)
Successfully processed 2 schedule(s).
```

### 2. Memproses Schedule Spesifik

Memproses schedule tertentu berdasarkan ID:

```bash
php artisan schedule:process --schedule=1
```

**Output contoh:**
```
Processing schedule: Daily Morning Reminder
✓ Message queued for schedule 'Daily Morning Reminder' (Message ID: 123)
```

### 3. Menjalankan Laravel Scheduler

Untuk menjalankan semua scheduled tasks (termasuk `schedule:process`):

```bash
php artisan schedule:run
```

**Catatan:** Command ini biasanya dipanggil oleh cron job setiap menit.

### 4. Melihat Daftar Scheduled Tasks

Untuk melihat semua scheduled tasks yang terdaftar:

```bash
php artisan schedule:list
```

**Output contoh:**
```
+------------------+------------------+------------------+
| Command          | Interval         | Description      |
+------------------+------------------+------------------+
| schedule:process | Every minute     | Process scheduled|
|                  |                  | messages         |
+------------------+------------------+------------------+
```

### 5. Test Scheduler (Tanpa Menjalankan)

Untuk melihat apa yang akan dijalankan tanpa benar-benar menjalankannya:

```bash
php artisan schedule:test
```

---

## Konfigurasi Cron Job

### Linux/Unix/macOS

Tambahkan entry berikut ke crontab server Anda:

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

**Cara menambahkan:**

1. Buka crontab editor:
   ```bash
   crontab -e
   ```

2. Tambahkan baris di atas (sesuaikan path project Anda)

3. Simpan dan keluar

**Verifikasi crontab:**
```bash
crontab -l
```

### Windows (Task Scheduler)

Untuk Windows, gunakan Task Scheduler:

1. Buka **Task Scheduler** (taskschd.msc)

2. Create Basic Task:
   - **Name:** Laravel Scheduler
   - **Trigger:** Daily (atau sesuai kebutuhan)
   - **Action:** Start a program
   - **Program/script:** `C:\path\to\php.exe`
   - **Add arguments:** `artisan schedule:run`
   - **Start in:** `D:\project\broadcast` (sesuaikan dengan path project)

3. Atau gunakan command line:
   ```cmd
   schtasks /create /tn "Laravel Scheduler" /tr "php D:\project\broadcast\artisan schedule:run" /sc minute /mo 1
   ```

### Docker

Jika menggunakan Docker, tambahkan ke `docker-compose.yml`:

```yaml
services:
  scheduler:
    image: your-app-image
    command: php artisan schedule:work
    volumes:
      - ./:/var/www/html
```

Atau gunakan cron di dalam container:

```dockerfile
# Di Dockerfile
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" | crontab -
```

---

## Cara Kerja

### Flow Proses

1. **Cron Job** memanggil `php artisan schedule:run` setiap menit
2. **Laravel Scheduler** menjalankan `schedule:process` command
3. **ProcessScheduledMessages** command mencari semua schedule aktif yang `next_run <= now()`
4. Untuk setiap schedule yang ditemukan:
   - Memvalidasi schedule (is_active, next_run, session)
   - Menentukan recipient (group_wa_id, received_number, atau wa_id)
   - Membuat record di tabel `messages` dengan status `pending`
   - Dispatch `SendMessageJob` ke queue
   - Update `last_run`, `next_run`, dan `usage_count`
5. **Queue Worker** memproses `SendMessageJob` dan mengirim pesan via WAHA API

### Database Schema

Schedule menggunakan kolom berikut untuk tracking:

- `is_active`: Status aktif/nonaktif schedule
- `next_run`: Waktu eksekusi berikutnya (UTC)
- `last_run`: Waktu eksekusi terakhir (UTC)
- `usage_count`: Jumlah kali schedule telah dijalankan

### Frequency Types

#### Daily
- Mengirim pesan setiap hari pada waktu yang ditentukan
- Contoh: Setiap hari pukul 09:00

#### Weekly
- Mengirim pesan sekali seminggu pada hari tertentu
- Memerlukan `day_of_week` (0=Sunday, 1=Monday, ..., 6=Saturday)
- Contoh: Setiap Senin pukul 10:00

#### Monthly
- Mengirim pesan sekali sebulan pada tanggal tertentu
- Memerlukan `day_of_month` (1-28)
- Contoh: Tanggal 1 setiap bulan pukul 08:00

---

## Timezone Handling

### Konsep Timezone

Sistem menggunakan timezone per user untuk perhitungan waktu pengiriman:

1. **User Timezone**: Diambil dari `users.timezone` (default: UTC)
2. **Calculation**: Perhitungan `next_run` dilakukan dalam timezone user
3. **Storage**: `next_run` disimpan dalam UTC di database
4. **Display**: Waktu ditampilkan kembali ke timezone user di UI

### Contoh

User dengan timezone `Asia/Jakarta` (UTC+7):
- Schedule: Daily at 09:00 (timezone user)
- `next_run` di database: 02:00 UTC (09:00 - 7 jam)
- Saat ditampilkan: 09:00 (Asia/Jakarta)

### Setting User Timezone

Timezone user dapat diatur melalui:
- Profile settings di aplikasi
- Database langsung: `UPDATE users SET timezone = 'Asia/Jakarta' WHERE id = 1;`

---

## Troubleshooting

### 1. Schedule Tidak Berjalan

**Gejala:** Schedule tidak mengirim pesan pada waktu yang ditentukan.

**Penyebab & Solusi:**

- **Cron job tidak berjalan**
  ```bash
  # Cek crontab
  crontab -l
  
  # Test manual
  php artisan schedule:run
  ```

- **Schedule tidak aktif**
  ```bash
  # Cek di database
  SELECT id, name, is_active, next_run FROM schedules WHERE id = 1;
  
  # Atau aktifkan via UI
  ```

- **next_run belum tercapai**
  ```bash
  # Cek next_run
  SELECT id, name, next_run, NOW() as current_time FROM schedules;
  
  # Force process schedule tertentu
  php artisan schedule:process --schedule=1
  ```

### 2. Queue Tidak Diproses

**Gejala:** Message status tetap `pending`.

**Solusi:**

```bash
# Pastikan queue worker berjalan
php artisan queue:work

# Cek queue status
php artisan queue:monitor

# Retry failed jobs
php artisan queue:retry all
```

### 3. Timezone Tidak Sesuai

**Gejala:** Pesan dikirim pada waktu yang salah.

**Solusi:**

```bash
# Cek timezone user
SELECT id, name, timezone FROM users WHERE id = 1;

# Update timezone
UPDATE users SET timezone = 'Asia/Jakarta' WHERE id = 1;

# Recalculate next_run untuk semua schedule user
php artisan tinker
>>> $user = User::find(1);
>>> $user->schedules()->each(function($schedule) {
...     $schedule->update(['next_run' => $schedule->calculateNextRun()]);
... });
```

### 4. Session Tidak Valid

**Gejala:** Error "Session not found" atau "Invalid session".

**Solusi:**

```bash
# Cek session status
SELECT id, name, session_id, status FROM waha_sessions;

# Pastikan session aktif dan terhubung ke WAHA
```

### 5. Logging dan Debugging

**Aktifkan logging:**

Logs tersimpan di `storage/logs/laravel.log`. Untuk debugging:

```bash
# Monitor log real-time
tail -f storage/logs/laravel.log

# Filter log schedule
grep "Scheduled message" storage/logs/laravel.log
```

**Test manual:**

```bash
# Test process schedule tertentu
php artisan schedule:process --schedule=1 -v

# Test tanpa benar-benar mengirim
# (Tambahkan --dry-run flag jika tersedia)
```

---

## Best Practices

### 1. Monitoring

- **Setup monitoring** untuk cron job dan queue worker
- **Alert** jika schedule tidak berjalan lebih dari X jam
- **Dashboard** untuk melihat status semua schedules

### 2. Performance

- **Limit concurrent schedules** jika banyak schedule berjalan bersamaan
- **Use queue** untuk menghindari timeout
- **Index database** pada kolom `next_run` dan `is_active`

### 3. Reliability

- **Enable `withoutOverlapping()`** untuk mencegah duplicate execution
- **Use transactions** saat membuat message record
- **Retry mechanism** untuk failed jobs

### 4. Security

- **Validate recipient** sebelum membuat schedule
- **Rate limiting** untuk mencegah spam
- **Permission check** untuk akses schedule

### 5. Maintenance

- **Regular cleanup** untuk schedule yang sudah tidak aktif
- **Archive old schedules** setelah periode tertentu
- **Backup database** secara berkala

### 6. Testing

```bash
# Test schedule creation
php artisan tinker
>>> $schedule = Schedule::create([...]);
>>> $schedule->calculateNextRun();

# Test schedule processing
php artisan schedule:process --schedule=1

# Test timezone conversion
php artisan tinker
>>> $user = User::find(1);
>>> $user->timezone;
>>> Carbon::now($user->timezone);
```

---

## Contoh Penggunaan

### Membuat Schedule via Tinker

```php
php artisan tinker

// Create daily schedule
$schedule = Schedule::create([
    'waha_session_id' => 1,
    'name' => 'Daily Reminder',
    'description' => 'Daily morning reminder',
    'message' => 'Good morning! *Have a great day!*',
    'wa_id' => '6281234567890@s.whatsapp.net',
    'received_number' => '6281234567890',
    'frequency' => 'daily',
    'time' => '09:00',
    'is_active' => true,
    'created_by' => 1,
]);

// Calculate next run
$schedule->next_run = $schedule->calculateNextRun();
$schedule->save();
```

### Monitoring Schedule Status

```sql
-- Cek semua schedule aktif
SELECT 
    id,
    name,
    frequency,
    time,
    is_active,
    last_run,
    next_run,
    usage_count,
    TIMESTAMPDIFF(MINUTE, NOW(), next_run) as minutes_until_next
FROM schedules
WHERE is_active = 1
ORDER BY next_run;
```

### Manual Trigger Schedule

```bash
# Process semua schedule yang ready
php artisan schedule:process

# Process schedule tertentu
php artisan schedule:process --schedule=1
```

---

## Referensi

- [Laravel Scheduling Documentation](https://laravel.com/docs/scheduling)
- [Laravel Queue Documentation](https://laravel.com/docs/queues)
- [Carbon Timezone Documentation](https://carbon.nesbot.com/docs/#api-timezone)

---

## Support

Jika mengalami masalah, silakan:
1. Cek log di `storage/logs/laravel.log`
2. Verifikasi cron job berjalan
3. Pastikan queue worker aktif
4. Cek status schedule di database
5. Hubungi tim development untuk bantuan lebih lanjut

---

**Last Updated:** {{ date('Y-m-d') }}

