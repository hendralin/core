<?php

namespace App\Services\Valuation;

use App\Models\FinancialRatio;
use App\Models\TradingInfo;
use App\Services\Screening\InternalScreeningDataService;

/**
 * DCF proxy model: tidak ada laporan arus kas penuh di DB; FCF diaproksimasi dari laba bersih × faktor konversi.
 * Semua angka diberi label asumsi agar transparan untuk memo investasi.
 */
class DcfValuationService
{
    public function __construct(
        private InternalScreeningDataService $internal,
        private ExternalValuationEnrichmentService $external,
    ) {}

    /**
     * Data historis + snapshot pasar internal untuk satu emiten.
     *
     * @return array<string, mixed>
     */
    public function getHistoricalForDcf(string $code): array
    {
        $code = strtoupper(trim($code));

        $series = $this->internal->revenueSeriesFiveYears($code);
        $bundle = $this->internal->emitenFundamentalBundle($code);

        $latest = FinancialRatio::query()
            ->audited()
            ->where('code', $code)
            ->orderByDesc('fs_date')
            ->first();

        return [
            'code' => $code,
            'revenue_series' => $series,
            'fundamental_bundle' => $bundle,
            'latest_fs_date' => $latest ? optional($latest->fs_date)->format('Y-m-d') : null,
            'currency_note' => 'sales/profit dalam miliar IDR pada financial_ratios; harga saham IDR per lembar dari trading_infos.',
        ];
    }

