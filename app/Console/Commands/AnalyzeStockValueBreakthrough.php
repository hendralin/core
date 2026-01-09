<?php

namespace App\Console\Commands;

use App\Models\StockSignal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnalyzeStockValueBreakthrough extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:analyze-value-breakthrough
                            {--limit= : Limit the number of results}
                            {--export= : Export results to CSV file}
                            {--market-cap-max=5000000000000 : Maximum market cap filter (default: 5T)}
                            {--save : Save results to database as stock signals}
                            {--publish : Auto-publish saved signals (only works with --save)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze stocks that hit 100B value for the first time in the last 200 trading days with market cap < 5T, PBV > 0, and PER > 0. Can save results as stock signals.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Analyzing stock value breakthroughs...');
        $this->info('Looking for stocks that hit 100B+ value for the first time in the last 200 trading days');
        $this->newLine();

        $marketCapMax = (float) $this->option('market-cap-max');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        // Execute the complex query
        $results = $this->executeValueBreakthroughQuery($marketCapMax, $limit);

        if (empty($results)) {
            $this->warn('No stocks found matching the criteria.');
            return Command::SUCCESS;
        }

        // Display results
        $this->displayResults($results);

        // Save to database if requested
        if ($this->option('save')) {
            $this->saveToDatabase($results);
        }

        // Export if requested
        if ($exportPath = $this->option('export')) {
            $this->exportToCsv($results, $exportPath);
        }

        $this->newLine();
        $this->info("✅ Analysis completed. Found " . count($results) . " stocks matching the criteria.");

        return Command::SUCCESS;
    }

    /**
     * Execute the value breakthrough analysis query
     */
    private function executeValueBreakthroughQuery(float $marketCapMax, ?int $limit = null)
    {
        $query = "
            WITH ranked AS (
                SELECT
                    kode_emiten,
                    `Date`,
                    `Value`,
                    Close,
                    Volume,
                    Listed_Shares,
                    (Close * Listed_Shares) AS market_cap,
                    ROW_NUMBER() OVER (
                        PARTITION BY kode_emiten
                        ORDER BY `Date` DESC
                    ) AS rn_desc
                FROM trading_infos
            ),
            last_200 AS (
                SELECT *
                FROM ranked
                WHERE rn_desc <= 200
                  AND market_cap < ?
            ),
            first_100m AS (
                SELECT
                    kode_emiten,
                    `Date` AS hit_date,
                    `Value` AS hit_value,
                    Close AS hit_close,
                    Volume AS hit_volume,
                    market_cap,
                    ROW_NUMBER() OVER (
                        PARTITION BY kode_emiten
                        ORDER BY `Date` ASC
                    ) AS rn_hit
                FROM last_200
                WHERE `Value` >= 100000000000
            )
            SELECT
                f.kode_emiten,
                f.market_cap,

                -- Financial Ratios
                fr.price_bv AS pbv,
                fr.per AS per,

                -- H-1
                b.`Date`   AS before_date,
                b.`Value`  AS before_value,
                b.Close    AS before_close,
                b.Volume   AS before_volume,

                -- H
                f.hit_date,
                f.hit_value,
                f.hit_close,
                f.hit_volume,

                -- H+1
                a.`Date`   AS after_date,
                a.`Value`  AS after_value,
                a.Close    AS after_close,
                a.Volume   AS after_volume

            FROM first_100m f
            INNER JOIN (
                SELECT
                    code,
                    price_bv,
                    per,
                    ROW_NUMBER() OVER (PARTITION BY code ORDER BY fs_date DESC) as rn
                FROM financial_ratios
                WHERE price_bv > 0 AND per > 0
            ) fr ON fr.code = f.kode_emiten AND fr.rn = 1
            LEFT JOIN trading_infos b
                ON b.kode_emiten = f.kode_emiten
               AND b.`Date` = (
                    SELECT MAX(`Date`)
                    FROM trading_infos
                    WHERE kode_emiten = f.kode_emiten
                      AND `Date` < f.hit_date
               )
            LEFT JOIN trading_infos a
                ON a.kode_emiten = f.kode_emiten
               AND a.`Date` = (
                    SELECT MIN(`Date`)
                    FROM trading_infos
                    WHERE kode_emiten = f.kode_emiten
                      AND `Date` > f.hit_date
               )
            WHERE f.rn_hit = 1
            ORDER BY f.hit_date
        ";

        $params = [$marketCapMax];

        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = $limit;
        }

        return DB::select($query, $params);
    }

    /**
     * Display results in a formatted table
     */
    private function displayResults($results)
    {
        $tableData = [];

        foreach ($results as $result) {
            $tableData[] = [
                'Kode' => $result->kode_emiten,
                'Market Cap' => $this->formatCurrency($result->market_cap),
                'PBV' => $result->pbv ? number_format($result->pbv, 2) . 'x' : '-',
                'PER' => $result->per ? number_format($result->per, 2) . 'x' : '-',
                'H-1 Date' => $result->before_date ?? '-',
                'H-1 Value' => $result->before_value ? $this->formatCurrency($result->before_value) : '-',
                'H-1 Close' => $result->before_close ? $this->formatPrice($result->before_close) : '-',
                'H-1 Volume' => $result->before_volume ? number_format($result->before_volume) : '-',
                'Hit Date' => $result->hit_date,
                'Hit Value' => $this->formatCurrency($result->hit_value),
                'Hit Close' => $this->formatPrice($result->hit_close),
                'Hit Volume' => number_format($result->hit_volume),
                'H+1 Date' => $result->after_date ?? '-',
                'H+1 Value' => $result->after_value ? $this->formatCurrency($result->after_value) : '-',
                'H+1 Close' => $result->after_close ? $this->formatPrice($result->after_close) : '-',
                'H+1 Volume' => $result->after_volume ? number_format($result->after_volume) : '-',
            ];
        }

        $headers = [
            'Kode',
            'Market Cap',
            'PBV',
            'PER',
            'H-1 Date',
            'H-1 Value',
            'H-1 Close',
            'H-1 Volume',
            'Hit Date',
            'Hit Value',
            'Hit Close',
            'Hit Volume',
            'H+1 Date',
            'H+1 Value',
            'H+1 Close',
            'H+1 Volume',
        ];

        $this->table($headers, $tableData);
    }

    /**
     * Export results to CSV file
     */
    private function exportToCsv($results, string $filePath)
    {
        $this->info("📄 Exporting results to: {$filePath}");

        $handle = fopen($filePath, 'w');

        // Write CSV header
        fputcsv($handle, [
            'Kode Emiten',
            'Market Cap',
            'PBV',
            'PER',
            'Before Date',
            'Before Value',
            'Before Close',
            'Before Volume',
            'Hit Date',
            'Hit Value',
            'Hit Close',
            'Hit Volume',
            'After Date',
            'After Value',
            'After Close',
            'After Volume'
        ]);

        // Write data rows
        foreach ($results as $result) {
            fputcsv($handle, [
                $result->kode_emiten,
                $result->market_cap,
                $result->pbv ?? '',
                $result->per ?? '',
                $result->before_date ?? '',
                $result->before_value ?? '',
                $result->before_close ?? '',
                $result->before_volume ?? '',
                $result->hit_date,
                $result->hit_value,
                $result->hit_close,
                $result->hit_volume,
                $result->after_date ?? '',
                $result->after_value ?? '',
                $result->after_close ?? '',
                $result->after_volume ?? ''
            ]);
        }

        fclose($handle);
        $this->info("✅ Export completed!");
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
     * Format price values
     */
    private function formatPrice($value): string
    {
        return number_format($value, 0);
    }

    /**
     * Save results to database as stock signals
     */
    private function saveToDatabase(array $results): void
    {
        $this->info("💾 Saving " . count($results) . " signals to database...");

        $saved = 0;
        $skipped = 0;
        $autoPublish = $this->option('publish');

        foreach ($results as $result) {
            try {
                // Check if signal already exists for this stock and date
                $existingSignal = StockSignal::where('kode_emiten', $result->kode_emiten)
                    ->where('hit_date', $result->hit_date)
                    ->where('signal_type', 'value_breakthrough')
                    ->first();

                if ($existingSignal) {
                    $this->warn("Signal for {$result->kode_emiten} on {$result->hit_date} already exists, skipping...");
                    $skipped++;
                    continue;
                }

                // Create new signal
                $signal = StockSignal::create([
                    'signal_type' => 'value_breakthrough',
                    'kode_emiten' => $result->kode_emiten,
                    'market_cap' => $result->market_cap,
                    'pbv' => $result->pbv,
                    'per' => $result->per,

                    // H-1 data
                    'before_date' => $result->before_date,
                    'before_value' => $result->before_value,
                    'before_close' => $result->before_close,
                    'before_volume' => $result->before_volume,

                    // H data (hit)
                    'hit_date' => $result->hit_date,
                    'hit_value' => $result->hit_value,
                    'hit_close' => $result->hit_close,
                    'hit_volume' => $result->hit_volume,

                    // H+1 data
                    'after_date' => $result->after_date,
                    'after_value' => $result->after_value,
                    'after_close' => $result->after_close,
                    'after_volume' => $result->after_volume,

                    // Status
                    'status' => $autoPublish ? 'published' : 'draft',
                    'published_at' => $autoPublish ? now() : null,

                    // Default recommendation
                    'recommendation' => $this->generateRecommendation($result),
                ]);

                $saved++;

                $this->info("✓ Saved signal for {$result->kode_emiten}");

            } catch (\Exception $e) {
                $this->error("Failed to save signal for {$result->kode_emiten}: {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("✅ Database save completed:");
        $this->table(
            ['Status', 'Count'],
            [
                ['Saved (new)', $saved],
                ['Skipped (exists)', $skipped],
                ['Total Processed', count($results)],
            ]
        );
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
}
