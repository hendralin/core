# Database Schema

## Overview

Sistem Bandar Saham menggunakan struktur database relasional dengan tabel utama `stock_companies` sebagai master data perusahaan tercatat di BEI.

## Entity Relationship Diagram

```
stock_companies (Master)
    │
    ├── company_secretaries
    ├── company_directors
    ├── company_commissioners
    ├── company_audit_committees
    ├── company_shareholders
    ├── company_subsidiaries
    ├── company_auditors
    ├── company_dividends
    ├── company_bonds
    ├── company_bond_details
    ├── financial_ratios
    └── trading_infos
```

---

## Tabel: `stock_companies`

Master data perusahaan tercatat di Bursa Efek Indonesia.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `source_id` | int | ID dari sumber API |
| `data_id` | int | DataID dari sumber |
| `kode_emiten` | varchar(10) | **Kode saham (UNIQUE)** |
| `nama_emiten` | varchar(255) | Nama perusahaan |
| `alamat` | text | Alamat perusahaan |
| `bae` | varchar(255) | Biro Administrasi Efek |
| `divisi` | varchar(255) | Divisi |
| `kode_divisi` | varchar(255) | Kode divisi |
| `jenis_emiten` | varchar(255) | Jenis emiten |
| `kegiatan_usaha_utama` | text | Kegiatan usaha utama |
| `efek_emiten_eba` | boolean | Flag EBA |
| `efek_emiten_etf` | boolean | Flag ETF |
| `efek_emiten_obligasi` | boolean | Flag Obligasi |
| `efek_emiten_saham` | boolean | Flag Saham |
| `efek_emiten_spei` | boolean | Flag SPEI |
| `sektor` | varchar(255) | Sektor |
| `sub_sektor` | varchar(255) | Sub-sektor |
| `industri` | varchar(255) | Industri |
| `sub_industri` | varchar(255) | Sub-industri |
| `email` | varchar(255) | Email |
| `telepon` | varchar(50) | Telepon |
| `fax` | varchar(50) | Fax |
| `website` | varchar(255) | Website |
| `npkp` | varchar(50) | NPKP |
| `npwp` | varchar(50) | NPWP |
| `papan_pencatatan` | varchar(50) | Papan (Utama/Pengembangan) |
| `tanggal_pencatatan` | date | Tanggal IPO |
| `status` | tinyint | Status (0=Aktif) |
| `logo` | varchar(255) | Path logo |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Indexes:** `kode_emiten` (unique), `sektor`, `sub_sektor`, `industri`, `status`, `tanggal_pencatatan`

---

## Tabel: `company_secretaries`

Sekretaris perusahaan.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `kode_emiten` | varchar(10) | FK ke stock_companies |
| `nama` | varchar(255) | Nama sekretaris |
| `telepon` | varchar(50) | Telepon |
| `email` | varchar(255) | Email |
| `fax` | varchar(50) | Fax |
| `hp` | varchar(50) | HP |
| `website` | varchar(255) | Website |

---

## Tabel: `company_directors`

Dewan direksi.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `kode_emiten` | varchar(10) | FK ke stock_companies |
| `nama` | varchar(255) | Nama direktur |
| `jabatan` | varchar(255) | Jabatan (DIREKTUR UTAMA, DIREKTUR) |
| `afiliasi` | boolean | Status afiliasi |

---

## Tabel: `company_commissioners`

Dewan komisaris.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `kode_emiten` | varchar(10) | FK ke stock_companies |
| `nama` | varchar(255) | Nama komisaris |
| `jabatan` | varchar(255) | Jabatan (KOMISARIS UTAMA, KOMISARIS) |
| `independen` | boolean | Komisaris independen |

---

## Tabel: `company_audit_committees`

Komite audit.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `kode_emiten` | varchar(10) | FK ke stock_companies |
| `nama` | varchar(255) | Nama anggota |
| `jabatan` | varchar(255) | Jabatan (KETUA, ANGGOTA) |

---

## Tabel: `company_shareholders`

Pemegang saham.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `kode_emiten` | varchar(10) | FK ke stock_companies |
| `nama` | varchar(255) | Nama pemegang saham |
| `kategori` | varchar(255) | Kategori (Lebih dari 5%, Komisaris, Direksi, dll) |
| `jumlah` | bigint | Jumlah lembar saham |
| `persentase` | decimal(10,4) | Persentase kepemilikan |
| `pengendali` | boolean | Pemegang saham pengendali |

