<?php

namespace App\Services\Valuation;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Hybrid enrichment for DCF: Finnhub quote, metrics (beta, PE), profile.
 * IDX symbols: {CODE}.JK
 */
class ExternalValuationEnrichmentService
{
    public function isConfigured(): bool
    {
        return (bool) config('services.finnhub.key');
    }

    /**
     * @return array{configured: bool, code: string, symbol: string, quote?: array<string, mixed>, metric?: array<string, mixed>, profile?: array<string, mixed>, beta?: float|null, error?: string}
     */
    public function enrichDcf(string $code): array
    {
        $key = config('services.finnhub.key');
        $code = strtoupper(trim($code));
        $symbol = $code.'.JK';

        if (! $key) {
            return [
                'configured' => false,
                'code' => $code,
                'symbol' => $symbol,
                'error' => 'FINNHUB_API_KEY tidak diset',
            ];
        }

        try {
            $quote = Http::timeout(15)
                ->get('https://finnhub.io/api/v1/quote', [
                    'symbol' => $symbol,
                    'token' => $key,
                ]);

            $metric = Http::timeout(15)
                ->get('https://finnhub.io/api/v1/stock/metric', [
                    'symbol' => $symbol,
                    'metric' => 'all',
                    'token' => $key,
                ]);

            $profile = Http::timeout(15)
                ->get('https://finnhub.io/api/v1/stock/profile2', [
                    'symbol' => $symbol,
                    'token' => $key,
                ]);

            $q = $quote->successful() ? $quote->json() : [];
            $m = $metric->successful() ? ($metric->json()['metric'] ?? []) : [];
            $p = $profile->successful() ? $profile->json() : [];

            $beta = null;
            if (isset($m['beta'])) {
                $beta = (float) $m['beta'];
            }

            return [
                'configured' => true,
                'code' => $code,
                'symbol' => $symbol,
                'beta' => $beta,
                'quote' => [
                    'current_price' => $q['c'] ?? null,
                    'high_52w' => $q['h'] ?? null,
                    'low_52w' => $q['l'] ?? null,
                    'previous_close' => $q['pc'] ?? null,
                ],
                'metric' => [
                    'pe_ttm' => $m['peTTM'] ?? null,
                    'pb' => $m['pbAnnual'] ?? null,
                    'dividend_yield_indicated' => $m['dividendYieldIndicatedAnnual'] ?? null,
                ],
                'profile' => [
                    'name' => $p['name'] ?? null,
                    'ticker' => $p['ticker'] ?? null,
                    'exchange' => $p['exchange'] ?? null,
                    'industry' => $p['finnhubIndustry'] ?? null,
                    'ipo' => $p['ipo'] ?? null,
                ],
            ];
        } catch (\Throwable $e) {
            Log::warning('ExternalValuationEnrichmentService enrichDcf failed', [
                'code' => $code,
                'message' => $e->getMessage(),
            ]);

            return [
                'configured' => true,
                'code' => $code,
                'symbol' => $symbol,
                'error' => $e->getMessage(),
            ];
        }
    }
}
