# API Reference

## Overview

Dokumentasi ini menjelaskan cara mengakses data melalui Eloquent Models.

---

## Stock Companies

### Get All Companies

```php
use App\Models\StockCompany;

// Basic
$companies = StockCompany::all();

// With pagination
$companies = StockCompany::paginate(20);

// Active companies only
$companies = StockCompany::active()->paginate(20);
```

### Get Company by Code

```php
$company = StockCompany::where('kode_emiten', 'BBCA')->first();

// Or using route model binding
$company = StockCompany::findByRouteKey('BBCA');
```

### Get Company with Relations

```php
$company = StockCompany::with([
    'directors',
    'commissioners',
    'shareholders' => fn($q) => $q->where('pengendali', true),
    'latestFinancialRatio',
    'latestTradingInfo'
])->where('kode_emiten', 'BBCA')->first();
```

### Filter by Sector

```php
// Companies in Energi sector
$companies = StockCompany::sektor('Energi')->get();

// Companies in specific industry
$companies = StockCompany::industri('Batu Bara')->get();
```

### Filter by Listing Board

```php
// Main board only
$companies = StockCompany::papan('Utama')->get();

// Development board
$companies = StockCompany::papan('Pengembangan')->get();
```

### Search Companies

```php
$search = 'bank';

$companies = StockCompany::where('nama_emiten', 'like', "%{$search}%")
    ->orWhere('kode_emiten', 'like', "%{$search}%")
    ->get();
```

---

## Financial Ratios

### Get Latest Ratios

```php
use App\Models\FinancialRatio;

// Latest ratio for each stock
$ratios = FinancialRatio::select('financial_ratios.*')
    ->whereIn('id', function ($query) {
        $query->selectRaw('MAX(id)')
            ->from('financial_ratios')
            ->groupBy('code');
    })
    ->get();
```

### Get Ratios for Specific Stock

```php
$ratios = FinancialRatio::code('BBCA')
    ->orderBy('fs_date', 'desc')
    ->get();
```

### Filter Sharia Stocks

```php
$shariaStocks = FinancialRatio::sharia()
    ->audited()
    ->latest()
    ->get();
```

### Top Performers

```php
// Top 10 by ROE
$topRoe = FinancialRatio::whereNotNull('roe')
    ->where('roe', '>', 0)
    ->orderByDesc('roe')
    ->limit(10)
    ->get();

// Low PER stocks (undervalued)
$lowPer = FinancialRatio::whereNotNull('per')
    ->where('per', '>', 0)
    ->where('per', '<', 10)
    ->orderBy('per')
    ->limit(10)
    ->get();
```

---

## Trading Info

### Get Latest Trading Data

```php
use App\Models\TradingInfo;

// Latest for all stocks
$latest = TradingInfo::whereDate('date', TradingInfo::max('date'))->get();

// Latest for specific stock
$latest = TradingInfo::code('BBCA')->latest()->first();
```

### Get Trading History

```php
// Last 30 days
$history = TradingInfo::code('BBCA')
    ->where('date', '>=', now()->subDays(30))
    ->orderBy('date')
    ->get();

// Specific date range
$history = TradingInfo::code('BBCA')
    ->dateRange('2024-01-01', '2024-12-31')
    ->orderBy('date')
    ->get();
```

### Top Gainers/Losers

```php
$latestDate = TradingInfo::max('date');

// Top gainers
$gainers = TradingInfo::whereDate('date', $latestDate)
    ->where('change', '>', 0)
    ->orderByDesc('change')
    ->limit(10)
    ->get();

// Top losers
$losers = TradingInfo::whereDate('date', $latestDate)
    ->where('change', '<', 0)
    ->orderBy('change')
    ->limit(10)
    ->get();
```

### Most Active Stocks

```php
$latestDate = TradingInfo::max('date');

// By volume
$mostActive = TradingInfo::whereDate('date', $latestDate)
    ->orderByDesc('volume')
    ->limit(10)
    ->get();

// By value
$mostActiveByValue = TradingInfo::whereDate('date', $latestDate)
    ->orderByDesc('value')
    ->limit(10)
    ->get();
```

### Foreign Flow Analysis

