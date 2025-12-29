# Import Commands

## Overview

Sistem Bandar Saham menyediakan beberapa Artisan command untuk mengimport data dari berbagai sumber (JSON dan CSV).

---

## 1. Import Stock Companies

Mengimport data perusahaan tercatat dari file JSON.

```bash
php artisan stock:import-companies {file} [options]
```

### Arguments

| Argument | Deskripsi |
|----------|-----------|
| `file` | Path ke file JSON |

### Options

| Option | Deskripsi |
|--------|-----------|
| `--truncate` | Hapus semua data sebelum import |
| `--update` | Update record yang sudah ada |

### Format JSON

```json
{
  "draw": 0,
  "recordsTotal": 956,
  "recordsFiltered": 956,
  "data": [
    {
      "KodeEmiten": "AADI",
      "NamaEmiten": "PT Adaro Andalan Indonesia Tbk",
      "Alamat": "Cyber 2 Tower...",
      "BAE": "PT. Datindo Entrycom",
      "Sektor": "Energi",
      "SubSektor": "Minyak, Gas & Batu Bara",
      "Industri": "Batu Bara",
      "SubIndustri": "Produksi Batu Bara",
      "PapanPencatatan": "Utama",
      "TanggalPencatatan": "2024-12-05T00:00:00",
      "Email": "corsec@adaroindonesia.com",
      "Telepon": "(021) 2553 3065",
      "Website": "www.adaroindonesia.com",
      "EfekEmiten_Saham": true,
      "EfekEmiten_Obligasi": false,
      "Status": 0,
      "Logo": "/Portals/0/StaticData/ListedCompanies/LogoEmiten/AADI.jpg"
    }
  ]
}
```

### Contoh Penggunaan

```bash
# Import data baru
php artisan stock:import-companies path/to/data/stock_companies.json

# Import dengan update existing
php artisan stock:import-companies path/to/data/stock_companies.json --update

# Import fresh (hapus semua data lama)
php artisan stock:import-companies path/to/data/stock_companies.json --truncate
```

---

## 2. Import Company Details

Mengimport detail perusahaan (direksi, komisaris, pemegang saham, dll).

```bash
php artisan stock:import-details {file} [options]
```

### Options

| Option | Deskripsi |
|--------|-----------|
| `--truncate` | Hapus semua data detail sebelum import |
| `--update` | Hapus dan import ulang per perusahaan |
| `--skip-missing` | Skip perusahaan yang tidak ada di stock_companies |

### Format JSON

```json
{
  "AADI": {
    "Sekretaris": [
      { "Nama": "Ray Aryaputra", "Email": "corsec@adaroindonesia.com" }
    ],
    "Direktur": [
      { "Nama": "Julius Aslan", "Jabatan": "DIREKTUR UTAMA", "Afiliasi": false }
    ],
    "Komisaris": [
      { "Nama": "Budi Bowoleksono", "Jabatan": "KOMISARIS UTAMA", "Independen": true }
    ],
    "KomiteAudit": [
      { "Nama": "Budi Bowoleksono", "Jabatan": "KETUA" }
    ],
    "PemegangSaham": [
      { "Nama": "PT Adaro Strategic Investments", "Jumlah": 3200142830, "Persentase": 41.0965, "Pengendali": true }
    ],
    "AnakPerusahaan": [
      { "Nama": "PT Adaro Indonesia", "BidangUsaha": "Pertambangan", "Lokasi": "Indonesia", "Persentase": 88.47 }
    ],
    "KAP": [
      { "Nama": "Rintis, Jumadi, Rianto & Rekan", "TahunBuku": 2024 }
    ],
    "Dividen": [
      { "Jenis": "dt", "TahunBuku": "2024", "CashDividenPerSaham": 184, "TanggalPembayaran": "2025-05-28T00:00:00" }
    ],
    "BondsAndSukuk": [],
    "IssuedBond": []
  },
  "AALI": { ... }
}
```

### Contoh Penggunaan

```bash
# Import dengan skip missing companies
php artisan stock:import-details path/to/data/company_details.json --skip-missing

# Import dengan update
php artisan stock:import-details path/to/data/company_details.json --update
```

---

## 3. Import Financial Ratios

Mengimport rasio keuangan.

```bash
php artisan stock:import-ratios {file} [options]
```

### Options

| Option | Deskripsi |
|--------|-----------|
| `--truncate` | Hapus semua data sebelum import |
| `--update` | Update record yang sudah ada |

### Format JSON

```json
{
  "totalRecords": 947,
  "data": [
    {
      "code": "AADI",
      "stockName": "PT Adaro Andalan Indonesia Tbk",
      "sharia": "S",
      "fsDate": "2024-06-30",
      "sector": "Energy",
      "subSector": "Oil, Gas & Coal",
      "industry": "Coal",
      "assets": 89069.22,
      "liabilities": 44461.68,
      "equity": 44607.55,
      "sales": 43550.84,
      "eps": 2710.63,
      "per": 3.13,
      "priceBV": 1.48,
      "deRatio": 1,
      "roa": 23.6977,
      "roe": 47.318,
      "npm": 48.4661,
      "audit": "A",
      "opini": "WTM"
    }
  ]
}
```

### Contoh Penggunaan

