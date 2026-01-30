<?php

namespace App\Console\Commands;

use App\Models\TradingInfo;
use App\Models\GoapiStockPrice;
use Illuminate\Console\Command;
use App\Models\GoapiGetStockPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FetchGoapiStockPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:fetch-goapi-prices
                            {--batch-size=50 : Number of stock codes to process per batch}
                            {--test : Run in test mode (no actual API calls or database saves)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch stock prices from GoAPI for today\'s trading data';

    protected array $stats = [
        'total_symbols' => 0,
        'batches_processed' => 0,
        'api_calls_made' => 0,
        'records_saved' => 0,
        'errors' => [],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $startTime = microtime(true); // Start timing

        $batchSize = (int) $this->option('batch-size');
        $isTestMode = $this->option('test');

        if ($isTestMode) {
            $this->warn('Running in TEST MODE - No actual API calls or database saves will be made');
        }

        // Get yesterday's date
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $this->info("Fetching stock symbols for date: {$today}");

        // Query kode_emiten from trading_infos where date = yesterday
        $stockCodes = TradingInfo::where('date', $yesterday)
            ->select('kode_emiten')
            ->distinct()
            ->pluck('kode_emiten')
            ->toArray();

        if (empty($stockCodes)) {
            $this->warn("No stock codes found for date {$today}");
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

        // Process in batches of 100
        $batches = array_chunk($stockCodes, $batchSize);
        $progressBar = $this->output->createProgressBar(count($batches));
        $progressBar->start();

        foreach ($batches as $batch) {
            try {
                $this->processBatch($batch, $isTestMode);
                $this->stats['batches_processed']++;
            } catch (\Exception $e) {
                $this->stats['errors'][] = "Batch processing error: {$e->getMessage()}";
                Log::error('FetchGoapiStockPrices batch error', [
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
        Log::info("FetchGoapiStockPrices command completed", [
            'execution_time_seconds' => $executionTime,
            'total_symbols' => $this->stats['total_symbols'],
            'batches_processed' => $this->stats['batches_processed'],
            'api_calls_made' => $this->stats['api_calls_made'],
            'records_saved' => $this->stats['records_saved'],
            'errors_count' => count($this->stats['errors']),
            'test_mode' => $isTestMode
        ]);

        $this->info("Command completed in {$executionTime} seconds");

        return Command::SUCCESS;
    }

    /**
     * Process a batch of stock codes
     */
    private function processBatch(array $stockCodes, bool $isTestMode): void
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
            $this->saveStockPrices($data);

        } catch (\Exception $e) {
            $this->stats['errors'][] = "API call failed for symbols {$symbols}: {$e->getMessage()}";
            Log::error('FetchGoapiStockPrices API error', [
                'symbols' => $symbols,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Save stock price data to database
     */
    private function saveStockPrices(array $data): void
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

            $this->stats['records_saved'] += count($records);
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
}
