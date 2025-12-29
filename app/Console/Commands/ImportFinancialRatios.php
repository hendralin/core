<?php

namespace App\Console\Commands;

use App\Models\FinancialRatio;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportFinancialRatios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:import-ratios
                            {file : Path to the JSON file to import}
                            {--truncate : Truncate the table before importing}
                            {--update : Update existing records instead of skipping}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import financial ratios data from IDX JSON file into database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');
        $shouldTruncate = $this->option('truncate');
        $shouldUpdate = $this->option('update');

        // Check if file exists
        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        // Read and parse JSON
        $this->info('Reading JSON file...');
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON format: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        // Check for data array (handle IDX format with metadata)
        $ratios = $data['data'] ?? $data;

        if (!is_array($ratios)) {
            $this->error('Invalid data structure: expected array of financial ratios');
            return Command::FAILURE;
        }

        $totalRecords = count($ratios);
        $this->info("Found {$totalRecords} financial ratio records to import");

        if ($totalRecords === 0) {
            $this->warn('No records found in the file');
            return Command::SUCCESS;
        }

        // Display metadata if available
        if (isset($data['totalRecords'])) {
            $this->info("IDX Total Records: {$data['totalRecords']}");
        }

        // Truncate table if requested
        if ($shouldTruncate) {
            if ($this->confirm('Are you sure you want to truncate the financial_ratios table?', false)) {
                DB::table('financial_ratios')->truncate();
                $this->warn('Table truncated!');
            } else {
                $this->info('Truncate cancelled, continuing with import...');
            }
        }

        // Import data with progress bar
        $progressBar = $this->output->createProgressBar($totalRecords);
        $progressBar->start();

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($ratios as $index => $ratio) {
                try {
                    $result = $this->importRatio($ratio, $shouldUpdate);

                    if ($result === 'imported') {
                        $imported++;
                    } elseif ($result === 'updated') {
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $code = $ratio['code'] ?? "index-{$index}";
                    $fsDate = $ratio['fsDate'] ?? 'unknown';
                    $errors[] = "{$code} ({$fsDate}): {$e->getMessage()}";
                }

                $progressBar->advance();
            }

            DB::commit();
            $progressBar->finish();

            $this->newLine(2);
            $this->info('Import completed!');
            $this->table(
                ['Status', 'Count'],
                [
                    ['Imported (new)', $imported],
                    ['Updated', $updated],
                    ['Skipped (exists)', $skipped],
                    ['Errors', count($errors)],
                    ['Total Processed', $totalRecords],
                ]
            );

            // Show summary statistics
            $this->newLine();
            $this->showSummaryStats();

            // Show errors if any
            if (count($errors) > 0) {
                $this->newLine();
                $this->error('Errors encountered:');
                foreach (array_slice($errors, 0, 10) as $error) {
                    $this->line("  - {$error}");
                }
                if (count($errors) > 10) {
                    $this->line("  ... and " . (count($errors) - 10) . " more errors");
                }
            }

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
     * Import a single financial ratio record
     */
    private function importRatio(array $data, bool $shouldUpdate): string
    {
        $code = $data['code'] ?? null;
        $fsDate = $data['fsDate'] ?? null;

        if (empty($code)) {
            throw new \Exception('Stock code is required');
        }

        if (empty($fsDate)) {
            throw new \Exception('Financial statement date (fsDate) is required');
        }

        // Map JSON fields to database columns
        $ratioData = $this->mapRatioData($data);

        // Check if record exists (unique by code + fs_date)
        $existing = FinancialRatio::where('code', $code)
            ->where('fs_date', $ratioData['fs_date'])
            ->first();

        if ($existing) {
            if ($shouldUpdate) {
                $existing->update($ratioData);
                return 'updated';
            }
            return 'skipped';
        }

        // Create new record
        FinancialRatio::create($ratioData);
        return 'imported';
    }

    /**
     * Map JSON data to database columns
     */
    private function mapRatioData(array $data): array
    {
        // Parse fs_date
        $fsDate = null;
        if (!empty($data['fsDate'])) {
            try {
                $fsDate = Carbon::parse($data['fsDate'])->toDateString();
            } catch (\Exception $e) {
                throw new \Exception('Invalid fsDate format');
            }
        }

        return [
            'code' => $data['code'],
            'stock_name' => $data['stockName'] ?? '',
            'sharia' => $data['sharia'] ?? null,
            'sector' => $data['sector'] ?? null,
            'sub_sector' => $data['subSector'] ?? null,
            'industry' => $data['industry'] ?? null,
            'sub_industry' => $data['subIndustry'] ?? null,
            'sector_code' => $data['sectorCode'] ?? null,
            'sub_sector_code' => $data['subSectorCode'] ?? null,
            'industry_code' => !empty($data['industryCode']) ? $data['industryCode'] : null,
            'sub_industry_code' => $data['subIndustryCode'] ?? null,
            'sub_name' => $data['subName'] ?? null,
            'sub_code' => $data['subCode'] ?? null,
            'fs_date' => $fsDate,
            'fiscal_year_end' => $data['fiscalYearEnd'] ?? null,
            'assets' => $this->parseDecimal($data['assets'] ?? null),
            'liabilities' => $this->parseDecimal($data['liabilities'] ?? null),
            'equity' => $this->parseDecimal($data['equity'] ?? null),
            'sales' => $this->parseDecimal($data['sales'] ?? null),
            'ebt' => $this->parseDecimal($data['ebt'] ?? null),
            'profit_period' => $this->parseDecimal($data['profitPeriod'] ?? null),
            'profit_attr_owner' => $this->parseDecimal($data['profitAttrOwner'] ?? null),
            'eps' => $this->parseDecimal($data['eps'] ?? null),
            'book_value' => $this->parseDecimal($data['bookValue'] ?? null),
            'per' => $this->parseDecimal($data['per'] ?? null),
            'price_bv' => $this->parseDecimal($data['priceBV'] ?? null),
            'de_ratio' => $this->parseDecimal($data['deRatio'] ?? null),
            'roa' => $this->parseDecimal($data['roa'] ?? null),
            'roe' => $this->parseDecimal($data['roe'] ?? null),
            'npm' => $this->parseDecimal($data['npm'] ?? null),
            'audit' => $data['audit'] ?? null,
            'opini' => $data['opini'] ?? null,
        ];
    }

    /**
     * Parse decimal value safely
     */
    private function parseDecimal($value): ?float
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return null;
    }

    /**
     * Show summary statistics after import
     */
    private function showSummaryStats(): void
    {
        $totalRecords = FinancialRatio::count();
        $uniqueStocks = FinancialRatio::distinct('code')->count('code');
        $sectors = FinancialRatio::distinct('sector')->count('sector');
        $shariaCount = FinancialRatio::where('sharia', 'S')->distinct('code')->count('code');
        $latestDate = FinancialRatio::max('fs_date');

        $this->info('Database Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Records', number_format($totalRecords)],
                ['Unique Stocks', number_format($uniqueStocks)],
                ['Sectors', $sectors],
                ['Sharia Stocks', number_format($shariaCount)],
                ['Latest FS Date', $latestDate ?? '-'],
            ]
        );
    }
}

