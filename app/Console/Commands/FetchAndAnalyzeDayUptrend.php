<?php

namespace App\Console\Commands;

use App\Models\GoapiGetStockPrice;
use App\Models\TradingInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class FetchAndAnalyzeDayUptrend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:fetch-and-analyze-day-uptrend
                            {--batch-size=200 : Jumlah record per batch saat sinkronisasi dari goapi_get_stock_prices}
                            {--test : Mode test (tanpa sinkron ke trading_infos dan tanpa analisa)}
                            {--from-latest=3 : Jumlah hari trading terakhir yang dianalisa (default 3)}
                            {--limit= : Batasi jumlah sinyal yang disimpan}
                            {--auto-publish : Auto-publish sinyal uptrend yang terdeteksi}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kombinasikan data harga dari tabel goapi_get_stock_prices ke trading_infos lalu otomatis menganalisa pola day uptrend dan menyimpan ke stock_signals.';

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
        $startTime = microtime(true);

        $batchSize   = (int) $this->option('batch-size');
        $isTestMode  = $this->option('test');
        $days        = (int) ($this->option('from-latest') ?: 3);
        $limit       = $this->option('limit');
        $autoPublish = $this->option('auto-publish');

        if ($isTestMode) {
            $this->warn('Running in TEST MODE - Tidak ada sinkronisasi ke trading_infos atau analisa uptrend.');
        }

        if ($days < 2) {
            $this->error('Jumlah hari minimal untuk analisa uptrend adalah 2.');
            return Command::FAILURE;
        }

        // Ambil seluruh data dari tabel goapi_get_stock_prices untuk disinkronkan ke trading_infos
        $totalRecords = GoapiGetStockPrice::count();

        if ($totalRecords === 0) {
            $this->warn('Tabel goapi_get_stock_prices kosong. Jalankan fetch harga dulu (misal: stock:fetch-goapi-prices).');
            return Command::SUCCESS;
        }

        $this->stats['total_symbols'] = $totalRecords;
        $this->info("Ditemukan {$totalRecords} record di goapi_get_stock_prices untuk disinkronkan ke trading_infos.");

        // Proses dalam batch (chunkById)
        GoapiGetStockPrice::query()
            ->orderBy('id')
            ->chunkById($batchSize, function ($chunk) use ($isTestMode) {
                $this->processBatch($chunk, $isTestMode);
                $this->stats['batches_processed']++;
            });

        $this->showSummary();

        // Setelah sinkronisasi, jalankan analisa uptrend dengan command existing
        if (! $isTestMode) {
            $this->info('Menjalankan analisa day uptrend (stock:analyze-day-uptrend)...');

            Artisan::call('stock:analyze-day-uptrend', array_filter([
                '--from-latest' => $days,
                '--limit'       => $limit,
                '--save'        => true,
                '--publish'     => $autoPublish,
            ], function ($v) {
                return ! is_null($v);
            }));

            // Tampilkan output dari command analisa
            $this->line(Artisan::output());
        }

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        Log::info('FetchAndAnalyzeDayUptrend command completed', [
            'execution_time_seconds' => $executionTime,
            'total_symbols' => $this->stats['total_symbols'],
            'batches_processed' => $this->stats['batches_processed'],
            'records_saved' => $this->stats['records_saved'],
            'errors_count' => count($this->stats['errors']),
            'test_mode' => $isTestMode,
            'auto_publish' => $autoPublish,
        ]);

        $this->info("Command completed in {$executionTime} seconds");

        return Command::SUCCESS;
    }

    /**
     * Sinkronkan satu batch data GoAPI ke trading_infos
     *
     * @param \Illuminate\Support\Collection<int,\App\Models\GoapiGetStockPrice> $batch
     */
    private function processBatch($batch, bool $isTestMode): void
    {
        if ($isTestMode) {
            $this->info('TEST: Akan sinkron ' . $batch->count() . ' record dari goapi_get_stock_prices ke trading_infos.');
            return;
        }

        $rows = [];

        foreach ($batch as $item) {
            /** @var GoapiGetStockPrice $item */
            $rows[] = [
                'kode_emiten' => $item->symbol,
                'date'        => $item->date,
                'open_price'  => $item->open,
                'high'        => $item->high,
                'low'         => $item->low,
                'close'       => $item->close,
                'volume'      => $item->volume,
                'change'      => $item->change,
                'value'       => $item->value,
            ];
        }

        if (! empty($rows)) {
            TradingInfo::upsert(
                $rows,
                ['kode_emiten', 'date'],
                ['open_price', 'high', 'low', 'close', 'volume', 'change', 'value']
            );

            $this->stats['records_saved'] += count($rows);
        }
    }

    /**
     * Tampilkan ringkasan proses
     */
    private function showSummary(): void
    {
        $this->info('Proses sinkronisasi dari goapi_get_stock_prices ke trading_infos selesai!');

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Records (GoAPI)', number_format($this->stats['total_symbols'])],
                ['Batches Processed', number_format($this->stats['batches_processed'])],
                ['Records Saved to trading_infos', number_format($this->stats['records_saved'])],
                ['Errors', count($this->stats['errors'])],
            ]
        );

        if (count($this->stats['errors']) > 0) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach (array_slice($this->stats['errors'], 0, 10) as $error) {
                $this->line("  - {$error}");
            }
            if (count($this->stats['errors']) > 10) {
                $this->line('  ... dan ' . (count($this->stats['errors']) - 10) . ' error lainnya');
            }
        }
    }
}

