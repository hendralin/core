<?php

namespace App\Livewire\Report\Analytics;

use App\Services\AnalyticsService;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Analytics')]
class AnalyticsIndex extends Component
{
    public string $dateFrom;

    public string $dateTo;

    public string $rangePreset = '30d';

    public function mount(): void
    {
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
    }

    public function updatedRangePreset(string $value): void
    {
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = match ($value) {
            '7d' => now()->subDays(7)->format('Y-m-d'),
            '30d' => now()->subDays(30)->format('Y-m-d'),
            '90d' => now()->subDays(90)->format('Y-m-d'),
            default => $this->dateFrom,
        };
    }

    public function applyDateRange(): void
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay();
        $to = Carbon::parse($this->dateTo)->endOfDay();
        if ($from->gt($to)) {
            [$this->dateFrom, $this->dateTo] = [$this->dateTo, $this->dateFrom];
        }
    }

    public function render(AnalyticsService $analytics)
    {
        $this->applyDateRange();

        [$from, $to] = $analytics->resolveRange($this->dateFrom, $this->dateTo);

        $visitSummary = $analytics->visitSummary($from, $to);
        $dailyPublic = $analytics->dailyVisitTrend($from, $to, true);
        $dailyAll = $analytics->dailyVisitTrend($from, $to, false);
        $browsers = $analytics->browserBreakdown($from, $to, true);
        $platforms = $analytics->platformBreakdown($from, $to, true);
        $devices = $analytics->deviceCategoryBreakdown($from, $to, true);
        $topUrls = $analytics->topUrls($from, $to, true);
        $topVehicles = $analytics->topVehiclesByVisits($from, $to);
        $engagement = $analytics->vehicleEngagementTotals();
        $internal = $analytics->internalActivitySummary($from, $to);
        $topUsers = $analytics->topActiveUsers($from, $to);
        $dailyLogins = $analytics->dailyLoginTrend($from, $to);

        return view('livewire.report.analytics.analytics-index', [
            'visitSummary' => $visitSummary,
            'dailyPublic' => $dailyPublic,
            'dailyAll' => $dailyAll,
            'browsers' => $browsers,
            'platforms' => $platforms,
            'devices' => $devices,
            'topUrls' => $topUrls,
            'topVehicles' => $topVehicles,
            'engagement' => $engagement,
            'internal' => $internal,
            'topUsers' => $topUsers,
            'dailyLogins' => $dailyLogins,
            'periodLabel' => $from->format('d M Y').' – '.$to->format('d M Y'),
        ]);
    }
}
