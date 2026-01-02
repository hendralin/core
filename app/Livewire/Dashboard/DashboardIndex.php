<?php

namespace App\Livewire\Dashboard;

use App\Models\News;
use App\Models\User;
use Livewire\Component;
use App\Models\LqCompany;
use App\Models\TradingInfo;
use App\Models\StockCompany;
use App\Models\FinancialRatio;
use Livewire\Attributes\Title;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

#[Title('Dashboard')]
class DashboardIndex extends Component
{
    public string $stockCode = '';
    public ?StockCompany $company = null;
    public ?TradingInfo $latestTrading = null;
    public ?FinancialRatio $financialRatio = null;
    public Collection $tradingHistory;
    public Collection $news;
    public bool $showNewsModal = false;
    public ?News $selectedNews = null;
    public bool $needsSetup = false;

    public string $period = '30'; // Days
    public bool $isCustomRange = false; // True when chart has been panned

    // Stock Picker Modal
    public bool $showStockPickerModal = false;
    public string $stockSearch = '';
    public Collection $stockList;
    public int $stockListPage = 1;
    public bool $hasMoreStocks = true;
    public int $stocksPerPage = 30;

    public function openNewsModal(string $itemId): void
    {
        $this->selectedNews = $this->news->firstWhere('item_id', $itemId);
        $this->showNewsModal = true;
    }

    public function closeNewsModal(): void
    {
        $this->showNewsModal = false;
        $this->selectedNews = null;
    }

    public function openStockPickerModal(): void
    {
        $this->stockSearch = '';
        $this->stockListPage = 1;
        $this->hasMoreStocks = true;
        $this->loadStockList(true);
        $this->showStockPickerModal = true;
    }

    public function closeStockPickerModal(): void
    {
        $this->showStockPickerModal = false;
        $this->stockSearch = '';
        $this->stockList = collect();
    }

    public function updatedStockSearch(): void
    {
        $this->stockListPage = 1;
        $this->hasMoreStocks = true;
        $this->loadStockList(true);
    }

    public function loadMoreStocks(): void
    {
        if (!$this->hasMoreStocks) {
            return;
        }

        $this->stockListPage++;
        $this->loadStockList(false);
    }

    private function loadStockList(bool $reset = false): void
    {
        $query = StockCompany::query()
            ->where('efek_emiten_saham', true)
            ->orderBy('kode_emiten');

        if (!empty($this->stockSearch)) {
            $search = $this->stockSearch;
            $query->where(function ($q) use ($search) {
                $q->where('kode_emiten', 'like', '%' . $search . '%')
                  ->orWhere('nama_emiten', 'like', '%' . $search . '%')
                  ->orWhere('sektor', 'like', '%' . $search . '%')
                  ->orWhere('industri', 'like', '%' . $search . '%');
            });
        }

        $offset = ($this->stockListPage - 1) * $this->stocksPerPage;
        $stocks = $query->skip($offset)->take($this->stocksPerPage + 1)->get();

        // Check if there are more stocks
        if ($stocks->count() > $this->stocksPerPage) {
            $this->hasMoreStocks = true;
            $stocks = $stocks->take($this->stocksPerPage);
        } else {
            $this->hasMoreStocks = false;
        }

        if ($reset) {
            $this->stockList = $stocks;
        } else {
            $this->stockList = $this->stockList->merge($stocks);
        }
    }

    public function selectStock(string $kodeEmiten): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Update user's default stock code
        $user->default_kode_emiten = strtoupper($kodeEmiten);
        $user->save();

        // Close modal and reload data
        $this->showStockPickerModal = false;
        $this->stockSearch = '';
        $this->period = '30';
        $this->isCustomRange = false;

        $this->loadData();

