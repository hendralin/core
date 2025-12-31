<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportIdxNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idx:import-news
                            {--basic-file= : Path to the basic news JSON file}
                            {--detailed-file= : Path to the detailed news JSON file}
                            {--truncate : Truncate the table before importing}
                            {--update : Update existing records instead of skipping}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import IDX news data from JSON files into database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $basicFilePath = $this->option('basic-file') ?: base_path('data/news/idx_news_20250101_to_20251230.json');
        $detailedFilePath = $this->option('detailed-file') ?: base_path('data/news/idx_news_detailed_20250101_to_20251230.json');
        $shouldTruncate = $this->option('truncate');
        $shouldUpdate = $this->option('update');

        // Check if at least one file exists
        $basicExists = File::exists($basicFilePath);
        $detailedExists = File::exists($detailedFilePath);

        if (!$basicExists && !$detailedExists) {
            $this->error("No valid files found. Checked:");
            $this->error("  Basic file: {$basicFilePath}");
            $this->error("  Detailed file: {$detailedFilePath}");
            return Command::FAILURE;
        }

        // Load data from both files
        $basicData = [];
        $detailedData = [];

        if ($basicExists) {
            $this->info('Reading basic news file...');
            $basicData = $this->loadJsonFile($basicFilePath);
            if ($basicData === null) return Command::FAILURE;
        }

        if ($detailedExists) {
            $this->info('Reading detailed news file...');
            $detailedData = $this->loadJsonFile($detailedFilePath);
            if ($detailedData === null) return Command::FAILURE;
        }

        // Merge data - detailed data takes precedence for content
        $mergedData = $this->mergeNewsData($basicData, $detailedData);
        $totalRecords = count($mergedData);

        $this->info("Found {$totalRecords} news articles to import");

        if ($totalRecords === 0) {
            $this->warn('No news articles found in the files');
            return Command::SUCCESS;
        }

        // Truncate table if requested
        if ($shouldTruncate) {
            if ($this->confirm('Are you sure you want to truncate the idx_news table?', false)) {
                DB::table('idx_news')->truncate();
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
            foreach ($mergedData as $index => $newsItem) {
                try {
                    $result = $this->importNewsItem($newsItem, $shouldUpdate);

                    if ($result === 'imported') {
                        $imported++;
                    } elseif ($result === 'updated') {
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $itemId = $newsItem['ItemId'] ?? "index-{$index}";
                    $errors[] = "{$itemId}: {$e->getMessage()}";
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

            // Show errors if any
            if (count($errors) > 0) {
                $this->newLine();
                $this->error('Errors encountered:');
                foreach ($errors as $error) {
                    $this->line("  - {$error}");
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
     * Load and parse JSON file
     */
    private function loadJsonFile(string $filePath): ?array
    {
        if (!File::exists($filePath)) {
            $this->warn("File not found: {$filePath}");
            return null;
        }

        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid JSON in {$filePath}: " . json_last_error_msg());
            return null;
        }

        // Extract data array from IDX format
        return $data['data'] ?? $data;
    }

    /**
     * Merge basic and detailed news data
     */
    private function mergeNewsData(array $basicData, array $detailedData): array
    {
        $merged = [];

        // Create lookup map for detailed data by ItemId
        $detailedMap = [];
        foreach ($detailedData as $item) {
            if (isset($item['ItemId'])) {
                $detailedMap[$item['ItemId']] = $item;
            }
        }

        // Merge data - use basic as base, add content from detailed
        foreach ($basicData as $basicItem) {
            $itemId = $basicItem['ItemId'] ?? null;
            $mergedItem = $basicItem;

            // If detailed version exists, merge the Contents field
            if ($itemId && isset($detailedMap[$itemId])) {
                $mergedItem['Contents'] = $detailedMap[$itemId]['Contents'] ?? null;
            }

            $merged[] = $mergedItem;
        }

        // Add any detailed items that don't exist in basic
        foreach ($detailedData as $detailedItem) {
            $itemId = $detailedItem['ItemId'] ?? null;
            if ($itemId && !isset($detailedMap[$itemId])) {
                // This item wasn't in basic data, add it with content
                $merged[] = $detailedItem;
            }
        }

        return $merged;
    }

    /**
     * Import a single news item
     */
    private function importNewsItem(array $data, bool $shouldUpdate): string
    {
        $itemId = $data['ItemId'] ?? null;

        if (empty($itemId)) {
            throw new \Exception('ItemId is required');
        }

        // Map JSON fields to database columns
        $newsData = $this->mapNewsData($data);

        // Check if news exists
        $existing = News::where('item_id', $itemId)->first();

        if ($existing) {
            if ($shouldUpdate) {
                $existing->update($newsData);
                return 'updated';
            }
            return 'skipped';
        }

        // Create new record
        News::create($newsData);
        return 'imported';
    }

    /**
     * Map JSON data to database columns
     */
    private function mapNewsData(array $data): array
    {
        return [
            'item_id' => $data['ItemId'],
            'published_date' => $data['PublishedDate'] ?? now(),
            'image_url' => $data['ImageUrl'] ?? null,
            'locale' => $data['Locale'] ?? 'en-us',
            'title' => $data['Title'] ?? '',
            'path_base' => $data['PathBase'] ?? null,
            'path_file' => $data['PathFile'] ?? null,
            'tags' => $data['Tags'] ?? null,
            'is_headline' => (bool) ($data['IsHeadline'] ?? false),
            'summary' => $data['Summary'] ?? null,
            'contents' => $data['Contents'] ?? null,
        ];
    }
}
