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
use Illuminate\Support\Facades\Http;

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
    public bool $showCompanyModal = false;
    public bool $needsSetup = false;

    public string $period = '30'; // Days
    public bool $isCustomRange = false; // True when chart has been panned

    // Company related data
    public Collection $directors;
    public Collection $shareholders;
    public Collection $subsidiaries;
    public Collection $dividends;
    public Collection $bonds;

    // Stock Picker Modal
    public bool $showStockPickerModal = false;
    public string $stockSearch = '';
    public Collection $stockList;
    public int $stockListPage = 1;
    public bool $hasMoreStocks = true;
    public int $stocksPerPage = 30;

    // Technical Indicators
    public array $technicalIndicators;

    // Stock Price Snapshot
    public ?array $stockPriceSnapshot = null;
    public ?string $stockPriceLastUpdated = null;
    public bool $isStockPriceFromApi = false;

    // Candlestick Data Timestamp
    public ?string $candlestickLastUpdated = null;

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

    public function openCompanyModal(): void
    {
        $this->showCompanyModal = true;
    }

    public function closeCompanyModal(): void
    {
        $this->showCompanyModal = false;
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

        // Update component stock code immediately
        $this->stockCode = strtoupper($kodeEmiten);

        // Reset API data for new stock
        $this->stockPriceSnapshot = null;
        $this->stockPriceLastUpdated = null;
        $this->isStockPriceFromApi = false;
        $this->candlestickLastUpdated = null;

        // Close modal and reload data
        $this->showStockPickerModal = false;
        $this->stockSearch = '';
        $this->period = '30';
        $this->isCustomRange = false;

        $this->loadData();

        // Dispatch chart update event - Livewire will handle timing
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
        $this->directors = collect();
        $this->shareholders = collect();
        $this->subsidiaries = collect();
        $this->dividends = collect();
        $this->bonds = collect();

        // Initialize stock price snapshot with latest trading data
        $this->stockPriceSnapshot = null;
        $this->stockPriceLastUpdated = null;
        $this->isStockPriceFromApi = false;
        $this->candlestickLastUpdated = null;

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
        $this->calculateTechnicalIndicators(); // Recalculate technical indicators for new period
        $this->dispatch('chart-data-updated',
            period: $this->period,
            chartData: $this->chartData,
            isPeriodChange: true
        );
    }

    public function setCustomRange(): void
    {
        $this->isCustomRange = true;
    }

    public function updateIndicatorsForVisibleRange(): void
    {
        // This method will be called when chart visible range changes
        // Recalculate technical indicators based on current trading history data
        $this->calculateTechnicalIndicators();
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
        $this->calculateTechnicalIndicators();
        $this->fetchStockPriceSnapshot();

        // Load company related data
        if ($this->company) {
            $this->directors = $this->company->directors()->get();
            $this->shareholders = $this->company->shareholders()->orderBy('persentase', 'desc')->get();
            $this->subsidiaries = $this->company->subsidiaries()->get();
            $this->dividends = $this->company->dividends()->orderBy('tanggal_pembayaran', 'desc')->get();
            $this->bonds = $this->company->bonds()->get();
        } else {
            $this->directors = collect();
            $this->shareholders = collect();
            $this->subsidiaries = collect();
            $this->dividends = collect();
            $this->bonds = collect();
        }

        // Initialize stock price snapshot with latest trading data if no API data exists
        if (!$this->stockPriceSnapshot && $this->latestTrading) {
            $this->stockPriceSnapshot = [
                'symbol' => $this->latestTrading->kode_emiten,
                'company' => [
                    'name' => $this->company ? $this->company->nama_emiten : 'Unknown Company',
                    'logo' => $this->company && $this->company->logo_url ? $this->company->logo_url : null
                ],
                'date' => $this->latestTrading->date->format('Y-m-d'),
                'open' => (float) $this->latestTrading->open_price,
                'high' => (float) $this->latestTrading->high,
                'low' => (float) $this->latestTrading->low,
                'close' => (float) $this->latestTrading->close,
                'volume' => (float) $this->latestTrading->volume,
                'change' => (float) $this->latestTrading->change,
                'change_pct' => (float) $this->changePercent
            ];
            $this->isStockPriceFromApi = false; // Mark as from database fallback
        }
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

    private function fetchStockPriceSnapshot(): void
    {
        // Check if current time is within IDX market hours using user's timezone
        /** @var User $user */
        $user = Auth::user();

        if (!$user || !$user->timezone) {
            // Fallback to server time if no user timezone is set
            $now = now();
        } else {
            // Use user's timezone
            $now = now($user->timezone);
        }

        $dayOfWeek = $now->format('N'); // 1=Monday, 7=Sunday
        $currentTime = $now->format('H:i:s');
        $isMarketHours = false;

        // Monday-Thursday (1-4)
        if ($dayOfWeek >= 1 && $dayOfWeek <= 4) {
            // Session I: 09:00:00 - 12:00:00
            // Session II: 13:30:00 - 15:49:59
            $isMarketHours = ($currentTime >= '09:00:00' && $currentTime <= '12:00:00') ||
                           ($currentTime >= '13:30:00' && $currentTime <= '15:49:59');
        }
        // Friday (5)
        elseif ($dayOfWeek == 5) {
            // Session I: 09:00:00 - 11:30:00
            // Session II: 14:00:00 - 15:49:59
            $isMarketHours = ($currentTime >= '09:00:00' && $currentTime <= '11:30:00') ||
                           ($currentTime >= '14:00:00' && $currentTime <= '15:49:59');
        }
        // Saturday-Sunday (6-7) - No trading


        // Always try to fetch data, but only update timestamps during market hours
        if (!$isMarketHours) {
            // Outside market hours - still fetch data but don't update timestamp
            // This ensures we always have the latest data available
            // Keep existing timestamp to show when data was last updated
        }

        try {
            $goapiBaseUrl = env('GOAPI_BASE_URL');
            $goapiKey = env('GOAPI_KEY');

            if (!$goapiBaseUrl || !$goapiKey) {
                $this->stockPriceSnapshot = null;
                $this->stockPriceLastUpdated = null;
                $this->isStockPriceFromApi = false;
                return;
            }

            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(10)->withHeaders([
                'Accept' => 'application/json',
                'X-API-KEY' => $goapiKey,
            ])->get("{$goapiBaseUrl}/stock/idx/prices", [
                'symbols' => $this->stockCode
            ]);

            if ($response->successful()) {
                /** @var array $data */
                $data = $response->json();

                if (isset($data['status']) && $data['status'] === 'success' && isset($data['data']['results'][0])) {
                    $this->stockPriceSnapshot = $data['data']['results'][0];
                    $this->isStockPriceFromApi = true; // Mark as from API

                    // Update candlestick timestamp whenever API data is received
                    /** @var User $user */
                    $user = Auth::user();
                    if ($user && $user->timezone) {
                        $this->candlestickLastUpdated = now($user->timezone)->toDateTimeString();
                    } else {
                        $this->candlestickLastUpdated = now()->toDateTimeString();
                    }

                    // Only update stock price timestamp during market hours
                    if ($isMarketHours) {
                        /** @var User $user */
                        $user = Auth::user();
                        if ($user && $user->timezone) {
                            $this->stockPriceLastUpdated = now($user->timezone)->toDateTimeString();
                        } else {
                            $this->stockPriceLastUpdated = now()->toDateTimeString();
                        }
                    }
                    // If outside market hours, keep existing timestamp to show when data was last updated

                    // Dispatch event to update chart and UI immediately after API success
                    $this->dispatch('stock-price-updated', [
                        'stockCode' => $this->stockCode,
                        'hasNewData' => true
                    ]);

                    // Force re-render of trading history table
                    $this->dispatch('$refresh');
                } else {
                    // Only clear data if API completely fails, not just returns invalid format
                    // This ensures we keep the last successful data
                    if (!$this->stockPriceSnapshot) {
                        $this->stockPriceSnapshot = null;
                        $this->stockPriceLastUpdated = null;
                        $this->isStockPriceFromApi = false;
                    }
                }
            } else {
                logger()->error('GOAPI request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers()
                ]);
                $this->stockPriceSnapshot = null;
                $this->stockPriceLastUpdated = null;
            }
        } catch (\Exception $e) {
            logger()->error('GOAPI request exception', [
                'message' => $e->getMessage(),
                'stock_code' => $this->stockCode
            ]);
            $this->stockPriceSnapshot = null;
            $this->stockPriceLastUpdated = null;
            $this->isStockPriceFromApi = false;
        }
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

        // Recalculate technical indicators with new data
        $this->calculateTechnicalIndicators();

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
        return $this->combinedTradingHistory->avg('volume') ?? 0;
    }

    public function getPreviousCloseProperty(): float
    {
        // If we have API data, get the previous trading day directly from database
        // to avoid dependency on filtered chart data
        if ($this->stockPriceSnapshot && $this->isStockPriceFromApi) {
            $apiDate = \Carbon\Carbon::createFromFormat('Y-m-d', $this->stockPriceSnapshot['date']);

            // Query database directly for previous trading day data
            $previousDayData = TradingInfo::where('kode_emiten', $this->stockCode)
                ->where('date', '<', $apiDate->format('Y-m-d'))
                ->orderBy('date', 'desc')
                ->first();

            if ($previousDayData && isset($previousDayData->close)) {
                return (float) $previousDayData->close;
            }
        }

        // Fallback to database value
        return $this->latestTrading ? (float) $this->latestTrading->previous : 0;
    }

    public function getEstimatedIndexIndividualProperty(): float
    {
        if (!$this->latestTrading || !$this->stockPriceSnapshot || !$this->isStockPriceFromApi) {
            return $this->latestTrading ? (float) $this->latestTrading->index_individual : 0;
        }

        // Estimasi sederhana: indeks hari ini = indeks kemarin * (harga hari ini / harga kemarin)
        $previousClose = $this->previousClose;
        $currentPrice = $this->stockPriceSnapshot['close'];

        if ($previousClose > 0) {
            $priceRatio = $currentPrice / $previousClose;
            return (float) $this->latestTrading->index_individual * $priceRatio;
        }

        return (float) $this->latestTrading->index_individual;
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

    public function getIsStockPriceCardVisibleProperty(): bool
    {
        // IDX Market Hours using user's timezone
        /** @var User $user */
        $user = Auth::user();

        if (!$user || !$user->timezone) {
            // Fallback to server time if no user timezone is set
            $now = now();
        } else {
            // Use user's timezone
            $now = now($user->timezone);
        }

        $dayOfWeek = $now->format('N'); // 1=Monday, 7=Sunday
        $currentTime = $now->format('H:i:s');

        // Monday-Thursday (1-4)
        if ($dayOfWeek >= 1 && $dayOfWeek <= 4) {
            // Session I: 09:00:00 - 12:00:00
            // Session II: 13:30:00 - 15:49:59
            return ($currentTime >= '09:00:00' && $currentTime <= '12:00:00') ||
                   ($currentTime >= '13:30:00' && $currentTime <= '15:49:59');
        }
        // Friday (5)
        elseif ($dayOfWeek == 5) {
            // Session I: 09:00:00 - 11:30:00
            // Session II: 14:00:00 - 15:49:59
            return ($currentTime >= '09:00:00' && $currentTime <= '11:30:00') ||
                   ($currentTime >= '14:00:00' && $currentTime <= '15:49:59');
        }
        // Saturday-Sunday (6-7) - No trading
        else {
            return false;
        }
    }

    public function getIsMarketBreakProperty(): bool
    {
        // Check if market is currently in break time (between sessions)
        /** @var User $user */
        $user = Auth::user();

        if (!$user || !$user->timezone) {
            // Fallback to server time if no user timezone is set
            $now = now();
        } else {
            // Use user's timezone
            $now = now($user->timezone);
        }

        $dayOfWeek = $now->format('N'); // 1=Monday, 7=Sunday
        $currentTime = $now->format('H:i:s');

        // Monday-Thursday (1-4)
        if ($dayOfWeek >= 1 && $dayOfWeek <= 4) {
            // Break between sessions: 12:00:00 - 13:30:00
            return $currentTime >= '12:00:00' && $currentTime < '13:30:00';
        }
        // Friday (5)
        elseif ($dayOfWeek == 5) {
            // Break between sessions: 11:30:00 - 14:00:00
            return $currentTime >= '11:30:00' && $currentTime < '14:00:00';
        }

        // No break time on weekends
        return false;
    }

    public function getLastTradingSessionTimeProperty(): string
    {
        // Get the end time of the last trading session based on current session
        /** @var User $user */
        $user = Auth::user();

        if (!$user || !$user->timezone) {
            // Fallback to server time if no user timezone is set
            $now = now();
        } else {
            // Use user's timezone
            $now = now($user->timezone);
        }

        $dayOfWeek = $now->format('N'); // 1=Monday, 7=Sunday
        $currentTime = $now->format('H:i:s');

        // Determine which session we're currently in or when the last session ended
        if ($dayOfWeek >= 1 && $dayOfWeek <= 4) {
            // Monday-Thursday schedule
            if ($currentTime < '09:00:00') {
                // Before market opens - last session was yesterday at 15:49:59
                return $now->subDay()->format('Y-m-d') . ' 15:49:59';
            } elseif ($currentTime >= '09:00:00' && $currentTime < '12:00:00') {
                // Currently in Session I - data should be from current session
                return $now->format('Y-m-d') . ' ' . $currentTime;
            } elseif ($currentTime >= '12:00:00' && $currentTime < '13:30:00') {
                // Break time after Session I - last session ended at 12:00:00
                return $now->format('Y-m-d') . ' 12:00:00';
            } elseif ($currentTime >= '13:30:00' && $currentTime <= '15:49:59') {
                // Currently in Session II - data should be from current session
                return $now->format('Y-m-d') . ' ' . $currentTime;
            } else {
                // After market closes - last session ended at 15:49:59
                return $now->format('Y-m-d') . ' 15:49:59';
            }
        } elseif ($dayOfWeek == 5) {
            // Friday schedule
            if ($currentTime < '09:00:00') {
                // Before market opens - last session was yesterday (Thursday) at 15:49:59
                return $now->subDay()->format('Y-m-d') . ' 15:49:59';
            } elseif ($currentTime >= '09:00:00' && $currentTime < '11:30:00') {
                // Currently in Session I - data should be from current session
                return $now->format('Y-m-d') . ' ' . $currentTime;
            } elseif ($currentTime >= '11:30:00' && $currentTime < '14:00:00') {
                // Break time after Session I - last session ended at 11:30:00
                return $now->format('Y-m-d') . ' 11:30:00';
            } elseif ($currentTime >= '14:00:00' && $currentTime <= '15:49:59') {
                // Currently in Session II - data should be from current session
                return $now->format('Y-m-d') . ' ' . $currentTime;
            } else {
                // After market closes - last session ended at 15:49:59
                return $now->format('Y-m-d') . ' 15:49:59';
            }
        } elseif ($dayOfWeek == 6) {
            // Saturday - last session was Friday at 15:49:59
            return $now->subDay()->format('Y-m-d') . ' 15:49:59';
        } elseif ($dayOfWeek == 7) {
            // Sunday - last session was Friday at 15:49:59
            return $now->subDays(2)->format('Y-m-d') . ' 15:49:59';
        }

        // Fallback
        return $now->format('Y-m-d H:i:s');
    }

    public function getCombinedTradingHistoryProperty()
    {
        $history = collect($this->tradingHistory);

        // Add latest data from API if available and not already in history
        if ($this->stockPriceSnapshot) {
            $apiDate = $this->stockPriceSnapshot['date'];
            $existingDates = $history->filter(function ($item) use ($apiDate) {
                return is_object($item) && isset($item->date) && $item->date instanceof \Carbon\Carbon;
            })->pluck('date')->map(function ($date) {
                return $date->format('Y-m-d');
            });

            $apiDataExists = $existingDates->contains($apiDate);

            // Only add if not already exists in history
            if (!$apiDataExists) {
                // Convert API data to match database model structure
                $latestData = (object) [
                    'date' => \Carbon\Carbon::createFromFormat('Y-m-d', $apiDate),
                    'open_price' => (float) $this->stockPriceSnapshot['open'],
                    'high' => (float) $this->stockPriceSnapshot['high'],
                    'low' => (float) $this->stockPriceSnapshot['low'],
                    'close' => (float) $this->stockPriceSnapshot['close'],
                    'change' => (float) $this->stockPriceSnapshot['change'],
                    'volume' => (float) $this->stockPriceSnapshot['volume'],
                    'value' => (float) ($this->stockPriceSnapshot['volume'] * $this->stockPriceSnapshot['close']), // Calculate value
                    'frequency' => 1, // Default frequency for API data
                ];

                // Determine market status badge for API data
                $user = Auth::user();
                $now = $user && $user->timezone ? now($user->timezone) : now();
                $dayOfWeek = $now->format('N');
                $currentTime = $now->format('H:i:s');

                $marketStatus = 'CLOSED'; // Default

                if ($dayOfWeek >= 1 && $dayOfWeek <= 4) {
                    // Monday-Thursday
                    if (($currentTime >= '09:00:00' && $currentTime <= '12:00:00') ||
                        ($currentTime >= '13:30:00' && $currentTime <= '15:49:59')) {
                        $marketStatus = 'LIVE';
                    } elseif ($currentTime >= '12:00:00' && $currentTime < '13:30:00') {
                        $marketStatus = 'BREAK';
                    }
                } elseif ($dayOfWeek == 5) {
                    // Friday
                    if (($currentTime >= '09:00:00' && $currentTime <= '11:30:00') ||
                        ($currentTime >= '14:00:00' && $currentTime <= '15:49:59')) {
                        $marketStatus = 'LIVE';
                    } elseif ($currentTime >= '11:30:00' && $currentTime < '14:00:00') {
                        $marketStatus = 'BREAK';
                    }
                } elseif ($dayOfWeek == 6 || $dayOfWeek == 7) {
                    $marketStatus = 'WEEKEND';
                }

                // Add to beginning of collection (most recent first)
                $latestData->_isFromApi = true; // Mark as API data
                $latestData->_marketStatus = $marketStatus; // Add market status
                $history->prepend($latestData);
            }
        }

        return $history;
    }

    public function refreshStockPrice(): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Calculate market hours for logging
        $logUserTimezone = $user && $user->timezone ? $user->timezone : 'server';
        $logNow = $user && $user->timezone ? now($user->timezone) : now();
        $logDayOfWeek = $logNow->format('N');
        $logCurrentTime = $logNow->format('H:i:s');
        $logIsMarketHours = false;

        if ($logDayOfWeek >= 1 && $logDayOfWeek <= 4) {
            $logIsMarketHours = ($logCurrentTime >= '09:00:00' && $logCurrentTime <= '12:00:00') ||
                              ($logCurrentTime >= '13:30:00' && $logCurrentTime <= '15:49:59');
        } elseif ($logDayOfWeek == 5) {
            $logIsMarketHours = ($logCurrentTime >= '09:00:00' && $logCurrentTime <= '11:30:00') ||
                              ($logCurrentTime >= '14:00:00' && $logCurrentTime <= '15:49:59');
        }


        // Only refresh if user is authenticated
        if (!$user) {
            logger()->warning('Stock price refresh skipped - user not authenticated');
            return;
        }

        // Ensure stock_code is set
        if (empty($this->stockCode)) {
            logger()->warning('Stock price refresh skipped - stock_code is empty');
            // Try to reload stock code from user
            if ($user->default_kode_emiten) {
                $this->stockCode = strtoupper($user->default_kode_emiten);
                logger()->info('Stock code reloaded from user', ['stock_code' => $this->stockCode]);
            } else {
                return;
            }
        }

        $this->fetchStockPriceSnapshot();
    }

    public function refreshChart(): void
    {
        // Dispatch chart update event with fresh data
        $this->dispatch('chart-data-updated', [
            'period' => $this->period,
            'chartData' => $this->chartData
        ]);
    }

    public function getChartDataProperty(): array
    {
        $data = $this->combinedTradingHistory->sortBy('date')->values();

        // Format for TradingView Lightweight Charts
        $candlestick = $data->map(function($item) {
            $candleData = [
                'time' => $item->date->format('Y-m-d'),
                'open' => (float) $item->open_price,
                'high' => (float) $item->high,
                'low' => (float) $item->low,
                'close' => (float) $item->close,
                '_isFromApi' => isset($item->_isFromApi) ? $item->_isFromApi : false,
            ];

            // Add special styling for API data (latest candle)
            if (isset($item->_isFromApi) && $item->_isFromApi) {
                $status = isset($item->_marketStatus) ? $item->_marketStatus : 'LIVE';
                switch ($status) {
                    case 'LIVE':
                        // Green border for live data
                        $candleData['borderColor'] = '#10b981'; // emerald-500
                        $candleData['wickColor'] = '#10b981';
                        break;
                    case 'BREAK':
                        // Yellow border for break time
                        $candleData['borderColor'] = '#f59e0b'; // amber-500
                        $candleData['wickColor'] = '#f59e0b';
                        break;
                    case 'CLOSED':
                        // Orange border for closed market
                        $candleData['borderColor'] = '#f97316'; // orange-500
                        $candleData['wickColor'] = '#f97316';
                        break;
                    case 'WEEKEND':
                        // Gray border for weekend
                        $candleData['borderColor'] = '#6b7280'; // gray-500
                        $candleData['wickColor'] = '#6b7280';
                        break;
                }
            }

            return $candleData;
        })->toArray();

        $volume = $data->map(function($item) {
            $volumeData = [
                'time' => $item->date->format('Y-m-d'),
                'value' => (float) $item->volume,
                'color' => $item->change >= 0 ? 'rgba(38, 166, 154, 0.7)' : 'rgba(239, 83, 80, 0.7)',
            ];

            // Add special styling for API data volume bars
            if (isset($item->_isFromApi) && $item->_isFromApi) {
                $status = isset($item->_marketStatus) ? $item->_marketStatus : 'LIVE';
                switch ($status) {
                    case 'LIVE':
                        $volumeData['color'] = $item->change >= 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(239, 68, 68, 0.8)'; // emerald/red
                        break;
                    case 'BREAK':
                        $volumeData['color'] = $item->change >= 0 ? 'rgba(245, 158, 11, 0.8)' : 'rgba(245, 158, 11, 0.8)'; // amber
                        break;
                    case 'CLOSED':
                        $volumeData['color'] = $item->change >= 0 ? 'rgba(249, 115, 22, 0.8)' : 'rgba(249, 115, 22, 0.8)'; // orange
                        break;
                    case 'WEEKEND':
                        $volumeData['color'] = $item->change >= 0 ? 'rgba(107, 114, 128, 0.8)' : 'rgba(107, 114, 128, 0.8)'; // gray
                        break;
                }
            }

            return $volumeData;
        })->toArray();

        return [
            'candlestick' => $candlestick,
            'volume' => $volume,
            'indicators' => $this->technicalIndicators['chart_data'] ?? [],
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

    private function calculateTechnicalIndicators(): void
    {
        $combinedHistory = $this->combinedTradingHistory;
        if ($combinedHistory->isEmpty()) {
            $this->technicalIndicators = [];
            return;
        }

        $data = $combinedHistory->sortBy('date')->values();
        $closes = $data->pluck('close')->toArray();
        $highs = $data->pluck('high')->toArray();
        $lows = $data->pluck('low')->toArray();
        $volumes = $data->pluck('volume')->toArray();

        // Calculate time series data for chart overlays
        $sma20Series = $this->calculateSMAArray($closes, 20);
        $sma50Series = $this->calculateSMAArray($closes, 50);
        $ema20Series = $this->calculateEMAArray($closes, 20);
        $ema50Series = $this->calculateEMAArray($closes, 50);
        $bollingerBands = $this->calculateBollingerBandsArray($closes, 20, 2);

        // Calculate RSI and MACD time series for separate panes
        $rsiSeries = $this->calculateRSIArray($closes, 14);
        $macdSeries = $this->calculateMACDArray($closes);

        // Convert to chart data format
        $dates = $data->pluck('date')->toArray();

        $this->technicalIndicators = [
            // Current values for sidebar display
            'sma_20' => $this->calculateSMA($closes, 20),
            'sma_50' => $this->calculateSMA($closes, 50),
            'ema_20' => $this->calculateEMA($closes, 20),
            'ema_50' => $this->calculateEMA($closes, 50),
            'rsi' => $this->calculateRSI($closes, 14),
            'macd' => $this->calculateMACD($closes),
            'bollinger_bands' => $this->calculateBollingerBands($closes, 20, 2),
            'stochastic' => $this->calculateStochastic($highs, $lows, $closes, 14),
            'volume_sma' => $this->calculateSMA($volumes, 20),

            // Time series data for chart overlays
            'chart_data' => [
                'sma_20' => $this->formatIndicatorSeries($dates, $sma20Series),
                'sma_50' => $this->formatIndicatorSeries($dates, $sma50Series),
                'ema_20' => $this->formatIndicatorSeries($dates, $ema20Series),
                'ema_50' => $this->formatIndicatorSeries($dates, $ema50Series),
                'bollinger_upper' => $this->formatIndicatorSeries($dates, $bollingerBands['upper'] ?? []),
                'bollinger_middle' => $this->formatIndicatorSeries($dates, $bollingerBands['middle'] ?? []),
                'bollinger_lower' => $this->formatIndicatorSeries($dates, $bollingerBands['lower'] ?? []),
                'rsi' => $this->formatIndicatorSeries($dates, $rsiSeries),
                'macd' => $macdSeries, // MACD is an array with macd, signal, histogram
            ]
        ];
    }

    private function calculateSMA(array $data, int $period): ?float
    {
        if (count($data) < $period) {
            return null;
        }

        $sum = array_sum(array_slice($data, -$period));
        return $sum / $period;
    }

    /**
     * Calculate SMA array for time series data
     */
    private function calculateSMAArray(array $data, int $period): array
    {
        $result = [];
        $count = count($data);

        for ($i = $period - 1; $i < $count; $i++) {
            $sum = 0;
            for ($j = $i - $period + 1; $j <= $i; $j++) {
                $sum += $data[$j];
            }
            $result[] = $sum / $period;
        }

        // Pad with nulls for earlier periods
        return array_pad($result, -$count, null);
    }

    private function calculateEMA(array $data, int $period): ?float
    {
        if (count($data) < $period) {
            return null;
        }

        $multiplier = 2 / ($period + 1);

        // Start with SMA for the first EMA value
        $ema = array_sum(array_slice($data, 0, $period)) / $period;

        // Calculate EMA for the remaining values
        for ($i = $period; $i < count($data); $i++) {
            $ema = ($data[$i] - $ema) * $multiplier + $ema;
        }

        return $ema;
    }

    /**
     * Calculate EMA array for time series data
     */
    private function calculateEMAArray(array $data, int $period): array
    {
        $result = [];
        $count = count($data);

        if ($count < $period) {
            return array_fill(0, $count, null);
        }

        $multiplier = 2 / ($period + 1);

        // Start with SMA for the first EMA value
        $sma = array_sum(array_slice($data, 0, $period)) / $period;
        $ema = $sma;

        // First EMA value
        $result[] = $ema;

        // Calculate EMA for the remaining values
        for ($i = $period; $i < $count; $i++) {
            $ema = ($data[$i] - $ema) * $multiplier + $ema;
            $result[] = $ema;
        }

        // Pad with nulls for earlier periods
        return array_pad($result, -$count, null);
    }

    private function calculateRSI(array $closes, int $period = 14): ?float
    {
        if (count($closes) < $period + 1) {
            return null;
        }

        $gains = [];
        $losses = [];

        for ($i = 1; $i < count($closes); $i++) {
            $change = $closes[$i] - $closes[$i - 1];
            $gains[] = $change > 0 ? $change : 0;
            $losses[] = $change < 0 ? abs($change) : 0;
        }

        if (count($gains) < $period) {
            return null;
        }

        $avgGain = array_sum(array_slice($gains, 0, $period)) / $period;
        $avgLoss = array_sum(array_slice($losses, 0, $period)) / $period;

        // Calculate RSI using Wilder's smoothing method
        for ($i = $period; $i < count($gains); $i++) {
            $avgGain = (($avgGain * ($period - 1)) + $gains[$i]) / $period;
            $avgLoss = (($avgLoss * ($period - 1)) + $losses[$i]) / $period;
        }

        if ($avgLoss == 0) {
            return 100;
        }

        $rs = $avgGain / $avgLoss;
        return 100 - (100 / (1 + $rs));
    }

    /**
     * Calculate RSI array for time series data
     */
    private function calculateRSIArray(array $closes, int $period = 14): array
    {
        $result = [];
        $count = count($closes);

        if ($count < $period + 1) {
            return array_fill(0, $count, null);
        }

        // Calculate gains and losses
        $gains = [];
        $losses = [];

        for ($i = 1; $i < $count; $i++) {
            $change = $closes[$i] - $closes[$i - 1];
            $gains[] = $change > 0 ? $change : 0;
            $losses[] = $change < 0 ? abs($change) : 0;
        }

        // Calculate RSI for each period
        for ($i = $period; $i < count($gains); $i++) {
            $avgGain = array_sum(array_slice($gains, $i - $period, $period)) / $period;
            $avgLoss = array_sum(array_slice($losses, $i - $period, $period)) / $period;

            if ($avgLoss == 0) {
                $rsi = 100;
            } else {
                $rs = $avgGain / $avgLoss;
                $rsi = 100 - (100 / (1 + $rs));
            }

            $result[] = $rsi;
        }

        // Pad with nulls for earlier periods
        return array_pad($result, -$count, null);
    }

    private function calculateMACD(array $closes): array
    {
        $fastPeriod = 12;
        $slowPeriod = 26;

        $fastEMA = $this->calculateEMA($closes, $fastPeriod);
        $slowEMA = $this->calculateEMA($closes, $slowPeriod);

        if ($fastEMA === null || $slowEMA === null) {
            return ['macd' => null, 'signal' => null, 'histogram' => null];
        }

        $macd = $fastEMA - $slowEMA;

        // For simplicity, use MACD as signal line when we don't have enough data
        // In a full implementation, you'd calculate EMA of MACD values over signal period
        $signal = $macd;

        return [
            'macd' => $macd,
            'signal' => $signal,
            'histogram' => 0, // No histogram without proper signal calculation
        ];
    }

    /**
     * Calculate MACD array for time series data
     */
    private function calculateMACDArray(array $closes): array
    {
        $fastPeriod = 12;
        $slowPeriod = 26;
        $signalPeriod = 9;

        $fastEMAs = $this->calculateEMAArray($closes, $fastPeriod);
        $slowEMAs = $this->calculateEMAArray($closes, $slowPeriod);

        $macdLine = [];
        $signalLine = [];
        $histogram = [];

        $count = count($closes);
        for ($i = 0; $i < $count; $i++) {
            if ($fastEMAs[$i] !== null && $slowEMAs[$i] !== null) {
                $macd = $fastEMAs[$i] - $slowEMAs[$i];
                $macdLine[] = $macd;

                // Calculate signal line (EMA of MACD)
                if (count($macdLine) >= $signalPeriod) {
                    $signal = $this->calculateEMA(array_slice($macdLine, -$signalPeriod), $signalPeriod);
                    $signalLine[] = $signal ?? $macd;
                    $histogram[] = $macd - ($signal ?? $macd);
                } else {
                    $signalLine[] = $macd;
                    $histogram[] = 0;
                }
            } else {
                $macdLine[] = null;
                $signalLine[] = null;
                $histogram[] = null;
            }
        }

        return [
            'macd' => $macdLine,
            'signal' => $signalLine,
            'histogram' => $histogram,
        ];
    }

    private function calculateBollingerBands(array $closes, int $period = 20, float $stdDev = 2): array
    {
        $sma = $this->calculateSMA($closes, $period);

        if ($sma === null) {
            return ['upper' => null, 'middle' => null, 'lower' => null];
        }

        $recentCloses = array_slice($closes, -$period);
        $variance = 0;

        foreach ($recentCloses as $close) {
            $variance += pow($close - $sma, 2);
        }

        $stdDeviation = sqrt($variance / $period);

        return [
            'upper' => $sma + ($stdDev * $stdDeviation),
            'middle' => $sma,
            'lower' => $sma - ($stdDev * $stdDeviation),
        ];
    }

    /**
     * Calculate Bollinger Bands array for time series data
     */
    private function calculateBollingerBandsArray(array $closes, int $period = 20, float $stdDev = 2): array
    {
        $upper = [];
        $middle = [];
        $lower = [];
        $count = count($closes);

        for ($i = $period - 1; $i < $count; $i++) {
            $recentCloses = array_slice($closes, $i - $period + 1, $period);
            $sma = array_sum($recentCloses) / $period;

            $variance = 0;
            foreach ($recentCloses as $close) {
                $variance += pow($close - $sma, 2);
            }
            $stdDeviation = sqrt($variance / $period);

            $upper[] = $sma + ($stdDev * $stdDeviation);
            $middle[] = $sma;
            $lower[] = $sma - ($stdDev * $stdDeviation);
        }

        // Pad with nulls for earlier periods
        return [
            'upper' => array_pad($upper, -$count, null),
            'middle' => array_pad($middle, -$count, null),
            'lower' => array_pad($lower, -$count, null),
        ];
    }

    private function calculateStochastic(array $highs, array $lows, array $closes, int $period = 14): array
    {
        if (count($closes) < $period) {
            return ['k' => null, 'd' => null];
        }

        $recentHighs = array_slice($highs, -$period);
        $recentLows = array_slice($lows, -$period);
        $currentClose = end($closes);

        $highestHigh = max($recentHighs);
        $lowestLow = min($recentLows);

        if ($highestHigh == $lowestLow) {
            $k = 100; // Avoid division by zero
        } else {
            $k = (($currentClose - $lowestLow) / ($highestHigh - $lowestLow)) * 100;
        }

        // D value is typically 3-period SMA of K
        $d = $k; // Simplified version

        return ['k' => $k, 'd' => $d];
    }

    /**
     * Format indicator series for chart data
     */
    private function formatIndicatorSeries(array $dates, array $values): array
    {
        $result = [];
        foreach ($dates as $index => $date) {
            $value = $values[$index] ?? null;
            if ($value !== null) {
                $result[] = [
                    'time' => $date->format('Y-m-d'),
                    'value' => (float) $value,
                ];
            }
        }
        return $result;
    }

    public function render()
    {
        if ($this->needsSetup) {
            return redirect()->route('settings.profile')->with('error', 'Please set your default stock code in your profile settings.');
        }

        return view('livewire.dashboard.dashboard-index');
    }
}
