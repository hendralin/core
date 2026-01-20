# IDX Stock Data Scraper

Script untuk mengambil data ringkasan perdagangan saham dari Bursa Efek Indonesia (IDX) dan menyimpannya dalam format JSON.

## Fitur

- Mengambil semua data ringkasan perdagangan saham dari API IDX dalam satu request
- Menyimpan data dalam format JSON dengan nama file berdasarkan tanggal (YYYYMMDD.json)
- Menggunakan cloudscraper untuk bypass Cloudflare protection
- Dapat dijalankan secara manual atau otomatis melalui Laravel scheduler
- Mendukung scraping untuk tanggal tertentu

## Struktur File

```
scripts/scrapers/
├── get-data-harian-emiten.py      # Script asli untuk per-emiten (referensi)
├── idx_bulk_stock_scraper.py      # Script utama dengan cloudscraper
└── README.md                      # Dokumentasi ini
```

## Instalasi Dependencies

```bash
pip install cloudscraper
```

## Penggunaan

### Melalui Laravel Command (Direkomendasikan)
```bash
# Scrape data hari ini
php artisan stock:scrape-idx

# Scrape data untuk tanggal tertentu
php artisan stock:scrape-idx --date=20241101

# Menggunakan Python executable tertentu
php artisan stock:scrape-idx --python=python3
```

### Melalui Script Python Langsung
```bash
# Scrape data hari ini
python3 scripts/scrapers/idx_bulk_stock_scraper.py

# Scrape data untuk tanggal tertentu
python3 scripts/scrapers/idx_bulk_stock_scraper.py 20241101
```

### Penjadwalan Otomatis

Script ini dijadwalkan untuk berjalan otomatis setiap hari pukul 17:35 melalui Laravel Scheduler.

Untuk menjalankan scheduler Laravel:
```bash
# Pastikan cron job sudah disetup untuk menjalankan scheduler
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Output

Data disimpan dalam direktori `storage/app/trading-data/` dengan format nama file:

- `20241101.json` - Data untuk tanggal 1 November 2024
- `20241102.json` - Data untuk tanggal 2 November 2024

### Format Data JSON

```json
{
  "draw": 0,
  "recordsTotal": 941,
  "recordsFiltered": 941,
  "data": [
    {
      "StockCode": "AALI",
      "Date": "2024-11-01T00:00:00",
      "Previous": 6775.0,
      "OpenPrice": 0.0,
      "High": 6800.0,
      "Low": 6650.0,
      "Close": 6725.0,
      "Change": -50.0,
      "Volume": 820400.0,
      "Value": 5520000000.0,
      "Frequency": 543.0,
      "IndexIndividual": 6725.0,
      "Offer": 6725.0,
      "OfferVolume": 100.0,
      "Bid": 6725.0,
      "BidVolume": 500.0,
      "ListedShares": 1927900000.0,
      "TradebleShares": 1927900000.0,
      "WeightForIndex": 0.085,
      "ForeignSell": 0.0,
      "ForeignBuy": 0.0,
      "DelistingDate": null,
      "NonRegularVolume": 0.0,
      "NonRegularValue": 0.0,
      "NonRegularFrequency": 0.0
    }
  ],
  "scraped_at": "2026-01-20T12:49:00.000000",
  "date_requested": "20241101",
  "scraping_method": "cloudscraper"
}
```

## API IDX

Script menggunakan endpoint IDX yang dilindungi Cloudflare protection:

```
https://www.idx.co.id/primary/TradingSummary/GetStockSummary?length=9999&start=0&date=YYYYMMDD
```

Parameter:
- `length=9999`: Mengambil maksimal 9999 record
- `start=0`: Mulai dari record pertama
- `date=YYYYMMDD`: Tanggal data yang diinginkan

**CloudScraper** digunakan untuk bypass Cloudflare protection dengan mensimulasikan browser Chrome dan mengelola session/cookie dengan benar.

## Troubleshooting

### Error: Python executable not found
Pastikan Python 3 terinstall dan dapat diakses melalui command line:
```bash
python3 --version
```

### Error: Module 'cloudscraper' not found
Install cloudscraper:
```bash
pip install cloudscraper
```

### Error: File not found
Pastikan script berada di lokasi yang benar:
```bash
ls -la scripts/scrapers/idx_bulk_stock_scraper.py
```

### Error: Permission denied
Pastikan script memiliki permission execute:
```bash
chmod +x scripts/scrapers/idx_bulk_stock_scraper.py
```

### Error: Network timeout
Cek koneksi internet dan pastikan dapat mengakses website IDX.

### Error: No data received
- Pastikan tanggal yang diminta valid (hari kerja IDX)
- Coba tanggal yang berbeda
- Periksa log untuk detail error lebih lanjut

## Import Data ke Database

Setelah data JSON tersimpan, Anda dapat mengimportnya ke database menggunakan command Laravel yang sudah ada:

```bash
# Import file JSON tertentu
php artisan stock:import-trading-json storage/app/trading-data/20241101.json

# Import dengan opsi tambahan (truncate table dan update existing records)
php artisan stock:import-trading-json storage/app/trading-data/20241101.json --truncate --update

# Import dengan batch size tertentu
php artisan stock:import-trading-json storage/app/trading-data/20241101.json --batch-size=500
```

## Log dan Monitoring

- Output dari script akan ditampilkan di console Laravel
- Untuk monitoring otomatis, lihat log Laravel di `storage/logs/`
- Data yang berhasil di-scrape akan tersimpan di `storage/app/trading-data/`
- Script akan mencatat metadata seperti waktu scraping dan method yang digunakan
