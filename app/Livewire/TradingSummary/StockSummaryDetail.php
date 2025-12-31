<?php

namespace App\Livewire\TradingSummary;

use App\Models\TradingInfo;
use App\Models\StockCompany;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Collection;

#[Title('Stock Detail')]
class StockSummaryDetail extends Component
{
    public string $stockCode;
    public ?StockCompany $company = null;
    public ?TradingInfo $latestTrading = null;
    public Collection $tradingHistory;
    public string $period = '30'; // Days
    public bool $isCustomRange = false; // True when chart has been panned

    public function mount(string $stockCode): void
    {
        $this->stockCode = strtoupper($stockCode);
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
        $this->company = StockCompany::where('kode_emiten', $this->stockCode)->first();

        $this->latestTrading = TradingInfo::where('kode_emiten', $this->stockCode)
            ->orderBy('date', 'desc')
            ->first();

        $this->loadTradingHistory();
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
        return view('livewire.trading-summary.stock-summary-detail');
    }
}