```bash
php artisan stock:import-ratios path/to/data/financial_ratios.json
php artisan stock:import-ratios path/to/data/financial_ratios.json --update
```

---

## 4. Import Trading Info (CSV)

Mengimport data trading dari file CSV. Satu file per saham.

```bash
php artisan stock:import-trading {path} [options]
```

### Arguments

| Argument | Deskripsi |
|----------|-----------|
| `path` | Path ke file CSV atau folder berisi file CSV |

### Options

| Option | Deskripsi |
|--------|-----------|
| `--truncate` | Hapus semua data sebelum import |
| `--update` | Update record yang sudah ada |
| `--batch-size=1000` | Jumlah record per batch insert |

### Format CSV

Nama file: `{KODE_EMITEN}.csv` (contoh: `AADI.csv`, `BBCA.csv`)

```csv
date,previous,open_price,first_trade,high,low,close,change,volume,value,frequency,index_individual,offer,offer_volume,bid,bid_volume,listed_shares,tradeble_shares,weight_for_index,foreign_sell,foreign_buy,delisting_date,non_regular_volume,non_regular_value,non_regular_frequency
2024-01-02,7150.0,7150.0,7150.0,7200.0,7100.0,7175.0,25.0,1000000.0,7175000000.0,500.0,125.5,7200.0,10000.0,7175.0,5000.0,7786891760.0,7786891760.0,1928813089.0,500000.0,600000.0,,1000.0,7000000.0,5.0
```

### Contoh Penggunaan

```bash
# Import satu file
php artisan stock:import-trading path/to/data/trading/AADI.csv

# Import semua file dalam folder
php artisan stock:import-trading path/to/data/trading/

# Import dengan batch size lebih besar
php artisan stock:import-trading path/to/data/trading/ --batch-size=5000
```

---

## 5. Import Trading Info (JSON)

Mengimport data trading dari file JSON. Satu file berisi semua saham.

```bash
php artisan stock:import-trading-json {file} [options]
```

### Arguments

| Argument | Deskripsi |
|----------|-----------|
| `file` | Path ke file JSON |

### Options

| Option | Deskripsi |
|--------|-----------|
| `--truncate` | Hapus semua data sebelum import |
| `--update` | Update record yang sudah ada |
| `--batch-size=1000` | Jumlah record per batch insert |

### Format JSON

```json
{
  "draw": 0,
  "recordsTotal": 960,
  "recordsFiltered": 960,
  "data": [
    {
      "StockCode": "AADI",
      "Date": "2025-05-09T00:00:00",
      "Previous": 7150.0,
      "OpenPrice": 7150.0,
      "FirstTrade": 7150.0,
      "High": 7150.0,
      "Low": 6950.0,
      "Close": 6975.0,
      "Change": -175.0,
      "Volume": 11195100.0,
      "Value": 78813650000.0,
      "Frequency": 6547.0,
      "IndexIndividual": 125.7,
      "Offer": 7000.0,
      "OfferVolume": 12000.0,
      "Bid": 6975.0,
      "BidVolume": 219600.0,
      "ListedShares": 7786891760.0,
      "TradebleShares": 7786891760.0,
      "WeightForIndex": 1928813089.0,
      "ForeignSell": 4279300.0,
      "ForeignBuy": 1045300.0,
      "DelistingDate": "",
      "NonRegularVolume": 1139.0,
      "NonRegularValue": 7350155.0,
      "NonRegularFrequency": 7.0
    }
  ]
}
```

### Contoh Penggunaan

```bash
php artisan stock:import-trading-json path/to/data/trading_2025-05-09.json
php artisan stock:import-trading-json path/to/data/trading_2025-05-09.json --update
```

---

## Ringkasan Commands

| Command | Format | Input | Deskripsi |
|---------|--------|-------|-----------|
| `stock:import-companies` | JSON | 1 file | Data perusahaan |
| `stock:import-details` | JSON | 1 file | Detail perusahaan (direksi, dll) |
| `stock:import-ratios` | JSON | 1 file | Rasio keuangan |
| `stock:import-trading` | CSV | Folder (1 file/saham) | Data trading harian |
| `stock:import-trading-json` | JSON | 1 file | Data trading harian |

---

## Urutan Import yang Disarankan

```bash
# 1. Import master data perusahaan
php artisan stock:import-companies data/stock_companies.json

# 2. Import detail perusahaan
php artisan stock:import-details data/company_details.json --skip-missing

# 3. Import rasio keuangan
php artisan stock:import-ratios data/financial_ratios.json

# 4. Import data trading
php artisan stock:import-trading-json data/trading_summary.json
# atau
php artisan stock:import-trading data/trading/
```

---

## Troubleshooting

### Error: "Numeric value out of range"

Nilai decimal melebihi batas kolom. Cek migration dan pastikan precision cukup besar.

### Error: "Company not found"

Gunakan option `--skip-missing` atau import data perusahaan terlebih dahulu.

### Import lambat

Gunakan option `--batch-size` dengan nilai lebih besar:

```bash
php artisan stock:import-trading path/to/data/trading/ --batch-size=5000
```

### Memory limit

Tambahkan memory limit di php.ini atau gunakan flag:

```bash
php -d memory_limit=512M artisan stock:import-trading path/to/data/trading/
```

