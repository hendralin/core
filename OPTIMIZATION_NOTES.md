# Optimasi Pengiriman Pesan - Queue System

## Ringkasan Perubahan

Implementasi sistem queue untuk pengiriman pesan WhatsApp guna meningkatkan efisiensi dan mencegah antrian yang menumpuk.

## Perubahan yang Dilakukan

### 1. **Job Class untuk Async Processing** (`app/Jobs/SendMessageJob.php`)
   - Menggunakan Laravel Queue untuk pengiriman pesan secara asynchronous
   - Retry mechanism: 3 kali percobaan dengan backoff (10s, 30s, 60s)
   - Timeout: 2 menit per job
   - Queue dedicated: `messages`
   - Auto-update status: `pending` → `sent` / `failed`

### 2. **Optimasi WahaService** (`app/Services/WahaService.php`)
   - **Rate Limiting**: Kontrol jumlah pesan per detik (default: 5 pesan/detik)
   - **Optimasi Delay**: Mengurangi delay typing indicator dari 1s menjadi 0.5s
   - **Bulk Sending**: Disable typing indicator untuk bulk sending (optional)
   - **Timeout**: Menambahkan timeout 30 detik untuk mencegah hanging
   - **Logging**: Optimasi logging untuk mengurangi overhead

### 3. **Update MessagesIndex** (`app/Livewire/Broadcast/Messages/MesssagesIndex.php`)
   - **Queue Integration**: Semua pesan diantrikan dengan status `pending`
   - **Non-blocking**: Request tidak menunggu pengiriman selesai
   - **Status Tracking**: Status otomatis diupdate oleh job (`pending` → `sent` / `failed`)
   - **Resend Support**: Fungsi resend juga menggunakan queue

## Manfaat

1. **Efisiensi**: Request tidak timeout meskipun mengirim ratusan pesan
2. **Rate Limiting**: Mencegah overload WAHA API
3. **Retry Mechanism**: Auto-retry untuk pesan yang gagal
4. **Status Tracking**: Status pesan selalu terupdate (`pending`, `sent`, `failed`)
5. **Scalability**: Dapat menangani volume besar tanpa blocking

## Cara Menggunakan

### 1. Setup Queue Worker
Pastikan queue worker berjalan:
```bash
php artisan queue:work --queue=messages
```

Atau untuk development:
```bash
php artisan queue:listen --queue=messages
```

```bash
php artisan queue:work --queue=messages --tries=3 --timeout=120
```

### 2. Konfigurasi Queue
Pastikan `QUEUE_CONNECTION` di `.env` sudah diset:
```env
QUEUE_CONNECTION=database
```

### 3. Monitor Status Pesan
- Status `pending`: Pesan sedang dalam antrian
- Status `sent`: Pesan berhasil dikirim
- Status `failed`: Pesan gagal setelah 3x retry

## Status Pesan

- **pending**: Pesan sedang dalam antrian, menunggu diproses
- **sent**: Pesan berhasil dikirim via WAHA API
- **failed**: Pesan gagal setelah semua retry attempts

## Rate Limiting

Default: **5 pesan per detik** per session. Dapat disesuaikan di:
- `WahaService::sendBulkText()` parameter `$rateLimitPerSecond`

## Retry Mechanism

- **Maksimal 3x percobaan**
- **Backoff**: 10 detik, 30 detik, 60 detik
- **Auto-update status**: Setelah retry terakhir gagal, status diupdate ke `failed`

## Monitoring

### Check Queue Status
```bash
php artisan queue:work --queue=messages --verbose
```

### Check Failed Jobs
```bash
php artisan queue:failed
```

### Retry Failed Jobs
```bash
php artisan queue:retry all
```

## Catatan Penting

1. **Queue Worker Harus Berjalan**: Tanpa queue worker, pesan tidak akan terkirim
2. **Database Queue**: Menggunakan database sebagai queue driver (default)
3. **Status Update**: Status diupdate secara otomatis oleh job, tidak perlu manual
4. **Typing Indicator**: Untuk bulk sending, typing indicator di-disable untuk performa

## Troubleshooting

### Pesan tetap `pending`
- Pastikan queue worker berjalan
- Check log: `storage/logs/laravel.log`
- Check failed jobs: `php artisan queue:failed`

### Pesan langsung `failed`
- Check koneksi WAHA API
- Check log untuk detail error
- Pastikan session ID valid

### Queue tidak diproses
- Restart queue worker
- Check database connection
- Verify `QUEUE_CONNECTION` setting