        // Dispatch event to reinitialize chart
        $this->dispatch('chart-data-updated',
            period: $this->period,
            chartData: $this->chartData
        );
    }

    public function getIsLq45Property(): bool
    {
        if (!$this->company) {
            return false;
        }

        return LqCompany::where('kode_emiten', $this->company->kode_emiten)->exists();
    }

    public function mount(): void
    {
        $this->stockList = collect();

        /** @var User $user */
        $user = Auth::user();

        if (!$user || !$user->default_kode_emiten) {
            $this->needsSetup = true;
            return;
        }

        $this->loadData();
    }

    public function updatedPeriod(): void
    {
        $this->isCustomRange = false; // Reset custom range when period is selected
        $this->loadTradingHistory();
        $this->dispatch('chart-data-updated',
            period: $this->period,
            chartData: $this->chartData
        );
    }

    public function setCustomRange(): void
    {
        $this->isCustomRange = true;
    }

    private function loadData(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->stockCode = strtoupper($user->default_kode_emiten);

        $this->company = StockCompany::where('kode_emiten', $this->stockCode)->first();

        $this->latestTrading = TradingInfo::where('kode_emiten', $this->stockCode)
            ->orderBy('date', 'desc')
            ->first();

        // Load latest financial ratio data
        $this->financialRatio = FinancialRatio::where('code', $this->stockCode)
            ->orderBy('fs_date', 'desc')
            ->first();

        $this->loadTradingHistory();
        $this->loadNews();
    }

    private function loadTradingHistory(): void
    {
        $query = TradingInfo::where('kode_emiten', $this->stockCode)
            ->orderBy('date', 'desc');

        if ($this->period === 'ytd') {
            // Year to Date: from start of current year
            $startOfYear = now()->startOfYear();
            $query->where('date', '>=', $startOfYear);
        } else {
            $days = (int) $this->period;
            $query->limit($days);
        }

        $this->tradingHistory = $query->get();
    }

    private function loadNews(): void
    {
        $query = News::query();

        // Build comprehensive search conditions
        $query->where(function ($q) {
            // Primary search: stock code
            $q->where('tags', 'like', '%' . $this->stockCode . '%')
              ->orWhere('summary', 'like', '%' . $this->stockCode . '%')
              ->orWhere('contents', 'like', '%' . $this->stockCode . '%');

            // Enhanced company-based keyword search
            if ($this->company && $this->company->nama_emiten) {
                // Search by full company name
                $q->orWhere('title', 'like', '%' . $this->company->nama_emiten . '%')
                  ->orWhere('summary', 'like', '%' . $this->company->nama_emiten . '%')
                  ->orWhere('contents', 'like', '%' . $this->company->nama_emiten . '%');

                // Split company name by words, exclude common corporate terms
                $companyKeywords = $this->getCompanyNameKeywords($this->company->nama_emiten);

                foreach ($companyKeywords as $keyword) {
                    if (strlen($keyword) >= 3) { // Minimum 3 characters for meaningful search
                        $q->orWhere('title', 'like', '%' . $keyword . '%')
                          ->orWhere('summary', 'like', '%' . $keyword . '%')
                          ->orWhere('contents', 'like', '%' . $keyword . '%')
                          ->orWhere('tags', 'like', '%' . $keyword . '%');
                    }
                }

                // Search by sector if available
                if ($this->company->sektor) {
                    $q->orWhere('title', 'like', '%' . $this->company->sektor . '%')
                      ->orWhere('summary', 'like', '%' . $this->company->sektor . '%')
                      ->orWhere('contents', 'like', '%' . $this->company->sektor . '%');
                }

                // Search by industry if available
                if ($this->company->industri) {
                    $q->orWhere('title', 'like', '%' . $this->company->industri . '%')
                      ->orWhere('summary', 'like', '%' . $this->company->industri . '%')
                      ->orWhere('contents', 'like', '%' . $this->company->industri . '%');
                }
            }
        });

        $this->news = $query->orderBy('published_date', 'desc')
                           ->limit(10)
                           ->get();
    }

    /**
     * Extract meaningful keywords from company name for broader search
     */
    private function extractKeywordsFromCompanyName(string $companyName): array
    {
        // Remove common words and extract meaningful parts
        $cleanName = preg_replace('/\b(PT|Persero|Tbk|Tbk\.|Tbk|PT\.|Ltd|Co|Corp|Inc|Plc|BV|GmbH|SA|NV|Sdn|Bhd|Pte|Ltd)\b/i', '', $companyName);

        // Split by spaces, dots, commas, and other separators
        $parts = preg_split('/[\s\.,\-\(\)]+/', $cleanName);

        // Filter out empty strings and very short words
        $keywords = array_filter($parts, function($part) {
            return !empty(trim($part)) && strlen(trim($part)) > 2;
        });

        return array_unique($keywords);
    }

    /**
     * Extract keywords from company name specifically for news search (split per word, exclude PT/Tbk)
     */
    private function getCompanyNameKeywords(string $companyName): array
    {
        // Define common corporate terms to exclude
        $excludeTerms = [
            'PT', 'Tbk', 'Persero', 'Ltd', 'Co', 'Corp', 'Inc', 'Plc', 'BV', 'GmbH',
            'SA', 'NV', 'Sdn', 'Bhd', 'Pte', 'Ltd', 'Corporation', 'Company', 'Limited',
            'Incorporated', 'Berhad', 'Sendirian', 'Maju', 'Utama', 'Indonesia', 'Indo',
            'Asia', 'Pacific', 'International', 'Global', 'Holding', 'Group', 'Jaya',
            'Makmur', 'Prima', 'Sakti', 'Abadi', 'Sejahtera', 'Sentosa', 'Sukses'
        ];

        // Clean the company name by removing common corporate prefixes/suffixes
        $cleanName = preg_replace('/\b(' . implode('|', $excludeTerms) . ')\b/i', '', $companyName);

        // Split by spaces, dots, commas, hyphens, and parentheses
        $words = preg_split('/[\s\.,\-\(\)\&]+/', $cleanName);

        // Filter out empty strings, very short words, and excluded terms
        $keywords = array_filter($words, function($word) use ($excludeTerms) {
            $word = trim($word);
            return !empty($word) &&
                   strlen($word) >= 3 && // Minimum 3 characters
                   !in_array(strtolower($word), array_map('strtolower', $excludeTerms));
        });

        // Remove duplicates and return
        return array_unique(array_values($keywords));
    }

    /**
     * Load more historical data before the given date (for chart infinite scroll)
     * Updates both chart and Trading History table
     */
    public function loadMoreData(string $beforeDate, int $limit = 30): void
    {
        $moreData = TradingInfo::where('kode_emiten', $this->stockCode)
            ->where('date', '<', $beforeDate)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();

        if ($moreData->isEmpty()) {
            $this->dispatch('no-more-data');
            return;
        }

        // Merge with existing tradingHistory for table display
        $this->tradingHistory = $this->tradingHistory
            ->merge($moreData)
            ->unique('date')
            ->sortByDesc('date')
            ->values();

        // Format data for chart
        $newData = $moreData->sortBy('date')->values();

        $candlestick = $newData->map(fn($item) => [
            'time' => $item->date->format('Y-m-d'),
            'open' => (float) $item->open_price,
            'high' => (float) $item->high,
            'low' => (float) $item->low,
            'close' => (float) $item->close,
        ])->toArray();

        $volume = $newData->map(fn($item) => [
            'time' => $item->date->format('Y-m-d'),
            'value' => (float) $item->volume,
            'color' => $item->change >= 0 ? 'rgba(38, 166, 154, 0.7)' : 'rgba(239, 83, 80, 0.7)',
        ])->toArray();

        $this->dispatch('more-data-loaded', [
            'candlestick' => $candlestick,
            'volume' => $volume,
            'direction' => 'before',
        ]);
    }

    public function getChangePercentProperty(): float
    {
        if (!$this->latestTrading || $this->latestTrading->previous == 0) {
            return 0;
        }

        /** @var float $change */
        $change = $this->latestTrading->change;
        /** @var float $previous */
        $previous = $this->latestTrading->previous;

        return ($change / $previous) * 100;
    }

    public function getNetForeignProperty(): float
    {
        if (!$this->latestTrading) {
            return 0;
        }

        return (float) ($this->latestTrading->foreign_buy - $this->latestTrading->foreign_sell);
    }

    public function getHighestPriceProperty(): float
    {
        return $this->tradingHistory->max('high') ?? 0;
    }

    public function getLowestPriceProperty(): float
    {
        return $this->tradingHistory->min('low') ?? 0;
    }

    public function getAvgVolumeProperty(): float
    {
        return $this->tradingHistory->avg('volume') ?? 0;
    }

    public function getAvgValueProperty(): float
    {
        return $this->tradingHistory->avg('value') ?? 0;
    }

    public function getTotalVolumeProperty(): float
    {
        return $this->tradingHistory->sum('volume') ?? 0;
    }

    public function getTotalValueProperty(): float
    {
        return $this->tradingHistory->sum('value') ?? 0;
    }

    public function getChartDataProperty(): array
    {
        $data = $this->tradingHistory->sortBy('date')->values();

        // Format for TradingView Lightweight Charts
        $candlestick = $data->map(fn($item) => [
            'time' => $item->date->format('Y-m-d'),
            'open' => (float) $item->open_price,
            'high' => (float) $item->high,
            'low' => (float) $item->low,
            'close' => (float) $item->close,
        ])->toArray();

        $volume = $data->map(fn($item) => [
            'time' => $item->date->format('Y-m-d'),
            'value' => (float) $item->volume,
            'color' => $item->change >= 0 ? 'rgba(38, 166, 154, 0.7)' : 'rgba(239, 83, 80, 0.7)',
        ])->toArray();

        return [
            'candlestick' => $candlestick,
            'volume' => $volume,
        ];
    }

    public function getPeriodLabelProperty(): string
    {
        return match ($this->period) {
            '7' => '1W',
            '14' => '2W',
            '30' => '1M',
            '60' => '2M',
            '90' => '3M',
            'ytd' => 'YTD',
            '365' => '1Y',
            '1095' => '3Y',
            '1825' => '5Y',
            default => $this->period . 'D',
        };
    }

    public function render()
    {
        if ($this->needsSetup) {
            return redirect()->route('settings.profile')->with('error', 'Please set your default stock code in your profile settings.');
        }

        return view('livewire.dashboard.dashboard-index');
    }
}