    /**
     * Membangun model DCF lengkap (angka siap untuk tabel memo).
     * Nama perusahaan diambil dari financial_ratios.stock_name atau stock_companies.nama_emiten.
     *
     * @param  array<string, float|int|string|null>  $overrides
     * @return array<string, mixed>
     */
    public function buildFullDcfModel(string $code, array $overrides = []): array
    {
        $code = strtoupper(trim($code));

        $hist = $this->getHistoricalForDcf($code);
        $series = $hist['revenue_series'];
        $points = $series['points'] ?? [];

        $latest = FinancialRatio::query()
            ->audited()
            ->where('code', $code)
            ->orderByDesc('fs_date')
            ->with('stockCompany')
            ->first();

        if (! $latest || $latest->sales === null || (float) $latest->sales <= 0) {
            return [
                'error' => 'Data pendapatan (sales) tidak tersedia untuk emiten ini.',
                'code' => $code,
            ];
        }

        $resolvedCompanyName = $latest->stock_name
            ?: ($latest->stockCompany?->nama_emiten ?? null)
            ?: $code;

        $trading = $this->internal->latestTradingSnapshot($code);
        $listedShares = $this->latestListedShares($code);

        $external = $this->external->enrichDcf($code);

        $salesBnBase = (float) $latest->sales;
        $equityBn = $latest->equity !== null ? (float) $latest->equity : null;
        $deRatio = $latest->de_ratio !== null ? (float) $latest->de_ratio : 0.5;

        $cagrPct = $series['sales_cagr_approx'] ?? null;
        $growth = isset($overrides['revenue_growth_annual'])
            ? (float) $overrides['revenue_growth_annual'] / 100
            : $this->capGrowth($cagrPct !== null ? (float) $cagrPct : 8.0);

        $npmPct = isset($overrides['npm_pct'])
            ? (float) $overrides['npm_pct']
            : $this->resolveNpm($points, $latest);

        $fcfConv = isset($overrides['fcf_conversion'])
            ? (float) $overrides['fcf_conversion']
            : 0.78;

        $taxRate = isset($overrides['tax_rate']) ? (float) $overrides['tax_rate'] / 100 : 0.22;
        $rf = isset($overrides['risk_free_pct']) ? (float) $overrides['risk_free_pct'] / 100 : 0.065;
        $erp = isset($overrides['erp_pct']) ? (float) $overrides['erp_pct'] / 100 : 0.06;
        $beta = isset($overrides['beta']) && $overrides['beta'] !== null
            ? (float) $overrides['beta']
            : ($external['beta'] ?? 1.0);

        $costDebtPretax = isset($overrides['cost_debt_pretax_pct'])
            ? (float) $overrides['cost_debt_pretax_pct'] / 100
            : 0.09;

        $terminalG = isset($overrides['terminal_growth_pct'])
            ? (float) $overrides['terminal_growth_pct'] / 100
            : 0.025;

        $exitEvToSales = isset($overrides['exit_ev_to_sales'])
            ? (float) $overrides['exit_ev_to_sales']
            : 2.4;

        $wacc = isset($overrides['wacc_override_decimal'])
            ? (float) $overrides['wacc_override_decimal']
            : $this->computeWacc($deRatio, $beta, $rf, $erp, $costDebtPretax, $taxRate);

        $projection = [];
        $rev = $salesBnBase;
        for ($y = 1; $y <= 5; $y++) {
            $rev *= (1 + $growth);
            $netIncomeBn = $rev * ($npmPct / 100);
            $fcfBn = $netIncomeBn * $fcfConv;
            $projection[] = [
                'year' => $y,
                'revenue_bn_idr' => round($rev, 4),
                'npm_pct' => round($npmPct, 2),
                'net_income_bn_idr' => round($netIncomeBn, 4),
                'fcf_proxy_bn_idr' => round($fcfBn, 4),
                'pv_fcf_bn_idr' => round($fcfBn / pow(1 + $wacc, $y), 4),
            ];
        }

        $pvFcfSum = array_sum(array_column($projection, 'pv_fcf_bn_idr'));

        $fcf5 = $projection[4]['fcf_proxy_bn_idr'];
        if ($wacc <= $terminalG) {
            return [
                'error' => 'WACC harus lebih besar dari terminal growth (periksa asumsi).',
                'wacc' => $wacc,
                'terminal_growth' => $terminalG,
            ];
        }

        $tvPerpBn = ($fcf5 * (1 + $terminalG)) / ($wacc - $terminalG);
        $pvTvPerpBn = $tvPerpBn / pow(1 + $wacc, 5);

        $rev5 = $projection[4]['revenue_bn_idr'];
        $tvExitBn = $rev5 * $exitEvToSales;
        $pvTvExitBn = $tvExitBn / pow(1 + $wacc, 5);

        $evPerpBn = $pvFcfSum + $pvTvPerpBn;
        $evExitBn = $pvFcfSum + $pvTvExitBn;

        $debtBn = ($equityBn !== null && $equityBn > 0) ? $deRatio * $equityBn : null;
        $netDebtBn = $debtBn;

        $equityPerpBn = $equityBn !== null && $debtBn !== null ? $evPerpBn - $netDebtBn : null;
        $equityExitBn = $equityBn !== null && $debtBn !== null ? $evExitBn - $netDebtBn : null;

        $fairPerSharePerp = ($equityPerpBn !== null && $listedShares !== null && $listedShares > 0)
            ? ($equityPerpBn * 1_000_000_000) / $listedShares
            : null;
        $fairPerShareExit = ($equityExitBn !== null && $listedShares !== null && $listedShares > 0)
            ? ($equityExitBn * 1_000_000_000) / $listedShares
            : null;

        $marketPrice = $trading['latest_close'];
        $midFair = null;
        if ($fairPerSharePerp !== null && $fairPerShareExit !== null) {
            $midFair = ($fairPerSharePerp + $fairPerShareExit) / 2;
        } elseif ($fairPerSharePerp !== null) {
            $midFair = $fairPerSharePerp;
        } elseif ($fairPerShareExit !== null) {
            $midFair = $fairPerShareExit;
        }

        $upside = ($midFair !== null && $marketPrice !== null && $marketPrice > 0)
            ? (($midFair - $marketPrice) / $marketPrice) * 100
            : null;

        $verdict = 'tidak_dapat_dinilai';
        if ($upside !== null) {
            if ($upside > 10) {
                $verdict = 'undervalued';
            } elseif ($upside < -10) {
                $verdict = 'overvalued';
            } else {
                $verdict = 'fairly_valued';
            }
        }

        $verdictId = match ($verdict) {
            'undervalued' => 'undervalued (murah vs model)',
            'overvalued' => 'overvalued (mahal vs model)',
            'fairly_valued' => 'fairly valued (wajar)',
            default => 'tidak cukup data untuk verdict',
        };

        return [
            'company_name' => $resolvedCompanyName,
            'code' => $code,
            'assumptions' => [
                'revenue_growth_annual_pct' => round($growth * 100, 2),
                'npm_pct' => round($npmPct, 2),
                'fcf_conversion_proxy' => $fcfConv,
                'tax_rate_pct' => round($taxRate * 100, 2),
                'risk_free_pct' => round($rf * 100, 2),
                'erp_pct' => round($erp * 100, 2),
                'beta' => round($beta, 3),
                'cost_debt_pretax_pct' => round($costDebtPretax * 100, 2),
                'terminal_growth_pct' => round($terminalG * 100, 2),
                'exit_ev_to_sales' => $exitEvToSales,
                'de_ratio_source' => $deRatio,
                'wacc_decimal' => round($wacc, 4),
                'fcf_note' => 'FCF = laba bersih × faktor konversi (proxy; bukan FCFF dari laporan arus kas).',
            ],
            'wacc_breakdown' => [
                'formula' => 'WACC = We×Re + Wd×Rd×(1−T); We=1/(1+D/E), Wd=(D/E)/(1+D/E); Re=Rf+β×ERP',
                'd_e_ratio' => $deRatio,
                'cost_of_equity_re' => round($rf + $beta * $erp, 4),
                'weights_we_wd' => [
                    'we' => round(1 / (1 + $deRatio), 4),
                    'wd' => round($deRatio / (1 + $deRatio), 4),
                ],
                'wacc' => round($wacc, 4),
            ],
            'historical_sales_cagr_pct' => $cagrPct,
            'projection_5y' => $projection,
            'pv_sum_fcf_bn_idr' => round($pvFcfSum, 4),
            'terminal_perpetuity' => [
                'fcf_year5_bn' => $fcf5,
                'tv_bn_idr' => round($tvPerpBn, 4),
                'pv_tv_bn_idr' => round($pvTvPerpBn, 4),
            ],
            'terminal_exit_multiple' => [
                'revenue_year5_bn' => $rev5,
                'ev_implied_bn_idr' => round($tvExitBn, 4),
                'pv_tv_bn_idr' => round($pvTvExitBn, 4),
            ],
            'enterprise_value_bn_idr' => [
                'perpetuity_method' => round($evPerpBn, 4),
                'exit_multiple_method' => round($evExitBn, 4),
            ],
            'balance_sheet_proxy_bn' => [
                'book_equity' => $equityBn,
                'debt_proxy' => $debtBn,
                'net_debt_proxy' => $netDebtBn,
            ],
            'equity_value_bn_idr' => [
                'perpetuity' => $equityPerpBn !== null ? round($equityPerpBn, 4) : null,
                'exit_multiple' => $equityExitBn !== null ? round($equityExitBn, 4) : null,
            ],
            'per_share_idr' => [
                'fair_perpetuity' => $fairPerSharePerp !== null ? round($fairPerSharePerp, 2) : null,
                'fair_exit_multiple' => $fairPerShareExit !== null ? round($fairPerShareExit, 2) : null,
                'fair_midpoint' => $midFair !== null ? round($midFair, 2) : null,
                'market_price' => $marketPrice,
                'listed_shares' => $listedShares,
                'upside_pct_vs_mid' => $upside !== null ? round($upside, 2) : null,
            ],
            'verdict' => $verdict,
            'verdict_id' => $verdictId,
            'external_finnhub' => $external,
            'internal_trading' => $trading,
        ];
    }