---

## Tabel: `company_subsidiaries`

Anak perusahaan.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `kode_emiten` | varchar(10) | FK ke stock_companies |
| `nama` | varchar(255) | Nama anak perusahaan |
| `bidang_usaha` | text | Bidang usaha |
| `lokasi` | varchar(255) | Lokasi/Negara |
| `persentase` | decimal(6,2) | Persentase kepemilikan |
| `jumlah_aset` | decimal(20,2) | Jumlah aset |
| `mata_uang` | varchar(10) | Mata uang (USD, IDR) |
| `satuan` | varchar(20) | Satuan (RIBUAN, JUTAAN) |
| `status_operasi` | varchar(50) | Status (Aktif, Tidak aktif) |
| `tahun_komersil` | varchar(10) | Tahun mulai komersial |

---

## Tabel: `company_auditors`

Kantor Akuntan Publik (KAP).

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `kode_emiten` | varchar(10) | FK ke stock_companies |
| `nama` | varchar(255) | Nama KAP |
| `kap` | varchar(255) | Kode KAP |
| `signing_partner` | varchar(255) | Partner penandatangan |
| `tahun_buku` | year | Tahun buku |
| `tanggal_tahun_buku` | date | Tanggal tahun buku |
| `akhir_periode` | date | Akhir periode |
| `tgl_opini` | date | Tanggal opini |

---

## Tabel: `company_dividends`

Dividen.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `kode_emiten` | varchar(10) | FK ke stock_companies |
| `nama` | varchar(255) | Nama perusahaan |
| `jenis` | varchar(10) | Jenis (dt=tunai, ds=saham) |
| `tahun_buku` | varchar(10) | Tahun buku |
| `total_saham_bonus` | bigint | Total saham bonus |
| `cash_dividen_per_saham_mu` | varchar(10) | Mata uang DPS |
| `cash_dividen_per_saham` | decimal(15,2) | Dividen per saham |
| `cash_dividen_total_mu` | varchar(10) | Mata uang total |
| `cash_dividen_total` | decimal(20,2) | Total dividen |
| `tanggal_cum` | date | Tanggal cum |
| `tanggal_ex_reguler_dan_negosiasi` | date | Tanggal ex |
| `tanggal_dps` | date | Tanggal DPS |
| `tanggal_pembayaran` | date | Tanggal pembayaran |
| `rasio1` | int | Rasio 1 (dividen saham) |
| `rasio2` | int | Rasio 2 (dividen saham) |

---

## Tabel: `company_bonds`

Obligasi dan Sukuk.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `source_id` | int | ID dari sumber |
| `kode_emiten` | varchar(10) | FK ke stock_companies |
| `nama_emisi` | varchar(255) | Nama emisi obligasi |
| `isin_code` | varchar(50) | Kode ISIN |
| `listing_date` | date | Tanggal listing |
| `mature_date` | date | Tanggal jatuh tempo |
| `rating` | varchar(20) | Rating (AAA, AA+, dll) |
| `nominal` | decimal(20,2) | Nilai nominal |
| `margin` | varchar(50) | Margin/kupon |
| `wali_amanat` | varchar(255) | Wali amanat |

---

## Tabel: `company_bond_details`

Detail obligasi.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `source_id` | int | ID dari sumber |
| `kode_emiten` | varchar(10) | FK ke stock_companies |
| `nama_seri` | varchar(255) | Nama seri |
| `amortisasi_value` | varchar(255) | Nilai amortisasi |
| `sinking_fund` | varchar(255) | Sinking fund |
| `coupon_detail` | varchar(255) | Detail kupon |
| `coupon_payment_detail` | date | Tanggal pembayaran kupon |
| `mature_date` | date | Tanggal jatuh tempo |

---

## Tabel: `financial_ratios`

