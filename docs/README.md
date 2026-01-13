# Bandar Saham - Dokumentasi

Sistem monitoring saham Indonesia yang mengambil data dari Bursa Efek Indonesia (IDX).

## Daftar Isi

1. [Database Schema](./database-schema.md)
2. [Models](./models.md)
3. [Import Commands](./import-commands.md)
4. [Real-time Stock Price Snapshot](#real-time-stock-price-snapshot)
5. [API Reference](./api-reference.md)

## Quick Start

### Instalasi

```bash
# Clone repository
git clone <repository-url>
cd bandar-saham

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate

# Build assets
npm run build
```

### Import Data

```bash
# 1. Import data perusahaan dari JSON
php artisan stock:import-companies path/to/data/stock_companies.json

# 2. Import detail perusahaan (direksi, komisaris, dll)
php artisan stock:import-details path/to/data/company_details.json

# 3. Import rasio keuangan
php artisan stock:import-ratios path/to/data/financial_ratios.json

# 4. Import data trading harian (CSV per saham)
php artisan stock:import-trading path/to/data/trading/

# 5. Import data trading harian (JSON semua saham)
php artisan stock:import-trading-json path/to/data/trading_summary.json

# 6. Import berita IDX (basic + detailed)
php artisan idx:import-news

# 7. Import detail berita IDX (konten lengkap)
php artisan idx:import-news-details
```

### Import News IDX

```bash
# Import berita IDX lengkap (menggabungkan basic + detailed)
php artisan idx:import-news --truncate

# Import berita IDX dengan update data yang sudah ada
php artisan idx:import-news --update

# Import detail berita IDX saja (untuk update konten)
php artisan idx:import-news-details --force

# Import dengan file path custom
php artisan idx:import-news --basic-file=data/news/basic.json --detailed-file=data/news/detailed.json
```

**Catatan:**
- Command `idx:import-news` akan menggabungkan data dari kedua file JSON
- Command `idx:import-news-details` hanya untuk update konten detail berita
- Gunakan `--truncate` untuk menghapus data lama sebelum import
- Gunakan `--update` untuk update data yang sudah ada
- Gunakan `--force` untuk skip konfirmasi

## Real-time Stock Price Snapshot

Sistem ini dilengkapi dengan fitur real-time stock price snapshot yang mengambil data langsung dari GOAPI untuk memberikan informasi harga saham terkini.

### Fitur Utama

- **Real-time Data**: Mengambil data harga saham terakhir dari GOAPI setiap 2 menit selama jam trading
- **Market Hours Logic**: Mengikuti jadwal Bursa Efek Indonesia (IDX):
  - **Sesi I**: Senin-Kamis (09:00 - 12:00), Jumat (09:00 - 11:30)
  - **Sesi II**: Senin-Kamis (13:30 - 15:49:59), Jumat (14:00 - 15:49:59)
  - **Break Time**: Senin-Kamis (12:00 - 13:30), Jumat (11:30 - 14:00)
- **Market Status Badge**: LIVE (saat trading aktif), BREAK (istirahat antar sesi), CLOSED (pasar tutup), WEEKEND
- **Quick Stats Bar**: Menampilkan OHLCV (Open, High, Low, Close, Volume), Value, Frequency, dan Date
- **Trading History**: Tabel riwayat trading dengan data terbaru dari API dan database
- **Timezone Support**: Menyesuaikan dengan timezone user

### Konfigurasi Environment

Tambahkan variabel berikut ke file `.env`:

```env
# GOAPI Configuration
GOAPI_BASE_URL=https://api.goapi.io
GOAPI_KEY=your-goapi-key-here
```

### Endpoint API yang Digunakan

```
GET {GOAPI_BASE_URL}/stock/idx/prices?symbols={KODE_EMITEN}
```

**Response Format:**
```json
{
  "status": "success",
  "data": {
    "results": [
      {
        "symbol": "BBCA",
        "company": {
          "name": "Bank Central Asia Tbk.",
          "logo": "https://s3.goapi.io/logo/BBCA.jpg"
        },
        "date": "2026-01-13",
        "open": 10250,
        "high": 10300,
        "low": 10200,
        "close": 10275,
        "volume": 28400000,
        "change": 25,
        "change_pct": 0.24
      }
    ]
  }
}
```

### Behavior Data

| Kondisi | Data Source | Frequency | Status |
|---------|-------------|-----------|--------|
| **Market Hours** | GOAPI + Database | 1 (API) | LIVE |
| **Market Break** | Database (terakhir) | DB value | BREAK |
| **After Hours** | Database (terakhir) | DB value | CLOSED |
| **Weekend** | Database (terakhir) | DB value | WEEKEND |
| **API Error** | Database (fallback) | DB value | CLOSED |

## UI Features

### Dashboard Components

1. **Stock Price Snapshot Card**
   - Menampilkan harga saham terkini dengan OHLCV data
   - Badge status market (LIVE/BREAK/CLOSED/WEEKEND)
   - Auto-refresh setiap 2 menit selama jam trading
   - Overlay informasi saat market tutup/break

2. **Quick Stats Bar**
   - Open, High, Low, Close prices
   - Volume dan Value calculations
   - Frequency counter (1 untuk API, actual untuk DB)
   - Tanggal data terakhir

3. **Trading History Table**
   - Riwayat trading dengan data dari API dan database
   - Badge warna berdasarkan market status
   - Pagination dan sorting
   - Auto-update saat data baru tersedia

4. **Interactive Charts**
   - Candlestick chart dengan volume bars
   - Dynamic colors berdasarkan market status
   - Technical indicators support
   - Responsive design

### Real-time Updates

- **Polling**: `wire:poll.keep-alive` setiap 2 menit selama market hours
- **Event-driven**: Livewire events untuk immediate UI updates
- **Background throttling**: Tetap polling saat tab browser tidak aktif
- **Error handling**: Graceful fallback ke data historical

## Tech Stack

- **Framework**: Laravel 12
- **Frontend**: Livewire + Flux UI + Alpine.js
- **Database**: MySQL/MariaDB
- **Real-time Data**: GOAPI (goapi.io)
- **Historical Data**: IDX (idx.co.id)
- **Charts**: Lightweight Charts
- **UI Components**: Flux UI

## Struktur Folder Data

```
data/
├── news/
│   ├── idx_news_20250101_to_20251230.json              # Data berita IDX (basic)
│   └── idx_news_detailed_20250101_to_20251230.json     # Data berita IDX (detailed)
├── stock_companies.json      # Data perusahaan tercatat
├── company_details.json      # Detail perusahaan (direksi, komisaris, dll)
├── financial_ratios.json     # Rasio keuangan
├── stock_summary/            # Data trading harian (JSON)
│   ├── 20251223.json
│   ├── 20251224.json
└── trading/                  # Data trading harian (CSV per saham)
    ├── AADI.csv
    ├── AALI.csv
    ├── BBCA.csv
    └── ...
```

## Troubleshooting

### Common Issues

#### 1. GOAPI Configuration Missing
```
Error: GOAPI_BASE_URL or GOAPI_KEY not set
```
**Solution:**
```env
GOAPI_BASE_URL=https://api.goapi.io
GOAPI_KEY=your-actual-api-key-here
```

#### 2. Frequency Always Shows "1"
**Problem:** Frequency selalu menampilkan 1 meskipun data dari database
**Solution:** Pastikan property `$isStockPriceFromApi` di `DashboardIndex.php` di-set dengan benar

#### 3. Polling Not Working
**Problem:** Data tidak update otomatis setiap 2 menit
**Solution:**
- Pastikan `wire:poll.keep-alive="refreshStockPrice"` ada di card
- Periksa market hours logic
- Cek browser console untuk error JavaScript

#### 4. Chart Not Updating
**Problem:** Chart tidak update saat stock berubah
**Solution:**
- Pastikan event `chart-data-updated` di-dispatch dengan benar
- Periksa JavaScript event listeners
- Cek data format untuk chart

#### 5. Wrong Market Status
**Problem:** Badge market status tidak sesuai jam aktual
**Solution:**
- Periksa timezone user di database
- Verifikasi market hours logic di `getMarketStatusProperty()`
- Pastikan server time sesuai dengan zona waktu Indonesia

### Debug Mode

Untuk debugging, aktifkan logging di `config/logging.php` dan periksa:

```bash
tail -f storage/logs/laravel.log
```

### Performance Tips

- **API Rate Limiting**: GOAPI memiliki batas request per menit
- **Database Indexing**: Pastikan index pada kolom `kode_emiten` dan `date`
- **Caching**: Implementasi cache untuk data yang jarang berubah
- **Background Jobs**: Pertimbangkan untuk memindahkan polling ke background job

## License

Proprietary - All rights reserved.

