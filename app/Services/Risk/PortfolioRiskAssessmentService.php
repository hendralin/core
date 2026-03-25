<?php

namespace App\Services\Risk;

use App\Models\FinancialRatio;
use App\Models\TradingInfo;
use App\Services\Screening\ExternalFundamentalService;
use App\Services\Screening\InternalScreeningDataService;

/**
 * Hybrid portfolio risk metrics: internal DB (IDX) + optional Finnhub enrichment.
 * Methodology is disclosed in outputs; proxies are used where bond duration / FX breakdown is unavailable.
 */
class PortfolioRiskAssessmentService
{
    private const MAX_POSITIONS = 15;

    private const WEIGHT_TOLERANCE = 0.5;

    private const TRADING_LOOKBACK_DAYS = 320;

    private const MIN_OVERLAP_DAYS = 30;

    public function __construct(
        private InternalScreeningDataService $internal,
        private ExternalFundamentalService $external,
    ) {}

    /**
     * Parse multiline portfolio: one line per holding, e.g. "BBCA 25" or "BBCA,25".
     *
     * @return array{
     *   ok: bool,
     *   positions: list<array{code: string, weight_pct: float}>,
     *   weight_sum_pct: float,
     *   errors: list<string>,
     *   warnings: list<string>
     * }
     */
    public function parsePortfolioInput(string $raw): array
    {
        $errors = [];
        $warnings = [];
        $positions = [];

        $lines = preg_split('/\R/u', $raw) ?: [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            $parts = preg_split('/[\s,;|]+/u', $line, -1, PREG_SPLIT_NO_EMPTY);
            if (count($parts) < 2) {
                $errors[] = "Baris tidak valid (butuh kode dan persen): {$line}";

                continue;
            }

            $code = strtoupper(trim($parts[0]));
            $w = str_replace(',', '.', (string) $parts[1]);
            if (! is_numeric($w)) {
                $errors[] = "Bobot bukan angka untuk {$code}";

                continue;
            }

            $weight = (float) $w;
            if ($weight < 0 || $weight > 100) {
                $errors[] = "Bobot harus 0–100 untuk {$code}";

                continue;
            }

            if (isset($positions[$code])) {
                $warnings[] = "Kode {$code} duplikat; bobot digabung.";
                $positions[$code]['weight_pct'] += $weight;
            } else {
                $positions[$code] = ['code' => $code, 'weight_pct' => $weight];
            }
        }

        $list = array_values($positions);
        if (count($list) > self::MAX_POSITIONS) {
            $errors[] = 'Maksimal '.self::MAX_POSITIONS.' posisi.';
        }

        $sum = array_sum(array_column($list, 'weight_pct'));
        if ($sum <= 0) {
            $errors[] = 'Total bobot harus lebih dari 0%.';
        } elseif (abs($sum - 100) > self::WEIGHT_TOLERANCE) {
            $warnings[] = 'Total bobot '.round($sum, 2).'% (target ~100%; toleransi ±'.self::WEIGHT_TOLERANCE.'%).';
        }

        return [
            'ok' => $errors === [] && $list !== [],
            'positions' => $list,
            'weight_sum_pct' => round($sum, 4),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Full risk snapshot for memo + agent tools.
     *
     * @param  list<array{code: string, weight_pct: float}>  $positions
     * @return array<string, mixed>
     */
    public function buildPortfolioRiskSnapshot(array $positions, ?float $totalPortfolioValueIdr = null): array
    {
        if ($positions === []) {
            return ['error' => 'Portofolio kosong.'];
        }

        $codes = array_column($positions, 'code');
        $weights = [];
        foreach ($positions as $p) {
            $weights[$p['code']] = $p['weight_pct'] / 100;
        }

        $sumW = array_sum($weights);
        if ($sumW <= 0) {
            return ['error' => 'Bobot tidak valid.'];
        }

        foreach ($weights as $c => $w) {
            $weights[$c] = $w / $sumW;
        }

        $perCodeMeta = [];
        foreach ($codes as $code) {
            $perCodeMeta[$code] = $this->buildPerCodeRiskMeta($code);
        }

        $corrBundle = $this->correlationFromTradingHistory($codes);
        $sectorConc = $this->sectorConcentration($positions, $perCodeMeta);
        $geoCurrency = $this->geographicCurrencyExposure($positions, $perCodeMeta);
        $rateSens = $this->interestRateSensitivityProxy($positions, $perCodeMeta);
        $liquidity = $this->liquidityRatings($positions, $perCodeMeta, $totalPortfolioValueIdr);
        $singleStock = $this->singleStockRiskAndSizing($positions, $corrBundle);
        $tail = $this->tailRiskScenarios($positions, $weights, $corrBundle);

        $ext = $this->external->enrichQuotes($codes);

        return [
            'methodology_note' => [
                'correlation' => 'Korelasi dari return harian harga tutup internal (trading_infos), window overlap terbatas.',
                'rates' => 'Sensitivitas suku bunga memakai proxy sektor + leverage (bukan durasi obligasi emiten).',
                'tail' => 'Estimasi tail risk dari volatilitas historis portofolio; probabilitas skenario bersifat indikatif.',
                'currency' => 'Emiten IDX umumnya berdenominasi IDR; ekspor/impor tidak dimodelkan per baris.',
            ],
            'positions_normalized' => array_map(fn ($c) => [
                'code' => $c,
                'weight_pct' => round($weights[$c] * 100, 4),
            ], $codes),
            'total_portfolio_value_idr' => $totalPortfolioValueIdr,
            'correlation' => $corrBundle,
            'sector_concentration' => $sectorConc,
            'geographic_currency' => $geoCurrency,
            'interest_rate_sensitivity' => $rateSens,
            'liquidity' => $liquidity,
            'single_stock_position_sizing' => $singleStock,
            'tail_risk' => $tail,
            'external_finnhub' => $ext,
            'heat_map_summary' => $this->buildHeatMapSummary($positions, $weights, $perCodeMeta, $liquidity, $rateSens),
        ];
    }

    /**
     * Recession stress: rough portfolio drawdown estimate from historical vol + correlation.
     *
     * @param  list<array{code: string, weight_pct: float}>  $positions
     * @return array<string, mixed>
     */
    public function runRecessionStressTest(array $positions, ?float $marketShockPct = -35.0): array
    {
        $parsed = $this->normalizePositions($positions);
        if ($parsed === []) {
            return ['error' => 'Portofolio kosong.'];
        }

        $codes = array_column($parsed, 'code');
        $weights = [];
        foreach ($parsed as $p) {
            $weights[$p['code']] = $p['weight_pct'] / 100;
        }
        $sumW = array_sum($weights);
        foreach ($weights as $c => $w) {
            $weights[$c] = $w / $sumW;
        }

        $corrBundle = $this->correlationFromTradingHistory($codes);
        $vols = $corrBundle['annualized_volatility_pct'] ?? [];
        $cov = $corrBundle['covariance_matrix'] ?? null;

        $portfolioVol = null;
        if (is_array($cov) && isset($corrBundle['ordered_codes'])) {
            $ordered = $corrBundle['ordered_codes'];
            $wVec = [];
            foreach ($ordered as $c) {
                $wVec[] = $weights[$c] ?? 0;
            }
            $portfolioVol = $this->portfolioVolatilityAnnualPct($wVec, $cov);
        }

        $shock = (float) $marketShockPct;
        $impliedDrawdownPct = null;
        if ($portfolioVol !== null && $portfolioVol > 0) {
            $betaToMarket = 1.0;
            $impliedDrawdownPct = $shock * $betaToMarket;
            $impliedDrawdownPct = max(-95, min(0, $impliedDrawdownPct * (1 + $portfolioVol / 100 * 0.15)));
        }

        $worstCaseSimple = 0.0;
        foreach ($weights as $code => $w) {
            $v = $vols[$code] ?? 25.0;
            $worstCaseSimple += $w * ($shock * (0.8 + min(0.5, $v / 100)));
        }

        return [
            'scenario' => 'recession_equity_shock',
            'market_shock_pct' => $shock,
            'estimated_portfolio_drawdown_pct' => $impliedDrawdownPct !== null
                ? round($impliedDrawdownPct, 2)
                : round($worstCaseSimple, 2),
            'portfolio_annual_volatility_pct' => $portfolioVol !== null ? round($portfolioVol, 2) : null,
            'disclaimer' => 'Stress test indikatif; bukan prediksi; sensitif terhadap data historis dan asumsi shock.',
        ];
    }

    /**
     * Simple risk-parity-ish rebalance toward sector caps and max single-name weight.
     *
     * @param  list<array{code: string, weight_pct: float}>  $positions
     * @return array<string, mixed>
     */
    public function proposeRebalancingPlan(array $positions, float $maxSingleNamePct = 25.0, float $maxSectorPct = 40.0): array
    {
        $parsed = $this->normalizePositions($positions);
        if ($parsed === []) {
            return ['error' => 'Portofolio kosong.'];
        }

        $meta = [];
        foreach ($parsed as $p) {
            $meta[$p['code']] = $this->buildPerCodeRiskMeta($p['code']);
        }

        $sectorWeights = [];
        foreach ($parsed as $p) {
            $s = $meta[$p['code']]['sector'] ?? 'Tidak diketahui';
            $sectorWeights[$s] = ($sectorWeights[$s] ?? 0) + $p['weight_pct'];
        }

        $targets = [];
        $n = count($parsed);
        $equal = 100 / max(1, $n);
        foreach ($parsed as $p) {
            $code = $p['code'];
            $w = $p['weight_pct'];
            $s = $meta[$code]['sector'] ?? 'Tidak diketahui';
            $adj = $w;
            if ($w > $maxSingleNamePct) {
                $adj = $maxSingleNamePct;
            }
            if (($sectorWeights[$s] ?? 0) > $maxSectorPct && $w > $equal) {
                $adj = min($adj, $equal + 5);
            }
            $targets[] = [
                'code' => $code,
                'current_weight_pct' => round($w, 2),
                'suggested_weight_pct' => round(max(0, min(100, $adj)), 2),
                'rationale' => $w > $maxSingleNamePct ? 'Kurangi konsentrasi nama' : 'Pertahankan/mendekati equal weight',
            ];
        }

        $sum = array_sum(array_column($targets, 'suggested_weight_pct'));
        if ($sum > 0 && abs($sum - 100) > 0.01) {
            $factor = 100 / $sum;
            foreach ($targets as &$t) {
                $t['suggested_weight_pct'] = round($t['suggested_weight_pct'] * $factor, 2);
            }
            unset($t);
        }

        return [
            'constraints' => [
                'max_single_name_pct' => $maxSingleNamePct,
                'max_sector_pct' => $maxSectorPct,
            ],
            'suggested_allocations' => array_values($targets),
        ];
    }

    /**
     * @param  list<array{code: string, weight_pct: float}>  $positions
     * @return list<array{code: string, weight_pct: float}>
     */
    private function normalizePositions(array $positions): array
    {
        $merged = [];
        foreach ($positions as $p) {
            $code = strtoupper(trim($p['code'] ?? ''));
            if ($code === '') {
                continue;
            }
            $w = (float) ($p['weight_pct'] ?? 0);
            $merged[$code] = ($merged[$code] ?? 0) + $w;
        }

        $out = [];
        foreach ($merged as $code => $w) {
            $out[] = ['code' => $code, 'weight_pct' => $w];
        }

        return $out;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPerCodeRiskMeta(string $code): array
    {
        $ratio = FinancialRatio::query()
            ->audited()
            ->where('code', $code)
            ->orderByDesc('fs_date')
            ->with('stockCompany')
            ->first();

        $snap = $this->internal->latestTradingSnapshot($code);

        $sector = $ratio?->sector ?? null;
        $companyName = $ratio?->stock_name ?? $ratio?->stockCompany?->nama_emiten ?? $code;

        return [
            'code' => $code,
            'company_name' => $companyName,
            'sector' => $sector,
            'de_ratio' => $ratio?->de_ratio !== null ? (float) $ratio->de_ratio : null,
            'npm' => $ratio?->npm !== null ? (float) $ratio->npm : null,
            'latest_close' => $snap['latest_close'] ?? null,
            'latest_date' => $snap['latest_date'] ?? null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function correlationFromTradingHistory(array $codes): array
    {
        $codes = array_values(array_unique(array_map('strtoupper', $codes)));
        $series = [];

        $cutoff = now()->subDays(self::TRADING_LOOKBACK_DAYS)->toDateString();

        foreach ($codes as $code) {
            $rows = TradingInfo::query()
                ->where('kode_emiten', $code)
                ->where('date', '>=', $cutoff)
                ->whereNotNull('close')
                ->orderBy('date')
                ->get(['date', 'close']);

            $byDate = [];
            foreach ($rows as $row) {
                $d = $row->date instanceof \DateTimeInterface
                    ? $row->date->format('Y-m-d')
                    : (string) $row->date;
                $byDate[$d] = (float) $row->close;
            }
            $series[$code] = $byDate;
        }

        $commonDates = null;
        foreach ($series as $byDate) {
            $dates = array_keys($byDate);
            $commonDates = $commonDates === null ? $dates : array_intersect($commonDates, $dates);
        }
        $commonDates = $commonDates ? array_values($commonDates) : [];
        sort($commonDates);

        if (count($commonDates) < self::MIN_OVERLAP_DAYS) {
            return [
                'status' => 'insufficient_overlap',
                'overlap_days' => count($commonDates),
                'min_required' => self::MIN_OVERLAP_DAYS,
                'correlation_matrix' => null,
                'annualized_volatility_pct' => null,
                'covariance_matrix' => null,
                'ordered_codes' => $codes,
            ];
        }

        $returns = [];
        foreach ($codes as $code) {
            $rets = [];
            for ($i = 1; $i < count($commonDates); $i++) {
                $d0 = $commonDates[$i - 1];
                $d1 = $commonDates[$i];
                $p0 = $series[$code][$d0] ?? null;
                $p1 = $series[$code][$d1] ?? null;
                if ($p0 !== null && $p1 !== null && $p0 > 0) {
                    $rets[] = ($p1 / $p0) - 1;
                }
            }
            $returns[$code] = $rets;
        }

        $len = min(array_map('count', $returns));
        if ($len < 20) {
            return [
                'status' => 'insufficient_returns',
                'overlap_days' => count($commonDates),
                'correlation_matrix' => null,
                'annualized_volatility_pct' => null,
                'covariance_matrix' => null,
                'ordered_codes' => $codes,
            ];
        }

        foreach ($codes as $code) {
            $returns[$code] = array_slice($returns[$code], -$len);
        }

        $vols = [];
        foreach ($codes as $code) {
            $vols[$code] = $this->annualizedVolPct($returns[$code]);
        }

        $corrMatrix = [];
        foreach ($codes as $i => $ci) {
            $corrMatrix[$ci] = [];
            foreach ($codes as $j => $cj) {
                if ($i === $j) {
                    $corrMatrix[$ci][$cj] = 1.0;
                } else {
                    $corrMatrix[$ci][$cj] = $this->correlation(
                        $returns[$ci],
                        $returns[$cj]
                    );
                }
            }
        }

        $cov = [];
        foreach ($codes as $i => $ci) {
            $cov[$ci] = [];
            foreach ($codes as $j => $cj) {
                $vi = (($vols[$ci] ?? 0) / 100);
                $vj = (($vols[$cj] ?? 0) / 100);
                $cov[$ci][$cj] = ($corrMatrix[$ci][$cj] ?? 0) * $vi * $vj;
            }
        }

        $covNumeric = [];
        foreach ($codes as $ci) {
            $row = [];
            foreach ($codes as $cj) {
                $row[] = $cov[$ci][$cj] ?? 0;
            }
            $covNumeric[] = $row;
        }

        return [
            'status' => 'ok',
            'overlap_days' => count($commonDates),
            'return_observations' => $len,
            'correlation_matrix' => $corrMatrix,
            'annualized_volatility_pct' => array_map(fn ($v) => round($v, 2), $vols),
            'covariance_matrix' => $covNumeric,
            'ordered_codes' => $codes,
        ];
    }

    /**
     * @param  list<float>  $w  weights summing to 1
     * @param  list<list<float>>  $cov  annual covariance matrix
     */
    private function portfolioVolatilityAnnualPct(array $w, array $cov): float
    {
        $n = count($w);
        $var = 0.0;
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $var += $w[$i] * $w[$j] * (float) ($cov[$i][$j] ?? 0);
            }
        }

        return sqrt(max(0, $var)) * 100;
    }

    /**
     * @param  list<float>  $a
     * @param  list<float>  $b
     */
    private function correlation(array $a, array $b): float
    {
        $n = min(count($a), count($b));
        if ($n < 2) {
            return 0.0;
        }
        $meanA = array_sum($a) / $n;
        $meanB = array_sum($b) / $n;
        $num = 0.0;
        $denA = 0.0;
        $denB = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $da = $a[$i] - $meanA;
            $db = $b[$i] - $meanB;
            $num += $da * $db;
            $denA += $da * $da;
            $denB += $db * $db;
        }
        if ($denA <= 0 || $denB <= 0) {
            return 0.0;
        }

        return $num / sqrt($denA * $denB);
    }

    /**
     * @param  list<float>  $rets
     */
    private function annualizedVolPct(array $rets): float
    {
        $n = count($rets);
        if ($n < 2) {
            return 0.0;
        }
        $mean = array_sum($rets) / $n;
        $var = 0.0;
        foreach ($rets as $r) {
            $var += ($r - $mean) ** 2;
        }
        $var /= ($n - 1);
        $dailyStd = sqrt($var);

        return $dailyStd * sqrt(252) * 100;
    }

    /**
     * @param  list<array{code: string, weight_pct: float}>  $positions
     * @param  array<string, array<string, mixed>>  $perCodeMeta
     * @return array<string, mixed>
     */
    private function sectorConcentration(array $positions, array $perCodeMeta): array
    {
        $bySector = [];
        foreach ($positions as $p) {
            $code = $p['code'];
            $w = $p['weight_pct'];
            $s = $perCodeMeta[$code]['sector'] ?? 'Tidak diketahui';
            $bySector[$s] = ($bySector[$s] ?? 0) + $w;
        }
        arsort($bySector);

        $rows = [];
        foreach ($bySector as $sector => $pct) {
            $rows[] = [
                'sector' => $sector,
                'weight_pct' => round($pct, 2),
            ];
        }

        $hhi = 0.0;
        foreach ($bySector as $pct) {
            $hhi += ($pct / 100) ** 2;
        }

        return [
            'by_sector_pct' => $rows,
            'herfindahl_hirschman_index' => round($hhi, 4),
            'concentration_note' => $hhi > 0.25 ? 'Konsentrasi sektor relatif tinggi (HHI proxy).' : 'Konsentrasi sektor moderat/rendah (HHI proxy).',
        ];
    }

    /**
     * @param  list<array{code: string, weight_pct: float}>  $positions
     * @param  array<string, array<string, mixed>>  $perCodeMeta
     * @return array<string, mixed>
     */
    private function geographicCurrencyExposure(array $positions, array $perCodeMeta): array
    {
        $idrPct = 100.0;
        $notes = [
            'Semua saham IDX diasumsikan bertransaksi dalam IDR; pendapatan bisa berekspor — tidak dimodelkan per emiten di tool ini.',
        ];

        return [
            'primary_listing' => 'Indonesia (IDX)',
            'currency_trading_pct' => [
                ['currency' => 'IDR', 'weight_pct' => round($idrPct, 2)],
            ],
            'geographic_revenue_proxy' => 'Tidak tersedia per baris; gunakan analisis fundamental terpisah untuk ekspor.',
            'fx_risk_summary' => 'Risiko FX utama: depresiasi IDR vs mata uang pendapatan ekspor (kualitatif).',
            'notes' => $notes,
        ];
    }

    /**
     * @param  list<array{code: string, weight_pct: float}>  $positions
     * @param  array<string, array<string, mixed>>  $perCodeMeta
     * @return array<string, mixed>
     */
    private function interestRateSensitivityProxy(array $positions, array $perCodeMeta): array
    {
        $rows = [];
        foreach ($positions as $p) {
            $code = $p['code'];
            $sector = strtolower((string) ($perCodeMeta[$code]['sector'] ?? ''));
            $de = $perCodeMeta[$code]['de_ratio'];

            $base = 0.35;
            if (str_contains($sector, 'keuangan') || str_contains($sector, 'bank')) {
                $base = 0.75;
            } elseif (str_contains($sector, 'properti') || str_contains($sector, 'real estat')) {
                $base = 0.65;
            } elseif (str_contains($sector, 'infrastruktur') || str_contains($sector, 'utilitas')) {
                $base = 0.45;
            }

            $leverageAdj = $de !== null ? min(0.35, max(0, ((float) $de) * 0.08)) : 0.1;
            $score = min(1.0, $base + $leverageAdj);

            $rows[] = [
                'code' => $code,
                'interest_rate_sensitivity_score_0_1' => round($score, 3),
                'label' => $score >= 0.65 ? 'tinggi' : ($score >= 0.45 ? 'sedang' : 'rendah'),
            ];
        }

        return [
            'per_position' => $rows,
            'scale' => '0 = rendah sensitivitas terhadap naiknya suku bunga (proxy), 1 = tinggi',
        ];
    }

    /**
     * @param  list<array{code: string, weight_pct: float}>  $positions
     * @param  array<string, array<string, mixed>>  $perCodeMeta
     * @return array<string, mixed>
     */
    private function liquidityRatings(array $positions, array $perCodeMeta, ?float $totalPortfolioValueIdr): array
    {
        $rows = [];
        foreach ($positions as $p) {
            $code = $p['code'];
            $avgVal = $this->avgDailyValueIdr($code, 20);
            $weight = $p['weight_pct'] / 100;
            $notional = $totalPortfolioValueIdr !== null && $totalPortfolioValueIdr > 0
                ? $totalPortfolioValueIdr * $weight
                : null;

            $adv = $avgVal;
            $daysToLiquidate = ($notional !== null && $adv !== null && $adv > 0)
                ? $notional / $adv
                : null;

            $rating = 'sedang';
            if ($adv !== null && $adv > 5_000_000_000) {
                $rating = 'tinggi';
            } elseif ($adv !== null && $adv < 500_000_000) {
                $rating = 'rendah';
            }

            $rows[] = [
                'code' => $code,
                'avg_daily_value_idr_20d' => $adv !== null ? round($adv, 2) : null,
                'position_notional_idr_estimate' => $notional !== null ? round($notional, 2) : null,
                'estimated_days_to_liquidate_full_position' => $daysToLiquidate !== null ? round($daysToLiquidate, 2) : null,
                'liquidity_rating' => $rating,
            ];
        }

        return ['per_holding' => $rows];
    }

    private function avgDailyValueIdr(string $code, int $days): ?float
    {
        $rows = TradingInfo::query()
            ->where('kode_emiten', $code)
            ->whereNotNull('value')
            ->orderByDesc('date')
            ->limit($days)
            ->pluck('value');

        if ($rows->isEmpty()) {
            return null;
        }

        return (float) $rows->avg();
    }

    /**
     * @param  list<array{code: string, weight_pct: float}>  $positions
     * @param  array<string, array<string, mixed>>  $perCodeMeta
     * @param  array<string, mixed>  $corrBundle
     * @return array<string, mixed>
     */
    private function singleStockRiskAndSizing(array $positions, array $corrBundle): array
    {
        $vols = $corrBundle['annualized_volatility_pct'] ?? [];
        $recs = [];

        foreach ($positions as $p) {
            $code = $p['code'];
            $w = $p['weight_pct'];
            $vol = $vols[$code] ?? null;
            $contrib = $vol !== null ? $w / 100 * $vol : null;

            $suggestedMax = min(25.0, max(5.0, 80 / max(1, count($positions))));
            $recs[] = [
                'code' => $code,
                'weight_pct' => round($w, 2),
                'estimated_annual_volatility_pct' => $vol !== null ? round((float) $vol, 2) : null,
                'marginal_risk_contribution_proxy' => $contrib !== null ? round($contrib, 2) : null,
                'position_sizing_note' => $w > 25
                    ? 'Pertimbangkan trim di bawah 25% per nama kecuali thesis sangat kuat.'
                    : 'Ukuran posisi masih dalam batas wajar untuk diversifikasi IDX.',
                'suggested_max_weight_pct' => round($suggestedMax, 2),
            ];
        }

        return ['per_holding' => $recs];
    }

    /**
     * @param  array<string, float>  $weights  code => weight sum 1
     * @param  array<string, mixed>  $corrBundle
     * @return array<string, mixed>
     */
    private function tailRiskScenarios(array $positions, array $weights, array $corrBundle): array
    {
        $vols = $corrBundle['annualized_volatility_pct'] ?? [];
        $cov = $corrBundle['covariance_matrix'] ?? null;
        $ordered = $corrBundle['ordered_codes'] ?? array_column($positions, 'code');

        $portVol = null;
        if (is_array($cov) && $corrBundle['status'] ?? '' === 'ok') {
            $wVec = [];
            foreach ($ordered as $c) {
                $wVec[] = $weights[$c] ?? 0;
            }
            $portVol = $this->portfolioVolatilityAnnualPct($wVec, $cov);
        }

        $dailyVolDec = $portVol !== null ? ($portVol / 100) / sqrt(252) : null;
        $var95 = $dailyVolDec !== null ? 1.645 * $dailyVolDec * 100 : null;
        $cvar95 = $dailyVolDec !== null ? 1.645 * 1.25 * $dailyVolDec * 100 : null;

        $scenarios = [
            [
                'name' => 'market_gap_down_5pct',
                'estimated_portfolio_impact_pct' => $portVol !== null ? round(-5.0 * min(1.5, ($portVol / 100) / 0.25), 2) : -5.0,
                'probability_note' => 'indikatif (bukan distribusi empiris penuh)',
            ],
            [
                'name' => 'idr_shock_10pct',
                'estimated_portfolio_impact_pct' => -8.0,
                'probability_note' => 'skenario stres FX kualitatif; angka proxy',
            ],
        ];

        return [
            'portfolio_annual_volatility_pct' => $portVol !== null ? round($portVol, 2) : null,
            'var_95_1day_pct' => $var95 !== null ? round($var95, 2) : null,
            'cvar_95_1day_proxy_pct' => $cvar95 !== null ? round($cvar95, 2) : null,
            'tail_scenarios' => $scenarios,
            'disclaimer' => 'VaR/CVaR proxy dari vol portofolio; probabilitas skenario bukan forecast.',
        ];
    }

    /**
     * @param  array<string, float>  $weights
     * @param  array<string, array<string, mixed>>  $perCodeMeta
     * @param  array<string, mixed>  $liquidity
     * @param  array<string, mixed>  $rateSens
     * @return list<array<string, mixed>>
     */
    private function buildHeatMapSummary(
        array $positions,
        array $weights,
        array $perCodeMeta,
        array $liquidity,
        array $rateSens
    ): array {
        $liqMap = [];
        foreach ($liquidity['per_holding'] ?? [] as $row) {
            $liqMap[$row['code']] = $row['liquidity_rating'] ?? '-';
        }
        $rateMap = [];
        foreach ($rateSens['per_position'] ?? [] as $row) {
            $rateMap[$row['code']] = $row['interest_rate_sensitivity_score_0_1'] ?? null;
        }

        $rows = [];
        foreach ($positions as $p) {
            $code = $p['code'];
            $w = $weights[$code] ?? 0;
            $rows[] = [
                'code' => $code,
                'weight_pct' => round($w * 100, 2),
                'sector' => $perCodeMeta[$code]['sector'] ?? '-',
                'liquidity' => $liqMap[$code] ?? '-',
                'rate_sensitivity_0_1' => $rateMap[$code] ?? null,
                'heat_score_1_5' => $this->heatScore($w, $liqMap[$code] ?? 'sedang', $rateMap[$code] ?? 0.5),
            ];
        }

        return $rows;
    }

    private function heatScore(float $weight, string $liquidity, float $rate): int
    {
        $s = 1;
        if ($weight > 0.25) {
            $s += 2;
        } elseif ($weight > 0.15) {
            $s += 1;
        }
        if ($liquidity === 'rendah') {
            $s += 1;
        }
        if ($rate >= 0.65) {
            $s += 1;
        }

        return min(5, max(1, $s));
    }
}
