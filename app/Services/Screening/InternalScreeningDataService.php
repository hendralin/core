<?php

namespace App\Services\Screening;

use App\Models\CompanyDividend;
use App\Models\FinancialRatio;
use App\Models\StockCompany;
use App\Models\TradingInfo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InternalScreeningDataService
{
    /**
     * Latest audited financial ratio row per stock code (by max fs_date).
     *
     * @return \Illuminate\Database\Eloquent\Builder<FinancialRatio>
     */
    public function latestAuditedPerCodeQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $sub = DB::table('financial_ratios')
            ->select('code', DB::raw('MAX(fs_date) as max_fs'))
            ->where('audit', 'A')
            ->groupBy('code');

        return FinancialRatio::query()
            ->select('financial_ratios.*')
            ->audited()
            ->joinSub($sub, 'latest', function ($join) {
                $join->on('financial_ratios.code', '=', 'latest.code')
                    ->on('financial_ratios.fs_date', '=', 'latest.max_fs');
            })
            ->with('stockCompany');
    }

    /**
     * @return list<string>
     */
    public function listSectors(int $limit = 100): array
    {
        return StockCompany::query()
            ->select('sektor')
            ->whereNotNull('sektor')
            ->where('sektor', '!=', '')
            ->distinct()
            ->orderBy('sektor')
            ->limit($limit)
            ->pluck('sektor')
            ->values()
            ->all();
    }

    /**
     * @param  array{sector?: string|null, min_roe?: float|null, max_de_ratio?: float|null, max_per?: float|null, sharia_only?: bool, min_fs_year?: int|null, sectors?: list<string>|null, limit?: int}  $filters
     * @return list<array<string, mixed>>
     */
    public function screenFundamentalCandidates(array $filters): array
    {
        $limit = isset($filters['limit']) && (int) $filters['limit'] > 0
            ? min((int) $filters['limit'], 20)
            : 10;

        $query = $this->latestAuditedPerCodeQuery();

        if (! empty($filters['sectors']) && is_array($filters['sectors'])) {
            $sectors = array_values(array_filter($filters['sectors']));
            if ($sectors !== []) {
                $query->whereIn('financial_ratios.sector', $sectors);
            }
        } elseif (! empty($filters['sector'])) {
            $query->where('financial_ratios.sector', $filters['sector']);
        }

        if (! empty($filters['sharia_only'])) {
            $query->where('financial_ratios.sharia', 'S');
        }

        if (isset($filters['min_roe']) && $filters['min_roe'] !== null) {
            $query->where('financial_ratios.roe', '>=', (float) $filters['min_roe']);
        }

        if (isset($filters['max_de_ratio']) && $filters['max_de_ratio'] !== null) {
            $query->where('financial_ratios.de_ratio', '<=', (float) $filters['max_de_ratio']);
        }

        if (isset($filters['max_per']) && $filters['max_per'] !== null) {
            $query->where('financial_ratios.per', '<=', (float) $filters['max_per']);
        }

        if (isset($filters['min_fs_year']) && $filters['min_fs_year'] !== null) {
            $query->whereYear('financial_ratios.fs_date', '>=', (int) $filters['min_fs_year']);
        }

        $query->orderByDesc('financial_ratios.roe');

        $rows = $query->limit($limit)->get();

        return $rows->map(function (FinancialRatio $row) {
            $company = $row->stockCompany;

            return [
                'code' => $row->code,
                'company_name' => $company?->nama_emiten ?? $row->stock_name,
                'sector' => $row->sector,
                'industry' => $row->industry,
                'fs_date' => optional($row->fs_date)->format('Y-m-d'),
                'per' => $row->per !== null ? (float) $row->per : null,
                'price_bv' => $row->price_bv !== null ? (float) $row->price_bv : null,
                'de_ratio' => $row->de_ratio !== null ? (float) $row->de_ratio : null,
                'roe' => $row->roe !== null ? (float) $row->roe : null,
                'roa' => $row->roa !== null ? (float) $row->roa : null,
                'npm' => $row->npm !== null ? (float) $row->npm : null,
                'sales_bn_idr' => $row->sales !== null ? (float) $row->sales : null,
                'profit_attr_owner_bn_idr' => $row->profit_attr_owner !== null ? (float) $row->profit_attr_owner : null,
                'is_sharia' => $row->isSharia(),
            ];
        })->all();
    }

    /**
     * @return array{sector: string|null, sample_size: int, avg_per: float|null, median_per: float|null|null}
     */
    public function sectorValuationBenchmark(?string $sector): array
    {
        $q = $this->latestAuditedPerCodeQuery()
            ->whereNotNull('financial_ratios.per')
            ->where('financial_ratios.per', '>', 0);

        if ($sector) {
            $q->where('financial_ratios.sector', $sector);
        }

        $pers = $q->get()
            ->pluck('per')
            ->filter(fn ($v) => $v !== null && (float) $v > 0)
            ->map(fn ($v) => (float) $v)
            ->sort()
            ->values();

        if ($pers->isEmpty()) {
            return [
                'sector' => $sector,
                'sample_size' => 0,
                'avg_per' => null,
                'median_per' => null,
            ];
        }

        $avg = $pers->avg();
        $mid = (int) floor(($pers->count() - 1) / 2);
        $median = $pers->count() % 2 === 1
            ? $pers[$mid]
            : ($pers[$mid] + $pers[$mid + 1]) / 2;

        return [
            'sector' => $sector,
            'sample_size' => $pers->count(),
            'avg_per' => $avg !== null ? round((float) $avg, 4) : null,
            'median_per' => $median !== null ? round((float) $median, 4) : null,
        ];
    }

    /**
     * Up to 5 annual audited points for revenue trend (sales in billions IDR).
     *
     * @return array{code: string, points: list<array{fs_date: string|null, sales_bn_idr: float|null, profit_bn_idr: float|null, per: float|null, de_ratio: float|null}>}
     */
    public function revenueSeriesFiveYears(string $code): array
    {
        $code = strtoupper(trim($code));

        $rows = FinancialRatio::query()
            ->audited()
            ->where('code', $code)
            ->orderByDesc('fs_date')
            ->limit(5)
            ->get()
            ->sortBy('fs_date')
            ->values();

        $points = $rows->map(function (FinancialRatio $row) {
            return [
                'fs_date' => optional($row->fs_date)->format('Y-m-d'),
                'sales_bn_idr' => $row->sales !== null ? (float) $row->sales : null,
                'profit_bn_idr' => $row->profit_attr_owner !== null ? (float) $row->profit_attr_owner : null,
                'per' => $row->per !== null ? (float) $row->per : null,
                'de_ratio' => $row->de_ratio !== null ? (float) $row->de_ratio : null,
            ];
        })->all();

        $cagr = null;
        $salesVals = array_values(array_filter(array_column($points, 'sales_bn_idr'), fn ($v) => $v !== null && $v > 0));
        if (count($salesVals) >= 2) {
            $first = $salesVals[0];
            $last = $salesVals[count($salesVals) - 1];
            $n = count($salesVals) - 1;
            if ($first > 0 && $n > 0) {
                $cagr = pow($last / $first, 1 / $n) - 1;
            }
        }

        return [
            'code' => $code,
            'points' => $points,
            'sales_cagr_approx' => $cagr !== null ? round($cagr * 100, 2) : null,
            'note' => 'CAGR is computed from available audited sales points (up to 5 years), not necessarily calendar years.',
        ];
    }

    /**
     * @return array{code: string, latest_close: float|null, latest_date: string|null, currency: string}
     */
    public function latestTradingSnapshot(string $code): array
    {
        $code = strtoupper(trim($code));

        $latestDate = TradingInfo::query()->where('kode_emiten', $code)->max('date');
        if (! $latestDate) {
            return [
                'code' => $code,
                'latest_close' => null,
                'latest_date' => null,
                'currency' => 'IDR',
            ];
        }

        $row = TradingInfo::query()
            ->where('kode_emiten', $code)
            ->whereDate('date', $latestDate)
            ->orderByDesc('id')
            ->first();

        return [
            'code' => $code,
            'latest_close' => $row ? (float) $row->close : null,
            'latest_date' => Carbon::parse($latestDate)->format('Y-m-d'),
            'currency' => 'IDR',
        ];
    }

    /**
     * Cash dividends (tunai) per fiscal year, latest first.
     *
     * @return array{code: string, items: list<array<string, mixed>>, dividend_yield_approx: float|null, payout_sustainability_hint: string|null}
     */
    public function dividendProfile(string $code): array
    {
        $code = strtoupper(trim($code));

        $rows = CompanyDividend::query()
            ->where('kode_emiten', $code)
            ->whereRaw('LOWER(jenis) = ?', ['dt'])
            ->orderByDesc('tahun_buku')
            ->limit(8)
            ->get();

        $items = $rows->map(function (CompanyDividend $d) {
            return [
                'tahun_buku' => $d->tahun_buku,
                'cash_dividen_per_saham' => $d->cash_dividen_per_saham !== null ? (float) $d->cash_dividen_per_saham : null,
                'currency' => $d->cash_dividen_per_saham_mu,
                'tanggal_pembayaran' => optional($d->tanggal_pembayaran)->format('Y-m-d'),
            ];
        })->all();

        $snap = $this->latestTradingSnapshot($code);
        $dps = $rows->first()?->cash_dividen_per_saham;
        $yield = null;
        if ($dps !== null && $snap['latest_close'] !== null && $snap['latest_close'] > 0) {
            $yield = ($dps / $snap['latest_close']) * 100;
        }

        $latest = FinancialRatio::query()
            ->audited()
            ->where('code', $code)
            ->orderByDesc('fs_date')
            ->first();

        $payoutHint = null;
        if ($latest && $latest->profit_attr_owner !== null && $latest->profit_attr_owner > 0 && $rows->isNotEmpty()) {
            $totalDiv = $rows->map(fn ($r) => (float) ($r->cash_dividen_total ?? 0))->filter()->sum();
            if ($totalDiv > 0) {
                $payoutHint = 'Compare cash dividend totals against profit_attr_owner from latest audited financial_ratios for payout ratio.';
            }
        }

        return [
            'code' => $code,
            'items' => $items,
            'dividend_yield_approx' => $yield !== null ? round($yield, 4) : null,
            'payout_sustainability_hint' => $payoutHint,
        ];
    }

    /**
     * @return array{code: string, latest_ratio: ?array<string, mixed>, sector_benchmark: array<string, mixed>}
     */
    public function emitenFundamentalBundle(string $code): array
    {
        $code = strtoupper(trim($code));

        $latest = FinancialRatio::query()
            ->audited()
            ->where('code', $code)
            ->orderByDesc('fs_date')
            ->first();

        $sector = $latest?->sector;

        $latestArr = null;
        if ($latest) {
            $latestArr = [
                'fs_date' => optional($latest->fs_date)->format('Y-m-d'),
                'sector' => $latest->sector,
                'industry' => $latest->industry,
                'per' => $latest->per !== null ? (float) $latest->per : null,
                'de_ratio' => $latest->de_ratio !== null ? (float) $latest->de_ratio : null,
                'roe' => $latest->roe !== null ? (float) $latest->roe : null,
                'npm' => $latest->npm !== null ? (float) $latest->npm : null,
                'sales_bn_idr' => $latest->sales !== null ? (float) $latest->sales : null,
            ];
        }

        return [
            'code' => $code,
            'latest_ratio' => $latestArr,
            'sector_benchmark' => $this->sectorValuationBenchmark($sector),
            'revenue_trend' => $this->revenueSeriesFiveYears($code),
            'trading' => $this->latestTradingSnapshot($code),
            'dividends' => $this->dividendProfile($code),
        ];
    }
}
