<?php

namespace App\Console\Commands;

use App\Models\StockSignal;
use App\Models\TradingInfo;
use App\Models\GoapiStockPrice;
use Illuminate\Console\Command;
use App\Models\GoapiGetStockPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FetchAndAnalyzeStockBreakthroughs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:fetch-and-analyze-breakthroughs
                            {--batch-size=50 : Number of stock codes to process per batch}
                            {--test : Run in test mode (no actual API calls or database saves)}
                            {--market-cap-max=5000000000000 : Maximum market cap filter (default: 5T)}
                            {--auto-publish : Auto-publish detected signals}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch stock prices from GoAPI and automatically analyze for value breakthroughs, updating stock_signals table';

    protected array $stats = [
        'total_symbols' => 0,
        'batches_processed' => 0,
        'api_calls_made' => 0,
        'records_saved' => 0,
        'breakthrough_signals_detected' => 0,
        'signals_saved' => 0,
        'signals_skipped' => 0,
        'errors' => [],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $startTime = microtime(true);

        $batchSize = (int) $this->option('batch-size');
        $isTestMode = $this->option('test');
        $marketCapMax = (float) $this->option('market-cap-max');
        $autoPublish = $this->option('auto-publish');

        if ($isTestMode) {
            $this->warn('Running in TEST MODE - No actual API calls or database saves will be made');
        }

        // Get yesterday's date
        $today = now()->toDateString();
        $yesterday = now()->previousWeekday()->toDateString();
        $this->info("Fetching stock symbols for date: {$today}");

        // Query kode_emiten from trading_infos where date = yesterday
        $stockCodes = TradingInfo::where('date', $yesterday)
            ->select('kode_emiten')
            ->distinct()
            ->pluck('kode_emiten')
            ->toArray();

        if (empty($stockCodes)) {
            Log::info('No stock codes found for date ' . $yesterday);
            $this->warn("No stock codes found for date {$yesterday}");
            return Command::SUCCESS;
        }

        $this->stats['total_symbols'] = count($stockCodes);
        $this->info("Found {$this->stats['total_symbols']} stock symbols to process");

        // Migrate data from goapi_get_stock_prices to goapi_stock_prices
        // $timeFetch = now()->toDateTimeString();
        // GoapiGetStockPrice::query()
        //     ->orderBy('id')
        //     ->chunkById(100, function ($chunk) use ($timeFetch) {
        //         foreach ($chunk as $item) {
        //             GoapiStockPrice::create([
        //                 'symbol' => $item->symbol,
        //                 'date' => $timeFetch,
        //                 'open' => $item->open,
        //                 'high' => $item->high,
        //                 'low' => $item->low,
        //                 'close' => $item->close,
        //                 'volume' => $item->volume,
        //                 'change' => $item->change,
        //                 'change_pct' => $item->change_pct,
        //                 'value' => $item->value,
        //             ]);
        //         }
        //     });

        // $this->info('Migrated data from goapi_get_stock_prices to goapi_stock_prices');

        // Truncate table before processing to ensure fresh data
        GoapiGetStockPrice::truncate();
        $this->info('Truncated goapi_get_stock_prices table');

        // Process in batches
        $batches = array_chunk($stockCodes, $batchSize);
        $progressBar = $this->output->createProgressBar(count($batches));
        $progressBar->start();

        foreach ($batches as $batch) {
            try {
                $this->processBatch($batch, $isTestMode, $marketCapMax, $autoPublish);
                $this->stats['batches_processed']++;
            } catch (\Exception $e) {
                $this->stats['errors'][] = "Batch processing error: {$e->getMessage()}";
                Log::error('FetchAndAnalyzeStockBreakthroughs batch error', [
                    'batch' => $batch,
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->showSummary();

        // Log execution time
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        Log::info("FetchAndAnalyzeStockBreakthroughs command completed", [
            'execution_time_seconds' => $executionTime,
            'total_symbols' => $this->stats['total_symbols'],
            'batches_processed' => $this->stats['batches_processed'],
            'api_calls_made' => $this->stats['api_calls_made'],
            'records_saved' => $this->stats['records_saved'],
            'breakthrough_signals_detected' => $this->stats['breakthrough_signals_detected'],
            'signals_saved' => $this->stats['signals_saved'],
            'signals_skipped' => $this->stats['signals_skipped'],
            'errors_count' => count($this->stats['errors']),
            'test_mode' => $isTestMode,
            'auto_publish' => $autoPublish
        ]);

        $this->info("Command completed in {$executionTime} seconds");

        return Command::SUCCESS;
    }

    /**
     * Process a batch of stock codes
     */
    private function processBatch(array $stockCodes, bool $isTestMode, float $marketCapMax, bool $autoPublish): void
    {
        $symbols = implode(',', $stockCodes);

        if ($isTestMode) {
            $this->info("TEST: Would fetch data for symbols: {$symbols}");
            $this->stats['api_calls_made']++;
            return;
        }

        try {
            // Make API call to GoAPI
            $goapiKey = env('GOAPI_KEY');
            $goapiBaseUrl = env('GOAPI_BASE_URL');

            $response = Http::timeout(30)->withHeaders([
                'Accept' => 'application/json',
                'X-API-KEY' => $goapiKey
            ])->get("{$goapiBaseUrl}/stock/idx/prices", [
                'symbols' => $symbols
            ]);

            $this->stats['api_calls_made']++;

            if (!$response->successful()) {
                throw new \Exception("API call failed with status {$response->status()}: {$response->body()}");
            }

            $data = $response->json();

            // Process and save the data
            $savedRecords = $this->saveStockPrices($data);

            // After saving, analyze for breakthroughs
            if ($savedRecords > 0) {
                $this->analyzeForBreakthroughs($marketCapMax, $autoPublish);
            }
        } catch (\Exception $e) {
            $this->stats['errors'][] = "API call failed for symbols {$symbols}: {$e->getMessage()}";
            Log::error('FetchAndAnalyzeStockBreakthroughs API error', [
                'symbols' => $symbols,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Save stock price data to database
     */
    private function saveStockPrices(array $data): int
    {
        if (!isset($data['data']['results']) || !is_array($data['data']['results'])) {
            throw new \Exception('Invalid API response format');
        }

        $records = [];
        $now = now();

        foreach ($data['data']['results'] as $stockData) {
            if (!isset($stockData['symbol'])) {
                continue; // Skip invalid records
            }

            $close = $this->parseDecimal($stockData['close'] ?? null);
            $volume = $this->parseDecimal($stockData['volume'] ?? null);

            $record = [
                'symbol' => $stockData['symbol'],
                'date' => $stockData['date'] ?? now()->toDateString(),
                'open' => $this->parseDecimal($stockData['open'] ?? null),
                'high' => $this->parseDecimal($stockData['high'] ?? null),
                'low' => $this->parseDecimal($stockData['low'] ?? null),
                'close' => $close,
                'volume' => $volume,
                'change' => $this->parseDecimal($stockData['change'] ?? null),
                'change_pct' => $this->parseDecimal($stockData['change_pct'] ?? null),
                'value' => ($close && $volume) ? $close * $volume : 0, // close x volume
            ];

            $records[] = $record;
        }

        if (!empty($records)) {
            // Use upsert to avoid duplicates
            GoapiGetStockPrice::upsert(
                $records,
                ['symbol'], // Unique keys
                ['open', 'high', 'low', 'close', 'volume', 'change', 'change_pct', 'value'] // Columns to update
            );

            // Also save to GoapiStockPrice table with current timestamp
            $currentTime = now()->toDateTimeString();
            $stockPriceRecords = array_map(function ($record) use ($currentTime) {
                return array_merge($record, ['date' => $currentTime]);
            }, $records);

            GoapiStockPrice::upsert(
                $stockPriceRecords,
                ['symbol'], // Unique keys
                ['date', 'open', 'high', 'low', 'close', 'volume', 'change', 'change_pct', 'value'] // Columns to update
            );

            $savedCount = count($records);
            $this->stats['records_saved'] += $savedCount;

            return $savedCount;
        }

        return 0;
    }

    /**
     * Analyze recent data for value breakthroughs
     */
    private function analyzeForBreakthroughs(float $marketCapMax, bool $autoPublish): void
    {
        try {
            // Query to find stocks that just hit 100B+ value for the first time in last 200 days
            $breakthroughs = $this->findValueBreakthroughs($marketCapMax);

            if (empty($breakthroughs)) {
                return; // No breakthroughs found
            }

            $this->stats['breakthrough_signals_detected'] += count($breakthroughs);

            // Process each breakthrough signal
            foreach ($breakthroughs as $breakthrough) {
                $this->processBreakthroughSignal($breakthrough, $autoPublish);
            }
        } catch (\Exception $e) {
            $this->stats['errors'][] = "Breakthrough analysis error: {$e->getMessage()}";
            Log::error('FetchAndAnalyzeStockBreakthroughs analysis error', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Find stocks that just hit 100B+ value for the first time in last 200 days
     */
    private function findValueBreakthroughs(float $marketCapMax): array
    {
        // Get today's date (most recent data from API)
        $today = now()->toDateString();

        $query = "
            SELECT
                r.kode_emiten,
                r.market_cap,
                fr.price_bv AS pbv,
                fr.per AS per,
                r.date AS hit_date,
                r.value AS hit_value,
                r.close AS hit_close,
                r.volume AS hit_volume,
                -- Get H-1 data
                b.Date AS before_date,
                b.Value AS before_value,
                b.Close AS before_close,
                b.Volume AS before_volume,
                -- Get H+1 data (if available)
                a.Date AS after_date,
                a.Value AS after_value,
                a.Close AS after_close,
                a.Volume AS after_volume
            FROM (
                -- Get today's data from the freshly fetched API data
                SELECT
                    g.symbol as kode_emiten,
                    g.date,
                    g.value,
                    g.close,
                    g.volume,
                    (g.close * t.Listed_Shares) AS market_cap
                FROM goapi_get_stock_prices g
                LEFT JOIN trading_infos t ON t.kode_emiten = g.symbol
                    AND t.Date = (
                        SELECT MAX(Date) FROM trading_infos
                        WHERE kode_emiten = g.symbol
                    )
                WHERE g.date = ?
                  AND g.value >= 100000000000 -- 100B threshold
                  AND (g.close * t.Listed_Shares) < ? -- Market cap filter
            ) r
            -- Filter out stocks that already hit 100B+ before today
            LEFT JOIN (
                SELECT DISTINCT kode_emiten
                FROM trading_infos
                WHERE Date < ?
                  AND Value >= 100000000000
            ) h ON h.kode_emiten = r.kode_emiten
            -- Join with financial ratios
            LEFT JOIN financial_ratios fr ON fr.code = r.kode_emiten
                AND fr.price_bv > 0
                AND fr.per > 0
                AND fr.fs_date = (
                    SELECT MAX(fs_date)
                    FROM financial_ratios
                    WHERE code = r.kode_emiten
                      AND price_bv > 0
                      AND per > 0
                )
            -- Get H-1 data
            LEFT JOIN trading_infos b
                ON b.kode_emiten = r.kode_emiten
               AND b.Date = (
                    SELECT MAX(Date)
                    FROM trading_infos
                    WHERE kode_emiten = r.kode_emiten
                      AND Date < r.date
               )
            -- Get H+1 data
            LEFT JOIN trading_infos a
                ON a.kode_emiten = r.kode_emiten
               AND a.Date = (
                    SELECT MIN(Date)
                    FROM trading_infos
                    WHERE kode_emiten = r.kode_emiten
                      AND Date > r.date
               )
            WHERE h.kode_emiten IS NULL
        ";

        return DB::select($query, [$today, $marketCapMax, $today]);
    }

    /**
     * Process a breakthrough signal and save to stock_signals table
     */
    private function processBreakthroughSignal($breakthrough, bool $autoPublish): void
    {
        try {
            // Check if signal already exists
            $existingSignal = StockSignal::where('kode_emiten', $breakthrough->kode_emiten)
                ->where('hit_date', $breakthrough->hit_date)
                ->where('signal_type', 'value_breakthrough')
                ->first();

            if ($existingSignal) {
                $this->stats['signals_skipped']++;
                Log::info('Breakthrough signal already exists', [
                    'kode_emiten' => $breakthrough->kode_emiten,
                    'hit_date' => $breakthrough->hit_date
                ]);
                return;
            }

            // Create new signal
            $signal = StockSignal::create([
                'signal_type' => 'value_breakthrough',
                'kode_emiten' => $breakthrough->kode_emiten,
                'market_cap' => $breakthrough->market_cap,
                'pbv' => $breakthrough->pbv,
                'per' => $breakthrough->per,

                // H-1 data
                'before_date' => $breakthrough->before_date,
                'before_value' => $breakthrough->before_value,
                'before_close' => $breakthrough->before_close,
                'before_volume' => $breakthrough->before_volume,

                // H data (hit)
                'hit_date' => $breakthrough->hit_date,
                'hit_value' => $breakthrough->hit_value,
                'hit_close' => $breakthrough->hit_close,
                'hit_volume' => $breakthrough->hit_volume,

                // H+1 data
                'after_date' => $breakthrough->after_date,
                'after_value' => $breakthrough->after_value,
                'after_close' => $breakthrough->after_close,
                'after_volume' => $breakthrough->after_volume,

                // Status
                'status' => $autoPublish ? 'published' : 'draft',
                'published_at' => $autoPublish ? now() : null,

                // Generate recommendation
                'recommendation' => $this->generateRecommendation($breakthrough),
            ]);

            $this->stats['signals_saved']++;

            Log::info('New breakthrough signal detected and saved', [
                'id' => $signal->id,
                'kode_emiten' => $breakthrough->kode_emiten,
                'hit_date' => $breakthrough->hit_date,
                'hit_value' => $breakthrough->hit_value,
                'market_cap' => $breakthrough->market_cap,
                'status' => $signal->status
            ]);

            // Send WhatsApp message to the Group
            $this->sendWhatsAppMessage($signal, $breakthrough);
        } catch (\Exception $e) {
            $this->stats['errors'][] = "Failed to save signal for {$breakthrough->kode_emiten}: {$e->getMessage()}";
            Log::error('Failed to save breakthrough signal', [
                'kode_emiten' => $breakthrough->kode_emiten,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate recommendation text based on signal data
     */
    private function generateRecommendation($result): string
    {
        $recommendation = "Saham {$result->kode_emiten} menunjukkan breakthrough value di atas 100 miliar untuk pertama kalinya dalam 200 hari terakhir.";

        if ($result->pbv && $result->per) {
            $recommendation .= " Dengan PBV {$result->pbv}x dan PER {$result->per}x,";
        }

        if ($result->market_cap) {
            $formattedCap = $this->formatCurrency($result->market_cap);
            $recommendation .= " market cap {$formattedCap}.";
        }

        $recommendation .= " Monitor perkembangan selanjutnya untuk potensi investasi.";

        return $recommendation;
    }

    /**
     * Format currency values
     */
    private function formatCurrency($value): string
    {
        if ($value >= 1000000000000) { // Triliun
            return number_format($value / 1000000000000, 2) . 'T';
        } elseif ($value >= 1000000000) { // Miliar
            return number_format($value / 1000000000, 2) . 'B';
        } elseif ($value >= 1000000) { // Juta
            return number_format($value / 1000000, 2) . 'M';
        } else {
            return number_format($value);
        }
    }

    /**
     * Parse decimal value safely
     */
    private function parseDecimal($value): ?float
    {
        if (is_null($value) || $value === '' || strtolower($value) === 'nan') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    /**
     * Show processing summary
     */
    private function showSummary(): void
    {
        $this->info('Processing completed!');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Symbols', number_format($this->stats['total_symbols'])],
                ['Batches Processed', number_format($this->stats['batches_processed'])],
                ['API Calls Made', number_format($this->stats['api_calls_made'])],
                ['Records Saved', number_format($this->stats['records_saved'])],
                ['Breakthrough Signals Detected', number_format($this->stats['breakthrough_signals_detected'])],
                ['Signals Saved (New)', number_format($this->stats['signals_saved'])],
                ['Signals Skipped (Exists)', number_format($this->stats['signals_skipped'])],
                ['Errors', count($this->stats['errors'])],
            ]
        );

        // Show errors if any
        if (count($this->stats['errors']) > 0) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach (array_slice($this->stats['errors'], 0, 10) as $error) {
                $this->line("  - {$error}");
            }
            if (count($this->stats['errors']) > 10) {
                $this->line("  ... and " . (count($this->stats['errors']) - 10) . " more errors");
            }
        }
    }

    /**
     * Send WhatsApp message for new breakthrough signal
     */
    private function sendWhatsAppMessage($signal, $breakthrough): void
    {
        try {
            $formattedValue = $this->formatCurrency($breakthrough->hit_value);
            $formattedMarketCap = $this->formatCurrency($breakthrough->market_cap);

            $message = "🚀 *BREAKTHROUGH SIGNAL DETECTED!*\n\n" .
                "📈 *Stock*: {$breakthrough->kode_emiten}\n" .
                "💰 *Value*: {$formattedValue}\n" .
                "🏢 *Market Cap*: {$formattedMarketCap}\n" .
                "📊 *PBV*: {$breakthrough->pbv}x\n" .
                "📊 *PER*: {$breakthrough->per}x\n" .
                "📅 *Date*: {$breakthrough->hit_date}\n" .
                "📈 *Close*: Rp " . number_format($breakthrough->hit_close, 0) . "\n" .
                "📦 *Volume*: " . number_format($breakthrough->hit_volume) . "\n\n" .
                "⚡ *Status*: {$signal->status}\n" .
                "🔗 *Signal ID*: {$signal->id}\n\n" .
                "Saham ini baru saja mencapai value di atas 100 miliar untuk pertama kalinya dalam 200 hari terakhir!";

            $response = Http::withHeaders([
                'accept' => 'application/json',
                'X-Api-Key' => env('WAHA_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post(env('WAHA_BASE_URL') . '/api/sendText', [
                'chatId' => env('WAHA_BASE_GROUP') . '@g.us',
                'reply_to' => null,
                'text' => $message,
                'linkPreview' => true,
                'linkPreviewHighQuality' => false,
                'session' => env('WAHA_SESSION_ID')
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'signal_id' => $signal->id,
                    'kode_emiten' => $breakthrough->kode_emiten
                ]);
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'signal_id' => $signal->id,
                    'kode_emiten' => $breakthrough->kode_emiten,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending WhatsApp message', [
                'signal_id' => $signal->id,
                'kode_emiten' => $breakthrough->kode_emiten,
                'error' => $e->getMessage()
            ]);
        }
    }
}
