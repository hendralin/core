<?php

namespace App\Services\Screening;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Optional enrichment when FINNHUB_API_KEY is set. IDX symbols use {CODE}.JK
 */
class ExternalFundamentalService
{
    public function isConfigured(): bool
    {
        return (bool) config('services.finnhub.key');
    }

    /**
     * @param  list<string>  $codes  IDX codes without .JK
     * @return array{configured: bool, items: list<array<string, mixed>>}
     */
    public function enrichQuotes(array $codes): array
    {
        $key = config('services.finnhub.key');
        if (! $key || $codes === []) {
            return ['configured' => false, 'items' => []];
        }

        $items = [];
        foreach (array_slice(array_unique($codes), 0, 10) as $code) {
            $code = strtoupper(trim($code));
            $symbol = $code.'.JK';
            try {
                $quote = Http::timeout(12)
                    ->get('https://finnhub.io/api/v1/quote', [
                        'symbol' => $symbol,
                        'token' => $key,
                    ]);

                if (! $quote->successful()) {
                    $items[] = [
                        'code' => $code,
                        'symbol' => $symbol,
                        'error' => 'quote_http_'.$quote->status(),
                    ];

                    continue;
                }

                $q = $quote->json();

                $metric = Http::timeout(12)
                    ->get('https://finnhub.io/api/v1/stock/metric', [
                        'symbol' => $symbol,
                        'metric' => 'all',
                        'token' => $key,
                    ]);

                $m = $metric->successful() ? $metric->json() : [];

                $items[] = [
                    'code' => $code,
                    'symbol' => $symbol,
                    'source' => 'finnhub',
                    'quote' => [
                        'current_price' => $q['c'] ?? null,
                        'high_52w' => $q['h'] ?? null,
                        'low_52w' => $q['l'] ?? null,
                        'previous_close' => $q['pc'] ?? null,
                        'timestamp' => $q['t'] ?? null,
                    ],
                    'metric' => [
                        'pe_ttm' => $m['metric']['peTTM'] ?? null,
                        'pb' => $m['metric']['pbAnnual'] ?? null,
                        'dividend_yield_indicated' => $m['metric']['dividendYieldIndicatedAnnual'] ?? null,
                    ],
                ];
            } catch (\Throwable $e) {
                Log::warning('ExternalFundamentalService enrichQuotes failed', [
                    'code' => $code,
                    'message' => $e->getMessage(),
                ]);
                $items[] = [
                    'code' => $code,
                    'symbol' => $symbol,
                    'error' => 'exception',
                ];
            }
        }

        return ['configured' => true, 'items' => $items];
    }
}
