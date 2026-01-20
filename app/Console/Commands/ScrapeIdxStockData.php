<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ScrapeIdxStockData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:scrape-idx
                            {--date= : Specific date in YYYYMMDD format (defaults to today)}
                            {--python=python3 : Python executable to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape IDX stock trading summary data and save as JSON file using cloudscraper';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $date = $this->option('date');
        $pythonExecutable = $this->option('python');

        $this->info('Starting IDX Stock Data Scraper');

        // Use the bulk stock scraper
        $scriptPath = base_path('scripts/scrapers/idx_bulk_stock_scraper.py');
        $this->info("Using IDX bulk stock scraper");

        if (!File::exists($scriptPath)) {
            $this->error("Scraper script not found: {$scriptPath}");
            return Command::FAILURE;
        }

        // Check if Python executable exists
        $process = new Process([$pythonExecutable, '--version']);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error("Python executable not found or not working: {$pythonExecutable}");
            $this->error("Process output: " . $process->getErrorOutput());
            return Command::FAILURE;
        }

        $this->info("Using Python: " . trim($process->getOutput()));

        // Prepare command arguments
        $command = [$pythonExecutable, $scriptPath];

        if ($date) {
            $this->info("Scraping data for specific date: {$date}");
            $command[] = $date;
        } else {
            $this->info("Scraping data for today");
        }

        // Run the scraper script
        $this->info("Running scraper script...");
        $scraperProcess = new Process($command);
        $scraperProcess->setTimeout(300); // 5 minutes timeout
        $scraperProcess->setWorkingDirectory(base_path());

        try {
            $scraperProcess->mustRun(function ($type, $buffer) {
                if ($type === Process::OUT) {
                    $this->info(trim($buffer));
                } else {
                    $this->warn(trim($buffer));
                }
            });

            $this->info('IDX stock data scraping completed successfully!');

            // Check if file was created
            $expectedFilename = $date ?: now()->format('Ymd');
            $expectedFilepath = storage_path("app/trading-data/{$expectedFilename}.json");

            if (File::exists($expectedFilepath)) {
                $fileSize = File::size($expectedFilepath);
                $this->info("Data file created: {$expectedFilepath}");
                $this->info("File size: " . number_format($fileSize) . " bytes");

                // Show file info
                $fileContent = json_decode(File::get($expectedFilepath), true);
                if ($fileContent && isset($fileContent['data'])) {
                    $recordCount = count($fileContent['data']);
                    $this->info("Records scraped: " . number_format($recordCount));
                }
            } else {
                $this->warn("Expected data file not found: {$expectedFilepath}");
            }

            return Command::SUCCESS;

        } catch (ProcessFailedException $e) {
            $this->error('Scraper script failed!');
            $this->error('Error output: ' . $e->getProcess()->getErrorOutput());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('Unexpected error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}