```php
// Net foreign for a stock
$netForeign = TradingInfo::code('BBCA')
    ->dateRange('2024-01-01', '2024-12-31')
    ->selectRaw('SUM(foreign_buy - foreign_sell) as net_foreign')
    ->value('net_foreign');

// Daily foreign flow
$dailyForeign = TradingInfo::code('BBCA')
    ->select('date')
    ->selectRaw('foreign_buy - foreign_sell as net_foreign')
    ->orderBy('date')
    ->get();
```

---

## Shareholders

### Get Major Shareholders

```php
use App\Models\CompanyShareholder;

$majors = CompanyShareholder::where('kode_emiten', 'BBCA')
    ->major()
    ->orderByDesc('persentase')
    ->get();
```

### Get Controlling Shareholders

```php
$controllers = CompanyShareholder::where('kode_emiten', 'BBCA')
    ->pengendali()
    ->get();
```

---

## Dividends

### Get Upcoming Dividends

```php
use App\Models\CompanyDividend;

$upcoming = CompanyDividend::upcoming()
    ->with('stockCompany')
    ->get();
```

### Get Dividend History

```php
$dividends = CompanyDividend::where('kode_emiten', 'BBCA')
    ->orderByDesc('tanggal_pembayaran')
    ->get();

// Calculate dividend yield
$latestDividend = $dividends->first();
$latestPrice = TradingInfo::code('BBCA')->latest()->value('close');
$yield = ($latestDividend->cash_dividen_per_saham / $latestPrice) * 100;
```

---

## Subsidiaries

### Get Active Subsidiaries

```php
use App\Models\CompanySubsidiary;

$subsidiaries = CompanySubsidiary::where('kode_emiten', 'BBCA')
    ->active()
    ->orderByDesc('persentase')
    ->get();
```

### Group by Location

```php
$byLocation = CompanySubsidiary::where('kode_emiten', 'AADI')
    ->active()
    ->selectRaw('lokasi, COUNT(*) as total, SUM(jumlah_aset) as total_aset')
    ->groupBy('lokasi')
    ->get();
```

---

## Bonds

### Get Active Bonds

```php
use App\Models\CompanyBond;

$activeBonds = CompanyBond::active()
    ->with('stockCompany')
    ->orderBy('mature_date')
    ->get();
```

### Upcoming Maturities

```php
$upcoming = CompanyBond::where('mature_date', '>=', now())
    ->where('mature_date', '<=', now()->addMonths(6))
    ->orderBy('mature_date')
    ->get();
```

---

## Aggregate Queries

### Market Statistics

```php
$latestDate = TradingInfo::max('date');

$stats = TradingInfo::whereDate('date', $latestDate)
    ->selectRaw('
        COUNT(*) as total_stocks,
        SUM(volume) as total_volume,
        SUM(value) as total_value,
        SUM(frequency) as total_frequency,
        SUM(foreign_buy) as total_foreign_buy,
        SUM(foreign_sell) as total_foreign_sell
    ')
    ->first();
```

### Sector Performance

```php
$sectorPerformance = TradingInfo::join('stock_companies', 'trading_infos.kode_emiten', '=', 'stock_companies.kode_emiten')
    ->whereDate('date', $latestDate)
    ->selectRaw('
        stock_companies.sektor,
        COUNT(*) as total_stocks,
        AVG(trading_infos.change) as avg_change,
        SUM(trading_infos.value) as total_value,
        SUM(trading_infos.foreign_buy - trading_infos.foreign_sell) as net_foreign
    ')
    ->groupBy('stock_companies.sektor')
    ->orderByDesc('total_value')
    ->get();
```

### Industry Comparison

```php
$industryStats = FinancialRatio::join('stock_companies', 'financial_ratios.code', '=', 'stock_companies.kode_emiten')
    ->selectRaw('
        stock_companies.industri,
        COUNT(*) as total_stocks,
        AVG(financial_ratios.per) as avg_per,
        AVG(financial_ratios.roe) as avg_roe,
        AVG(financial_ratios.de_ratio) as avg_der
    ')
    ->whereNotNull('financial_ratios.per')
    ->groupBy('stock_companies.industri')
    ->having('total_stocks', '>=', 5)
    ->orderByDesc('avg_roe')
    ->get();
```

