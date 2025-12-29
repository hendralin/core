<?php

namespace App\Console\Commands;

use App\Models\StockCompany;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportStockCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:import-companies
                            {file : Path to the JSON file to import}
                            {--truncate : Truncate the table before importing}
                            {--update : Update existing records instead of skipping}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import stock companies data from IDX JSON file into database';

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
        $companies = $data['data'] ?? $data;

        if (!is_array($companies)) {
            $this->error('Invalid data structure: expected array of companies');
            return Command::FAILURE;
        }

        $totalRecords = count($companies);
        $this->info("Found {$totalRecords} companies to import");

        if ($totalRecords === 0) {
            $this->warn('No companies found in the file');
            return Command::SUCCESS;
        }

        // Display metadata if available
        if (isset($data['recordsTotal'])) {
            $this->info("IDX Records Total: {$data['recordsTotal']}");
        }

        // Truncate table if requested
        if ($shouldTruncate) {
            if ($this->confirm('Are you sure you want to truncate the stock_companies table?', false)) {
                DB::table('stock_companies')->truncate();
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
            foreach ($companies as $index => $company) {
                try {
                    $result = $this->importCompany($company, $shouldUpdate);

                    if ($result === 'imported') {
                        $imported++;
                    } elseif ($result === 'updated') {
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } catch (\Exception $e) {
                    $kodeEmiten = $company['KodeEmiten'] ?? "index-{$index}";
                    $errors[] = "{$kodeEmiten}: {$e->getMessage()}";
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
     * Import a single company record
     */
    private function importCompany(array $data, bool $shouldUpdate): string
    {
        $kodeEmiten = $data['KodeEmiten'] ?? null;

        if (empty($kodeEmiten)) {
            throw new \Exception('KodeEmiten is required');
        }

        // Map JSON fields to database columns
        $companyData = $this->mapCompanyData($data);

        // Check if company exists
        $existing = StockCompany::where('kode_emiten', $kodeEmiten)->first();

        if ($existing) {
            if ($shouldUpdate) {
                $existing->update($companyData);
                return 'updated';
            }
            return 'skipped';
        }

        // Create new record
        StockCompany::create($companyData);
        return 'imported';
    }

    /**
     * Map JSON data to database columns
     */
    private function mapCompanyData(array $data): array
    {
        // Parse tanggal pencatatan
        $tanggalPencatatan = null;
        if (!empty($data['TanggalPencatatan'])) {
            try {
                $tanggalPencatatan = Carbon::parse($data['TanggalPencatatan'])->toDateString();
            } catch (\Exception $e) {
                // Keep null if parsing fails
            }
        }

        return [
            'source_id' => $data['id'] ?? null,
            'data_id' => $data['DataID'] ?? null,
            'kode_emiten' => $data['KodeEmiten'],
            'nama_emiten' => $data['NamaEmiten'] ?? '',
            'alamat' => $data['Alamat'] ?? null,
            'bae' => $data['BAE'] ?? null,
            'divisi' => $data['Divisi'] ?? null,
            'kode_divisi' => $data['KodeDivisi'] ?? null,
            'jenis_emiten' => $data['JenisEmiten'] ?? null,
            'kegiatan_usaha_utama' => $data['KegiatanUsahaUtama'] ?? null,
            'efek_emiten_eba' => (bool) ($data['EfekEmiten_EBA'] ?? false),
            'efek_emiten_etf' => (bool) ($data['EfekEmiten_ETF'] ?? false),
            'efek_emiten_obligasi' => (bool) ($data['EfekEmiten_Obligasi'] ?? false),
            'efek_emiten_saham' => (bool) ($data['EfekEmiten_Saham'] ?? false),
            'efek_emiten_spei' => (bool) ($data['EfekEmiten_SPEI'] ?? false),
            'sektor' => $data['Sektor'] ?? null,
            'sub_sektor' => $data['SubSektor'] ?? null,
            'industri' => $data['Industri'] ?? null,
            'sub_industri' => $data['SubIndustri'] ?? null,
            'email' => $data['Email'] ?? null,
            'telepon' => $data['Telepon'] ?? null,
            'fax' => $data['Fax'] ?? null,
            'website' => $data['Website'] ?? null,
            'npkp' => $data['NPKP'] ?? null,
            'npwp' => $data['NPWP'] ?? null,
            'papan_pencatatan' => $data['PapanPencatatan'] ?? null,
            'tanggal_pencatatan' => $tanggalPencatatan,
            'status' => (int) ($data['Status'] ?? 0),
            'logo' => $data['Logo'] ?? null,
        ];
    }
}

