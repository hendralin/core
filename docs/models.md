# Models

## Overview

Semua model menggunakan Eloquent ORM Laravel dengan relasi yang sudah didefinisikan.

---

## StockCompany

Model utama untuk perusahaan tercatat.

**File:** `app/Models/StockCompany.php`

### Relasi

```php
// Has Many
$company->secretaries;
$company->directors;
$company->commissioners;
$company->auditCommittees;
$company->shareholders;
$company->subsidiaries;
$company->auditors;
$company->dividends;
$company->bonds;
$company->bondDetails;
$company->financialRatios;
$company->tradingInfos;

// Has One (Latest)
$company->latestFinancialRatio;
$company->latestTradingInfo;
```

### Scopes

```php
// Filter by sector
StockCompany::sektor('Energi')->get();

// Filter by industry
StockCompany::industri('Batu Bara')->get();

// Only stocks (saham)
StockCompany::saham()->get();

// Filter by listing board
StockCompany::papan('Utama')->get();

// Active companies only
StockCompany::active()->get();
```

### Accessors

```php
$company->logo_url;    // Full URL logo dari IDX
$company->website_url; // Full URL website dengan https://
```

### Contoh Penggunaan

```php
// Get company with all relations
$company = StockCompany::with([
    'directors',
    'commissioners',
    'shareholders',
    'latestFinancialRatio'
])->where('kode_emiten', 'BBCA')->first();

// Get all directors
foreach ($company->directors as $director) {
    echo $director->nama . ' - ' . $director->jabatan;
}

// Get controlling shareholders
$controllers = $company->shareholders()
    ->where('pengendali', true)
    ->get();
```

---

## FinancialRatio

Rasio keuangan perusahaan.

**File:** `app/Models/FinancialRatio.php`

### Relasi

```php
$ratio->stockCompany; // belongsTo StockCompany
```

### Scopes

```php
// Filter by stock code
FinancialRatio::code('BBCA')->get();

// Filter by sector
FinancialRatio::sektor('Keuangan')->get();

// Sharia stocks only
FinancialRatio::sharia()->get();

// Audited reports only
FinancialRatio::audited()->get();

// Latest reports
FinancialRatio::latest()->get();
```

### Methods

```php
$ratio->isSharia();   // bool - Is sharia compliant
$ratio->isAudited();  // bool - Is audited

// Accessors
$ratio->audit_label;  // "Audited" or "Unaudited"
$ratio->opini_label;  // "Wajar Tanpa Modifikasi", etc.

// Formatters
$ratio->formatBillion($ratio->assets);  // "89.069,22 B"
$ratio->formatPercent($ratio->roe);     // "47,32%"
$ratio->formatRatio($ratio->per);       // "3,13"
```

---

## TradingInfo

Data trading harian.

**File:** `app/Models/TradingInfo.php`

### Relasi

```php
$trading->stockCompany; // belongsTo StockCompany
```

### Scopes

```php
// Filter by stock code
TradingInfo::code('BBCA')->get();

// Filter by date range
TradingInfo::dateRange('2024-01-01', '2024-12-31')->get();

// Latest first
TradingInfo::latest()->get();
```

### Accessors

```php
$trading->change_percent;           // float - Percentage change
$trading->formatted_change_percent; // "+2,50%" or "-1,25%"
$trading->net_foreign;              // float - Foreign buy - sell
```

### Methods

```php
$trading->isUp();   // bool - Price went up
$trading->isDown(); // bool - Price went down

// Formatters
$trading->formatNumber($trading->volume);    // "13.701.900"
$trading->formatBillion($trading->value);    // "424,67 B"
$trading->formatMillion($trading->volume);   // "13,70 M"
```

### Contoh Penggunaan

```php
// Get latest trading for a stock
$latest = TradingInfo::code('BBCA')
    ->latest()
    ->first();

echo "Close: " . $latest->close;
echo "Change: " . $latest->formatted_change_percent;

// Get trading history for a period
$history = TradingInfo::code('BBCA')
    ->dateRange('2024-01-01', '2024-06-30')
    ->orderBy('date')
    ->get();

// Calculate foreign net flow
$netForeign = TradingInfo::code('BBCA')
    ->dateRange('2024-01-01', '2024-12-31')
    ->selectRaw('SUM(foreign_buy - foreign_sell) as net_foreign')
    ->value('net_foreign');
```

---

## CompanyDirector

Dewan direksi.

**File:** `app/Models/CompanyDirector.php`

### Methods

```php
$director->isDirekturUtama(); // bool - Is main director
```

---

## CompanyCommissioner

Dewan komisaris.

**File:** `app/Models/CompanyCommissioner.php`

### Methods

```php
$commissioner->isKomisarisUtama(); // bool - Is main commissioner
```

---

## CompanyShareholder

Pemegang saham.

**File:** `app/Models/CompanyShareholder.php`

### Scopes

```php
// Major shareholders (>5%)
CompanyShareholder::major()->get();

// Controlling shareholders
CompanyShareholder::pengendali()->get();
```

### Accessors

```php
$shareholder->formatted_jumlah;     // "3.200.142.830"
$shareholder->formatted_persentase; // "41,0965%"
```

---

## CompanySubsidiary

Anak perusahaan.

**File:** `app/Models/CompanySubsidiary.php`

### Scopes

```php
// Active subsidiaries
CompanySubsidiary::active()->get();

// Indonesian subsidiaries
CompanySubsidiary::indonesia()->get();
```

### Methods

```php
$subsidiary->isActive(); // bool
```

### Accessors

```php
$subsidiary->formatted_aset; // "USD 2.408.625K"
```

---

## CompanyDividend

Dividen.

**File:** `app/Models/CompanyDividend.php`

### Scopes

```php
// Upcoming dividends
CompanyDividend::upcoming()->get();

// Past dividends
CompanyDividend::past()->get();
```

### Methods

```php
$dividend->isCashDividend();  // bool
$dividend->isStockDividend(); // bool
```

### Accessors

```php
$dividend->jenis_label;     // "Dividen Tunai" or "Dividen Saham"
$dividend->formatted_dps;   // "IDR 184,00"
$dividend->formatted_total; // "IDR 354.142.653.272"
```

---

## CompanyBond

Obligasi dan sukuk.

**File:** `app/Models/CompanyBond.php`

### Scopes

```php
// Active bonds
CompanyBond::active()->get();

// Matured bonds
CompanyBond::matured()->get();
```

### Methods

```php
$bond->isMatured(); // bool
$bond->isActive();  // bool
```

### Accessors

```php
$bond->formatted_nominal; // "IDR 316.000.000.000"
```

---

## Query Contoh

### Top 10 Saham by Market Cap

```php
$topStocks = TradingInfo::select('kode_emiten')
    ->selectRaw('close * listed_shares as market_cap')
    ->whereDate('date', now())
    ->orderByDesc('market_cap')
    ->limit(10)
    ->get();
```

### Saham dengan ROE Tertinggi

```php
$highRoe = FinancialRatio::with('stockCompany')
    ->whereNotNull('roe')
    ->where('roe', '>', 0)
    ->orderByDesc('roe')
    ->limit(20)
    ->get();
```

### Net Foreign Flow per Sektor

```php
$foreignFlow = TradingInfo::join('stock_companies', 'trading_infos.kode_emiten', '=', 'stock_companies.kode_emiten')
    ->selectRaw('stock_companies.sektor, SUM(foreign_buy - foreign_sell) as net_foreign')
    ->whereDate('date', '>=', now()->subDays(30))
    ->groupBy('stock_companies.sektor')
    ->orderByDesc('net_foreign')
    ->get();
```

