<?php

namespace App\Console\Commands;

use App\Models\News;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportIdxNewsDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idx:import-news-details
                            {file? : Path to the detailed news JSON file}
                            {--create-missing : Create news records if they don\'t exist}
                            {--force : Skip confirmation prompts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import detailed news content from IDX JSON file (updates existing records with contents field)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file') ?: base_path('data/news/idx_news_detailed_20250101_to_20251230.json');
        $createMissing = $this->option('create-missing');
        $force = $this->option('force');

        // Check if file exists
        if (!File::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        // Read and parse JSON
        $this->info('Reading detailed news JSON file...');
        $jsonContent = File::get($filePath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON format: ' . json_last_error_msg());
            return Command::FAILURE;
        }

        // Check for data array (handle IDX format with metadata)
        $newsDetails = $data['data'] ?? $data;

        if (!is_array($newsDetails)) {
            $this->error('Invalid data structure: expected array of news details');
            return Command::FAILURE;
        }

        $totalRecords = count($newsDetails);
        $this->info("Found {$totalRecords} news details to process");

        if ($totalRecords === 0) {
            $this->warn('No news details found in the file');
            return Command::SUCCESS;
        }

        // Display metadata if available
        if (isset($data['total'])) {
            $this->info("IDX Total Records: {$data['total']}");
        }

        // Show warning about operation
        if (!$force) {
            $this->warn('This command will update existing news records with detailed content.');
            $this->warn('Only records with matching ItemId will be updated.');
            if (!$this->confirm('Do you want to continue?', true)) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        // Process data with progress bar
        $progressBar = $this->output->createProgressBar($totalRecords);
        $progressBar->start();

        $updated = 0;
        $created = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($newsDetails as $index => $newsDetail) {
                try {
                    $result = $this->processNewsDetail($newsDetail, $createMissing);

                    if ($result === 'updated') {
                        $updated++;
                    } elseif ($result === 'created') {
                        $created++;
                    } else {
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $itemId = $newsDetail['ItemId'] ?? "index-{$index}";
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
                    ['Updated (with content)', $updated],
                    ['Created (new records)', $created],
                    ['Skipped (no match)', $skipped],
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
     * Process a single news detail item
     */
    private function processNewsDetail(array $data, bool $createMissing): string
    {
        $itemId = $data['ItemId'] ?? null;

        if (empty($itemId)) {
            throw new \Exception('ItemId is required');
        }

        // Check if news exists
        $existing = News::where('item_id', $itemId)->first();

        if ($existing) {
            // Update existing record with detailed content
            $updateData = [];

            // Only update fields that are not already set or if content is provided
            if (empty($existing->contents) && !empty($data['Contents'])) {
                $updateData['contents'] = $data['Contents'];
            }

            // Update other fields if they're empty in the existing record
            $fieldsToUpdate = [
                'image_url' => 'ImageUrl',
                'locale' => 'Locale',
                'title' => 'Title',
                'path_base' => 'PathBase',
                'path_file' => 'PathFile',
                'tags' => 'Tags',
                'is_headline' => 'IsHeadline',
                'summary' => 'Summary',
            ];

            foreach ($fieldsToUpdate as $dbField => $jsonField) {
                if (empty($existing->$dbField) && isset($data[$jsonField]) && $data[$jsonField] !== null) {
                    $updateData[$dbField] = $dbField === 'is_headline' ? (bool) $data[$jsonField] : $data[$jsonField];
                }
            }

            if (!empty($updateData)) {
                $existing->update($updateData);
                return 'updated';
            }

            return 'skipped';
        }

        // Create new record if allowed
        if ($createMissing) {
            $newsData = $this->mapNewsDetailData($data);
            News::create($newsData);
            return 'created';
        }

        return 'skipped';
    }

    /**
     * Map JSON data to database columns for detailed news
     */
    private function mapNewsDetailData(array $data): array
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
