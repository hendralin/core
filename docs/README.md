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
```

## Tech Stack

- **Framework**: Laravel 12
- **Frontend**: Livewire + Flux UI
- **Database**: MySQL/MariaDB
- **Data Source**: IDX (idx.co.id)

## Struktur Folder Data

```
storage/app/data/
├── stock_companies.json      # Data perusahaan tercatat
├── company_details.json      # Detail perusahaan (direksi, komisaris, dll)
├── financial_ratios.json     # Rasio keuangan
├── stock_summary/            # Data trading harian (JSON)
|   ├── 20251223.json
|   ├── 20251224.json
└── trading/                  # Data trading harian (CSV per saham)
    ├── AADI.csv
    ├── AALI.csv
    ├── BBCA.csv
    └── ...
```

## License

Proprietary - All rights reserved.

