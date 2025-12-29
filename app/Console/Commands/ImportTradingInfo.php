<?php

namespace App\Console\Commands;

use App\Models\TradingInfo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportTradingInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:import-trading
                            {path : Path to CSV file or directory containing CSV files}
                            {--truncate : Truncate the table before importing}
                            {--update : Update existing records instead of skipping}
                            {--batch-size=1000 : Number of records to insert per batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import trading info from CSV files (one file per stock code)';

    protected array $stats = [
        'files_processed' => 0,
        'files_skipped' => 0,
        'records_imported' => 0,
        'records_updated' => 0,
        'records_skipped' => 0,
        'errors' => [],
    ];

    protected array $columnMap = [
        'date' => 'date',
        'previous' => 'previous',
        'open_price' => 'open_price',
        'first_trade' => 'first_trade',
        'high' => 'high',
        'low' => 'low',
        'close' => 'close',
        'change' => 'change',
        'volume' => 'volume',
        'value' => 'value',
        'frequency' => 'frequency',
        'index_individual' => 'index_individual',
        'offer' => 'offer',
        'offer_volume' => 'offer_volume',
        'bid' => 'bid',
        'bid_volume' => 'bid_volume',
        'listed_shares' => 'listed_shares',
        'tradeble_shares' => 'tradeble_shares',
        'weight_for_index' => 'weight_for_index',
        'foreign_sell' => 'foreign_sell',
        'foreign_buy' => 'foreign_buy',
        'delisting_date' => 'delisting_date',
        'non_regular_volume' => 'non_regular_volume',
        'non_regular_value' => 'non_regular_value',
        'non_regular_frequency' => 'non_regular_frequency',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $path = $this->argument('path');
        $shouldTruncate = $this->option('truncate');
        $shouldUpdate = $this->option('update');
        $batchSize = (int) $this->option('batch-size');

        // Check if path exists
        if (!File::exists($path)) {
            $this->error("Path not found: {$path}");
            return Command::FAILURE;
        }

        // Get list of CSV files
        $files = $this->getCsvFiles($path);

        if (empty($files)) {
            $this->error("No CSV files found in: {$path}");
            return Command::FAILURE;
        }

        $this->info("Found " . count($files) . " CSV file(s) to process");

        // Truncate table if requested
        if ($shouldTruncate) {
            if ($this->confirm('Are you sure you want to truncate the trading_infos table?', false)) {
                DB::table('trading_infos')->truncate();
                $this->warn('Table truncated!');
            } else {
                $this->info('Truncate cancelled, continuing with import...');
            }
        }

        // Process each file
        $progressBar = $this->output->createProgressBar(count($files));
        $progressBar->start();

        foreach ($files as $file) {
            try {
                $this->processFile($file, $shouldUpdate, $batchSize);
                $this->stats['files_processed']++;
            } catch (\Exception $e) {
                $filename = basename($file);
                $this->stats['errors'][] = "{$filename}: {$e->getMessage()}";
            }

            $progressBar->advance();
        }

        $progressBar->finish();

        $this->newLine(2);
        $this->showSummary();

        return Command::SUCCESS;
    }

    /**
     * Get list of CSV files from path
     */
    private function getCsvFiles(string $path): array
    {
        if (is_file($path)) {
            return [$path];
        }

        // Get all CSV files in directory
        $files = File::glob($path . '/*.csv');

        // Also check for uppercase extension
        $filesUpper = File::glob($path . '/*.CSV');

        return array_merge($files, $filesUpper);
    }

    /**
     * Process a single CSV file
     */
    private function processFile(string $filePath, bool $shouldUpdate, int $batchSize): void
    {
        // Extract kode_emiten from filename (e.g., AADI.csv -> AADI)
        $filename = pathinfo($filePath, PATHINFO_FILENAME);
        $kodeEmiten = strtoupper($filename);

        // Read CSV file
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception("Cannot open file");
        }

        // Get header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            throw new \Exception("Cannot read header row");
        }

        // Normalize header
        $header = array_map(function ($col) {
            return strtolower(trim($col));
        }, $header);

        // Get existing dates if not updating
        $existingDates = [];
        if (!$shouldUpdate) {
            $existingDates = TradingInfo::where('kode_emiten', $kodeEmiten)
                ->pluck('date')
                ->map(fn($d) => $d->format('Y-m-d'))
                ->toArray();
        }

        // Process rows in batches
        $batch = [];
        $now = now();

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== count($header)) {
                    continue; // Skip malformed rows
                }

                $data = array_combine($header, $row);
                $date = $this->parseDate($data['date'] ?? null);

                if (!$date) {
                    continue; // Skip rows without valid date
                }

                // Check if record exists
                if (!$shouldUpdate && in_array($date, $existingDates)) {
                    $this->stats['records_skipped']++;
                    continue;
                }

                // Map data to database columns
                $record = $this->mapRowData($kodeEmiten, $data, $now);

                if ($shouldUpdate) {
                    // Update or create
                    TradingInfo::updateOrCreate(
                        ['kode_emiten' => $kodeEmiten, 'date' => $date],
                        $record
                    );
                    $this->stats['records_updated']++;
                } else {
                    $batch[] = $record;

                    // Insert batch when size reached
                    if (count($batch) >= $batchSize) {
                        TradingInfo::insert($batch);
                        $this->stats['records_imported'] += count($batch);
                        $batch = [];
                    }
                }
            }

            // Insert remaining batch
            if (!empty($batch)) {
                TradingInfo::insert($batch);
                $this->stats['records_imported'] += count($batch);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        } finally {
            fclose($handle);
        }
    }

    /**
     * Map CSV row data to database columns
     */
    private function mapRowData(string $kodeEmiten, array $data, $now): array
    {
        return [
            'kode_emiten' => $kodeEmiten,
            'date' => $this->parseDate($data['date'] ?? null),
            'previous' => $this->parseDecimal($data['previous'] ?? null),
            'open_price' => $this->parseDecimal($data['open_price'] ?? null),
            'first_trade' => $this->parseDecimal($data['first_trade'] ?? null),
            'high' => $this->parseDecimal($data['high'] ?? null),
            'low' => $this->parseDecimal($data['low'] ?? null),
            'close' => $this->parseDecimal($data['close'] ?? null),
            'change' => $this->parseDecimal($data['change'] ?? null),
            'volume' => $this->parseDecimal($data['volume'] ?? null),
            'value' => $this->parseDecimal($data['value'] ?? null),
            'frequency' => $this->parseDecimal($data['frequency'] ?? null),
            'index_individual' => $this->parseDecimal($data['index_individual'] ?? null),
            'offer' => $this->parseDecimal($data['offer'] ?? null),
            'offer_volume' => $this->parseDecimal($data['offer_volume'] ?? null),
            'bid' => $this->parseDecimal($data['bid'] ?? null),
            'bid_volume' => $this->parseDecimal($data['bid_volume'] ?? null),
            'listed_shares' => $this->parseDecimal($data['listed_shares'] ?? null),
            'tradeble_shares' => $this->parseDecimal($data['tradeble_shares'] ?? null),
            'weight_for_index' => $this->parseDecimal($data['weight_for_index'] ?? null),
            'foreign_sell' => $this->parseDecimal($data['foreign_sell'] ?? null),
            'foreign_buy' => $this->parseDecimal($data['foreign_buy'] ?? null),
            'delisting_date' => $this->parseDate($data['delisting_date'] ?? null),
            'non_regular_volume' => $this->parseDecimal($data['non_regular_volume'] ?? null),
            'non_regular_value' => $this->parseDecimal($data['non_regular_value'] ?? null),
            'non_regular_frequency' => $this->parseDecimal($data['non_regular_frequency'] ?? null),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    /**
     * Parse date value
     */
    private function parseDate($value): ?string
    {
        if (empty($value) || $value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse decimal value
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
     * Show import summary
     */
    private function showSummary(): void
    {
        $this->info('Import completed!');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Files Processed', $this->stats['files_processed']],
                ['Files Skipped', $this->stats['files_skipped']],
                ['Records Imported', number_format($this->stats['records_imported'])],
                ['Records Updated', number_format($this->stats['records_updated'])],
                ['Records Skipped', number_format($this->stats['records_skipped'])],
                ['Errors', count($this->stats['errors'])],
            ]
        );

        // Show database stats
        $this->newLine();
        $totalRecords = TradingInfo::count();
        $uniqueStocks = TradingInfo::distinct('kode_emiten')->count('kode_emiten');
        $latestDate = TradingInfo::max('date');
        $oldestDate = TradingInfo::min('date');

        $this->info('Database Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Records', number_format($totalRecords)],
                ['Unique Stocks', number_format($uniqueStocks)],
                ['Date Range', ($oldestDate ?? '-') . ' to ' . ($latestDate ?? '-')],
            ]
        );

        // Show errors if any
        if (count($this->stats['errors']) > 0) {
            $this->newLine();
            $this->error('Errors encountered:');
            foreach (array_slice($this->stats['errors'], 0, 20) as $error) {
                $this->line("  - {$error}");
            }
            if (count($this->stats['errors']) > 20) {
                $this->line("  ... and " . (count($this->stats['errors']) - 20) . " more errors");
            }
        }
    }
}

