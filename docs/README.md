# Bandar Saham - Dokumentasi

Sistem monitoring saham Indonesia yang mengambil data dari Bursa Efek Indonesia (IDX).

## Daftar Isi

1. [Database Schema](./database-schema.md)
2. [Models](./models.md)
3. [Import Commands](./import-commands.md)
4. [API Reference](./api-reference.md)

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

## Tech Stack

- **Framework**: Laravel 12
- **Frontend**: Livewire + Flux UI
- **Database**: MySQL/MariaDB
- **Data Source**: IDX (idx.co.id)

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

## License

Proprietary - All rights reserved.