Rasio keuangan.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `code` | varchar(10) | Kode saham |
| `stock_name` | varchar(255) | Nama perusahaan |
| `sharia` | varchar(5) | Flag syariah (S) |
| `sector` | varchar(255) | Sektor |
| `sub_sector` | varchar(255) | Sub-sektor |
| `industry` | varchar(255) | Industri |
| `sub_industry` | varchar(255) | Sub-industri |
| `sector_code` | varchar(10) | Kode sektor |
| `sub_sector_code` | varchar(10) | Kode sub-sektor |
| `industry_code` | varchar(10) | Kode industri |
| `sub_industry_code` | varchar(10) | Kode sub-industri |
| `sub_name` | varchar(255) | Nama sub |
| `sub_code` | varchar(10) | Kode sub |
| `fs_date` | date | Tanggal laporan keuangan |
| `fiscal_year_end` | varchar(10) | Akhir tahun fiskal |
| `assets` | decimal(20,2) | Total aset (miliar) |
| `liabilities` | decimal(20,2) | Total liabilitas (miliar) |
| `equity` | decimal(20,2) | Total ekuitas (miliar) |
| `sales` | decimal(20,2) | Penjualan (miliar) |
| `ebt` | decimal(20,2) | Laba sebelum pajak (miliar) |
| `profit_period` | decimal(20,2) | Laba periode (miliar) |
| `profit_attr_owner` | decimal(20,2) | Laba pemilik entitas induk (miliar) |
| `eps` | decimal(15,2) | Laba per saham |
| `book_value` | decimal(15,2) | Nilai buku per saham |
| `per` | decimal(15,4) | Price to Earnings Ratio |
| `price_bv` | decimal(15,4) | Price to Book Value |
| `de_ratio` | decimal(15,4) | Debt to Equity Ratio |
| `roa` | decimal(15,4) | Return on Assets (%) |
| `roe` | decimal(15,4) | Return on Equity (%) |
| `npm` | decimal(15,4) | Net Profit Margin (%) |
| `audit` | varchar(5) | Status audit (A/U) |
| `opini` | varchar(10) | Opini audit (WTM, WTP, dll) |

**Indexes:** `code`, `sector`, `sub_sector`, `industry`, `fs_date`, `sharia`
**Unique:** `code` + `fs_date`

---

## Tabel: `trading_infos`

Data trading harian.

| Kolom | Tipe | Deskripsi |
|-------|------|-----------|
| `id` | bigint | Primary key |
| `kode_emiten` | varchar(10) | Kode saham |
| `date` | date | Tanggal trading |
| `previous` | decimal(15,2) | Harga penutupan sebelumnya |
| `open_price` | decimal(15,2) | Harga pembukaan |
| `first_trade` | decimal(15,2) | Harga transaksi pertama |
| `high` | decimal(15,2) | Harga tertinggi |
| `low` | decimal(15,2) | Harga terendah |
| `close` | decimal(15,2) | Harga penutupan |
| `change` | decimal(15,2) | Perubahan harga |
| `volume` | decimal(20,2) | Volume perdagangan |
| `value` | decimal(20,2) | Nilai perdagangan |
| `frequency` | decimal(15,2) | Frekuensi perdagangan |
| `index_individual` | decimal(15,4) | Indeks individual |
| `offer` | decimal(15,2) | Harga penawaran terbaik |
| `offer_volume` | decimal(20,2) | Volume penawaran |
| `bid` | decimal(15,2) | Harga permintaan terbaik |
| `bid_volume` | decimal(20,2) | Volume permintaan |
| `listed_shares` | decimal(20,2) | Saham tercatat |
| `tradeble_shares` | decimal(20,2) | Saham dapat diperdagangkan |
| `weight_for_index` | decimal(20,2) | Bobot untuk indeks |
| `foreign_sell` | decimal(20,2) | Volume jual asing |
| `foreign_buy` | decimal(20,2) | Volume beli asing |
| `delisting_date` | date | Tanggal delisting |
| `non_regular_volume` | decimal(20,2) | Volume pasar non-reguler |
| `non_regular_value` | decimal(20,2) | Nilai pasar non-reguler |
| `non_regular_frequency` | decimal(15,2) | Frekuensi pasar non-reguler |

**Indexes:** `kode_emiten`, `date`
**Unique:** `kode_emiten` + `date`

---

## Menjalankan Migration

```bash
# Jalankan semua migration
php artisan migrate

# Rollback migration terakhir
php artisan migrate:rollback

# Reset dan jalankan ulang semua migration
php artisan migrate:fresh
```

