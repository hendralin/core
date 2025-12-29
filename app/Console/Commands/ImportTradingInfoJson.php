<?php

namespace App\Console\Commands;

use App\Models\TradingInfo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportTradingInfoJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:import-trading-json
                            {file : Path to JSON file containing trading data}
                            {--truncate : Truncate the table before importing}
                            {--update : Update existing records instead of skipping}
                            {--batch-size=1000 : Number of records to insert per batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import trading info from a single JSON file (IDX format with all stocks in one file)';

    protected int $recordsImported = 0;
    protected int $recordsUpdated = 0;
    protected int $recordsSkipped = 0;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');
        $shouldTruncate = $this->option('truncate');
        $shouldUpdate = $this->option('update');
        $batchSize = (int) $this->option('batch-size');

        // Check if file exists
        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        // Check if it's a file (not directory)
        if (!is_file($filePath)) {
            $this->error("Path is not a file: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Reading JSON file: {$filePath}");

        // Read and parse JSON
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON format: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        // Get data array (handle IDX format with metadata)
        $records = $data['data'] ?? $data;

        if (!is_array($records)) {
            $this->error('Invalid data structure: expected array of records');
            return Command::FAILURE;
        }

        $totalRecords = count($records);

        if ($totalRecords === 0) {
            $this->warn('No records found in the file');
            return Command::SUCCESS;
        }

        $this->info("Found {$totalRecords} trading records to import");

        // Display metadata if available
        if (isset($data['recordsTotal'])) {
            $this->info("IDX Records Total: {$data['recordsTotal']}");
        }

        // Truncate table if requested
        if ($shouldTruncate) {
            if ($this->confirm('Are you sure you want to truncate the trading_infos table?', false)) {
                DB::table('trading_infos')->truncate();
                $this->warn('Table truncated!');
            } else {
                $this->info('Truncate cancelled, continuing with import...');
            }
        }

        // Get existing records for quick lookup if not updating
        $existingRecords = [];
        if (!$shouldUpdate) {
            $this->info('Loading existing records...');
            $stockCodes = collect($records)->pluck('StockCode')->unique()->filter()->toArray();

            if (!empty($stockCodes)) {
                $existingRecords = TradingInfo::whereIn('kode_emiten', $stockCodes)
                    ->get(['kode_emiten', 'date'])
                    ->mapWithKeys(function ($item) {
                        return [$item->kode_emiten . '_' . $item->date->format('Y-m-d') => true];
                    })
                    ->toArray();

                $this->info('Found ' . count($existingRecords) . ' existing records');
            }
        }

        // Import with progress bar
        $progressBar = $this->output->createProgressBar($totalRecords);
        $progressBar->start();

        $batch = [];
        $now = now();

        DB::beginTransaction();

        try {
            foreach ($records as $record) {
                $kodeEmiten = $record['StockCode'] ?? null;
                $date = $this->parseDate($record['Date'] ?? null);

                if (empty($kodeEmiten) || empty($date)) {
                    $progressBar->advance();
                    continue;
                }

                $key = $kodeEmiten . '_' . $date;

                // Check if record exists
                if (!$shouldUpdate && isset($existingRecords[$key])) {
                    $this->recordsSkipped++;
                    $progressBar->advance();
                    continue;
                }

                // Map data to database columns
                $mappedRecord = $this->mapRecordData($kodeEmiten, $record, $now);

                if ($shouldUpdate) {
                    TradingInfo::updateOrCreate(
                        ['kode_emiten' => $kodeEmiten, 'date' => $date],
                        $mappedRecord
                    );
                    $this->recordsUpdated++;
                } else {
                    $batch[] = $mappedRecord;

                    if (count($batch) >= $batchSize) {
                        TradingInfo::insert($batch);
                        $this->recordsImported += count($batch);
                        $batch = [];
                    }
                }

                $progressBar->advance();
            }

            // Insert remaining batch
            if (!empty($batch)) {
                TradingInfo::insert($batch);
                $this->recordsImported += count($batch);
            }

            DB::commit();
            $progressBar->finish();

            $this->newLine(2);
            $this->showSummary();

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $progressBar->finish();
            $this->newLine();
            $this->error('Import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Map JSON record to database columns
     */
    private function mapRecordData(string $kodeEmiten, array $data, $now): array
    {
        return [
            'kode_emiten' => $kodeEmiten,
            'date' => $this->parseDate($data['Date'] ?? null),
            'previous' => $this->parseDecimal($data['Previous'] ?? null),
            'open_price' => $this->parseDecimal($data['OpenPrice'] ?? null),
            'first_trade' => $this->parseDecimal($data['FirstTrade'] ?? null),
            'high' => $this->parseDecimal($data['High'] ?? null),
            'low' => $this->parseDecimal($data['Low'] ?? null),
            'close' => $this->parseDecimal($data['Close'] ?? null),
            'change' => $this->parseDecimal($data['Change'] ?? null),
            'volume' => $this->parseDecimal($data['Volume'] ?? null),
            'value' => $this->parseDecimal($data['Value'] ?? null),
            'frequency' => $this->parseDecimal($data['Frequency'] ?? null),
            'index_individual' => $this->parseDecimal($data['IndexIndividual'] ?? null),
            'offer' => $this->parseDecimal($data['Offer'] ?? null),
            'offer_volume' => $this->parseDecimal($data['OfferVolume'] ?? null),
            'bid' => $this->parseDecimal($data['Bid'] ?? null),
            'bid_volume' => $this->parseDecimal($data['BidVolume'] ?? null),
            'listed_shares' => $this->parseDecimal($data['ListedShares'] ?? null),
            'tradeble_shares' => $this->parseDecimal($data['TradebleShares'] ?? null),
            'weight_for_index' => $this->parseDecimal($data['WeightForIndex'] ?? null),
            'foreign_sell' => $this->parseDecimal($data['ForeignSell'] ?? null),
            'foreign_buy' => $this->parseDecimal($data['ForeignBuy'] ?? null),
            'delisting_date' => $this->parseDate($data['DelistingDate'] ?? null),
            'non_regular_volume' => $this->parseDecimal($data['NonRegularVolume'] ?? null),
            'non_regular_value' => $this->parseDecimal($data['NonRegularValue'] ?? null),
            'non_regular_frequency' => $this->parseDecimal($data['NonRegularFrequency'] ?? null),
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
        if (is_null($value) || $value === '' || $value === 'NaN') {
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
                ['Records Imported', number_format($this->recordsImported)],
                ['Records Updated', number_format($this->recordsUpdated)],
                ['Records Skipped', number_format($this->recordsSkipped)],
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
    }
}