    /**
     * Sensitivitas fair value (midpoint) terhadap WACC dan pertumbuhan terminal.
     *
     * @return list<array<string, mixed>>
     */
    public function runSensitivity(
        string $code,
        array $baseOverrides = [],
        ?float $waccShiftPctPoints = null,
        ?float $terminalShiftPctPoints = null,
    ): array {
        $base = $this->buildFullDcfModel($code, $baseOverrides);
        if (isset($base['error'])) {
            return [['error' => $base['error']]];
        }

        $waccShift = $waccShiftPctPoints ?? 0.01;
        $gShift = $terminalShiftPctPoints ?? 0.005;

        $baseWacc = (float) ($base['assumptions']['wacc_decimal'] ?? 0.1);
        $baseG = (float) ($base['assumptions']['terminal_growth_pct'] ?? 2.5) / 100;

        $rows = [];
        foreach ([-$waccShift, 0, $waccShift] as $dw) {
            foreach ([-$gShift, 0, $gShift] as $dg) {
                $o = $baseOverrides;
                $newG = max(0.005, min(0.06, $baseG + $dg));
                $o['terminal_growth_pct'] = $newG * 100;
                $waccTarget = max(0.04, min(0.25, $baseWacc + $dw));
                $o['wacc_override_decimal'] = $waccTarget;
                $m = $this->buildFullDcfModel($code, $o);
                $mid = $m['per_share_idr']['fair_midpoint'] ?? null;
                $rows[] = [
                    'wacc' => round($waccTarget, 4),
                    'terminal_growth_pct' => round($newG * 100, 2),
                    'fair_midpoint_idr' => $mid,
                ];
            }
        }

        return $rows;
    }

    private function computeWacc(float $deRatio, float $beta, float $rf, float $erp, float $costDebtPretax, float $taxRate): float
    {
        $re = $rf + $beta * $erp;
        $we = 1 / (1 + $deRatio);
        $wd = $deRatio / (1 + $deRatio);

        return $we * $re + $wd * $costDebtPretax * (1 - $taxRate);
    }

    private function capGrowth(float $cagrPercent): float
    {
        $g = $cagrPercent / 100;

        return max(0.02, min(0.18, $g));
    }

    /**
     * @param  list<array<string, mixed>>  $points
     */
    private function resolveNpm(array $points, FinancialRatio $latest): float
    {
        if ($latest->npm !== null && (float) $latest->npm > 0) {
            return (float) $latest->npm;
        }

        return 12.0;
    }

    private function latestListedShares(string $code): ?float
    {
        $latestDate = TradingInfo::query()->where('kode_emiten', $code)->max('date');
        if (! $latestDate) {
            return null;
        }

        $row = TradingInfo::query()
            ->where('kode_emiten', $code)
            ->whereDate('date', $latestDate)
            ->orderByDesc('id')
            ->first();

        if (! $row || $row->listed_shares === null) {
            return null;
        }

        return (float) $row->listed_shares;
    }
}
