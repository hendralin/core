<?php

namespace App\Console\Commands;

use App\Models\CompanyAuditCommittee;
use App\Models\CompanyAuditor;
use App\Models\CompanyBond;
use App\Models\CompanyBondDetail;
use App\Models\CompanyCommissioner;
use App\Models\CompanyDirector;
use App\Models\CompanyDividend;
use App\Models\CompanySecretary;
use App\Models\CompanyShareholder;
use App\Models\CompanySubsidiary;
use App\Models\StockCompany;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportCompanyDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:import-details 
                            {file : Path to the JSON file to import}
                            {--truncate : Truncate all detail tables before importing}
                            {--update : Update existing records instead of skipping}
                            {--skip-missing : Skip companies that do not exist in stock_companies table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import company details (directors, commissioners, shareholders, etc.) from IDX JSON file';

    protected array $stats = [
        'companies_processed' => 0,
        'companies_skipped' => 0,
        'secretaries' => 0,
        'directors' => 0,
        'commissioners' => 0,
        'audit_committees' => 0,
        'shareholders' => 0,
        'subsidiaries' => 0,
        'auditors' => 0,
        'dividends' => 0,
        'bonds' => 0,
        'bond_details' => 0,
        'errors' => [],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->argument('file');
        $shouldTruncate = $this->option('truncate');
        $shouldUpdate = $this->option('update');
        $skipMissing = $this->option('skip-missing');

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

        // Data should be an object with stock codes as keys
        if (!is_array($data)) {
            $this->error('Invalid data structure');
            return Command::FAILURE;
        }

        $totalCompanies = count($data);
        $this->info("Found {$totalCompanies} companies to process");

        if ($totalCompanies === 0) {
            $this->warn('No companies found in the file');
            return Command::SUCCESS;
        }

        // Truncate tables if requested
        if ($shouldTruncate) {
            if ($this->confirm('Are you sure you want to truncate all company detail tables?', false)) {
                $this->truncateTables();
                $this->warn('All detail tables truncated!');
            } else {
                $this->info('Truncate cancelled, continuing with import...');
            }
        }

        // Import data with progress bar
        $progressBar = $this->output->createProgressBar($totalCompanies);
        $progressBar->start();

        DB::beginTransaction();

        try {
            foreach ($data as $kodeEmiten => $companyData) {
                try {
                    $this->importCompanyDetails($kodeEmiten, $companyData, $shouldUpdate, $skipMissing);
                    $this->stats['companies_processed']++;
                } catch (\Exception $e) {
                    $this->stats['errors'][] = "{$kodeEmiten}: {$e->getMessage()}";
                }

                $progressBar->advance();
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
     * Truncate all detail tables
     */
    private function truncateTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('company_secretaries')->truncate();
        DB::table('company_directors')->truncate();
        DB::table('company_commissioners')->truncate();
        DB::table('company_audit_committees')->truncate();
        DB::table('company_shareholders')->truncate();
        DB::table('company_subsidiaries')->truncate();
        DB::table('company_auditors')->truncate();
        DB::table('company_dividends')->truncate();
        DB::table('company_bonds')->truncate();
        DB::table('company_bond_details')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Import details for a single company
     */
    private function importCompanyDetails(string $kodeEmiten, array $data, bool $shouldUpdate, bool $skipMissing): void
    {
        // Check if company exists in stock_companies table
        $company = StockCompany::where('kode_emiten', $kodeEmiten)->first();

        if (!$company) {
            if ($skipMissing) {
                $this->stats['companies_skipped']++;
                return;
            }
            throw new \Exception("Company not found in stock_companies table");
        }

        // Delete existing records if updating
        if ($shouldUpdate) {
            $this->deleteExistingRecords($kodeEmiten);
        }

        // Import each section
        $this->importSecretaries($kodeEmiten, $data['Sekretaris'] ?? []);
        $this->importDirectors($kodeEmiten, $data['Direktur'] ?? []);
        $this->importCommissioners($kodeEmiten, $data['Komisaris'] ?? []);
        $this->importAuditCommittees($kodeEmiten, $data['KomiteAudit'] ?? []);
        $this->importShareholders($kodeEmiten, $data['PemegangSaham'] ?? []);
        $this->importSubsidiaries($kodeEmiten, $data['AnakPerusahaan'] ?? []);
        $this->importAuditors($kodeEmiten, $data['KAP'] ?? []);
        $this->importDividends($kodeEmiten, $data['Dividen'] ?? []);
        $this->importBonds($kodeEmiten, $data['BondsAndSukuk'] ?? []);
        $this->importBondDetails($kodeEmiten, $data['IssuedBond'] ?? []);
    }

    /**
     * Delete existing records for a company
     */
    private function deleteExistingRecords(string $kodeEmiten): void
    {
        CompanySecretary::where('kode_emiten', $kodeEmiten)->delete();
        CompanyDirector::where('kode_emiten', $kodeEmiten)->delete();
        CompanyCommissioner::where('kode_emiten', $kodeEmiten)->delete();
        CompanyAuditCommittee::where('kode_emiten', $kodeEmiten)->delete();
        CompanyShareholder::where('kode_emiten', $kodeEmiten)->delete();
        CompanySubsidiary::where('kode_emiten', $kodeEmiten)->delete();
        CompanyAuditor::where('kode_emiten', $kodeEmiten)->delete();
        CompanyDividend::where('kode_emiten', $kodeEmiten)->delete();
        CompanyBond::where('kode_emiten', $kodeEmiten)->delete();
        CompanyBondDetail::where('kode_emiten', $kodeEmiten)->delete();
    }

    /**
     * Import secretaries
     */
    private function importSecretaries(string $kodeEmiten, array $items): void
    {
        foreach ($items as $item) {
            CompanySecretary::create([
                'kode_emiten' => $kodeEmiten,
                'nama' => $item['Nama'] ?? '',
                'telepon' => $item['Telepon'] ?? null,
                'email' => $item['Email'] ?? null,
                'fax' => $item['Fax'] ?? null,
                'hp' => $item['HP'] ?? null,
                'website' => $item['Website'] ?? null,
            ]);
            $this->stats['secretaries']++;
        }
    }

    /**
     * Import directors
     */
    private function importDirectors(string $kodeEmiten, array $items): void
    {
        foreach ($items as $item) {
            CompanyDirector::create([
                'kode_emiten' => $kodeEmiten,
                'nama' => $item['Nama'] ?? '',
                'jabatan' => $item['Jabatan'] ?? '',
                'afiliasi' => (bool) ($item['Afiliasi'] ?? false),
            ]);
            $this->stats['directors']++;
        }
    }

    /**
     * Import commissioners
     */
    private function importCommissioners(string $kodeEmiten, array $items): void
    {
        foreach ($items as $item) {
            CompanyCommissioner::create([
                'kode_emiten' => $kodeEmiten,
                'nama' => $item['Nama'] ?? '',
                'jabatan' => $item['Jabatan'] ?? '',
                'independen' => (bool) ($item['Independen'] ?? false),
            ]);
            $this->stats['commissioners']++;
        }
    }

    /**
     * Import audit committee members
     */
    private function importAuditCommittees(string $kodeEmiten, array $items): void
    {
        foreach ($items as $item) {
            CompanyAuditCommittee::create([
                'kode_emiten' => $kodeEmiten,
                'nama' => $item['Nama'] ?? '',
                'jabatan' => $item['Jabatan'] ?? '',
            ]);
            $this->stats['audit_committees']++;
        }
    }

    /**
     * Import shareholders
     */
    private function importShareholders(string $kodeEmiten, array $items): void
    {
        foreach ($items as $item) {
            CompanyShareholder::create([
                'kode_emiten' => $kodeEmiten,
                'nama' => $item['Nama'] ?? '',
                'kategori' => $item['Kategori'] ?? '',
                'jumlah' => (int) ($item['Jumlah'] ?? 0),
                'persentase' => (float) ($item['Persentase'] ?? 0),
                'pengendali' => (bool) ($item['Pengendali'] ?? false),
            ]);
            $this->stats['shareholders']++;
        }
    }

    /**
     * Import subsidiaries
     */
    private function importSubsidiaries(string $kodeEmiten, array $items): void
    {
        // Remove duplicates based on Nama (subsidiary name)
        $uniqueItems = collect($items)->unique('Nama')->values()->all();

        foreach ($uniqueItems as $item) {
            CompanySubsidiary::create([
                'kode_emiten' => $kodeEmiten,
                'nama' => $item['Nama'] ?? '',
                'bidang_usaha' => $item['BidangUsaha'] ?? null,
                'lokasi' => $item['Lokasi'] ?? null,
                'persentase' => isset($item['Persentase']) ? (float) $item['Persentase'] : null,
                'jumlah_aset' => isset($item['JumlahAset']) ? (float) $item['JumlahAset'] : null,
                'mata_uang' => $item['MataUang'] ?? null,
                'satuan' => $item['Satuan'] ?? null,
                'status_operasi' => $item['StatusOperasi'] ?? null,
                'tahun_komersil' => $item['TahunKomersil'] ?? null,
            ]);
            $this->stats['subsidiaries']++;
        }
    }

    /**
     * Import auditors (KAP)
     */
    private function importAuditors(string $kodeEmiten, array $items): void
    {
        // Remove duplicates and invalid dates
        $uniqueItems = collect($items)->unique(function ($item) {
            return ($item['Nama'] ?? '') . '-' . ($item['TahunBuku'] ?? '');
        })->values()->all();

        foreach ($uniqueItems as $item) {
            $tahunBuku = isset($item['TahunBuku']) && $item['TahunBuku'] > 1900 ? $item['TahunBuku'] : null;

            CompanyAuditor::create([
                'kode_emiten' => $kodeEmiten,
                'nama' => $item['Nama'] ?? '',
                'kap' => $item['KAP1'] ?? null,
                'signing_partner' => $item['SigningPartner'] ?? null,
                'tahun_buku' => $tahunBuku,
                'tanggal_tahun_buku' => $this->parseDate($item['TanggalTahunBuku'] ?? null),
                'akhir_periode' => $this->parseDate($item['AkhirPeriode'] ?? null),
                'tgl_opini' => $this->parseDate($item['TglOpini'] ?? null),
            ]);
            $this->stats['auditors']++;
        }
    }

    /**
     * Import dividends
     */
    private function importDividends(string $kodeEmiten, array $items): void
    {
        foreach ($items as $item) {
            CompanyDividend::create([
                'kode_emiten' => $kodeEmiten,
                'nama' => $item['Nama'] ?? null,
                'jenis' => $item['Jenis'] ?? null,
                'tahun_buku' => $item['TahunBuku'] ?? null,
                'total_saham_bonus' => (int) ($item['TotalSahamBonus'] ?? 0),
                'cash_dividen_per_saham_mu' => $item['CashDividenPerSahamMU'] ?? null,
                'cash_dividen_per_saham' => isset($item['CashDividenPerSaham']) ? (float) $item['CashDividenPerSaham'] : null,
                'cash_dividen_total_mu' => $item['CashDividenTotalMU'] ?? null,
                'cash_dividen_total' => isset($item['CashDividenTotal']) ? (float) $item['CashDividenTotal'] : null,
                'tanggal_cum' => $this->parseDate($item['TanggalCum'] ?? null),
                'tanggal_ex_reguler_dan_negosiasi' => $this->parseDate($item['TanggalExRegulerDanNegosiasi'] ?? null),
                'tanggal_dps' => $this->parseDate($item['TanggalDPS'] ?? null),
                'tanggal_pembayaran' => $this->parseDate($item['TanggalPembayaran'] ?? null),
                'rasio1' => (int) ($item['Rasio1'] ?? 0),
                'rasio2' => (int) ($item['Rasio2'] ?? 0),
            ]);
            $this->stats['dividends']++;
        }
    }

    /**
     * Import bonds
     */
    private function importBonds(string $kodeEmiten, array $items): void
    {
        foreach ($items as $item) {
            CompanyBond::create([
                'source_id' => $item['id'] ?? null,
                'kode_emiten' => $kodeEmiten,
                'nama_emisi' => $item['NamaEmisi'] ?? '',
                'isin_code' => $item['ISINCode'] ?? null,
                'listing_date' => $this->parseDate($item['ListingDate'] ?? null),
                'mature_date' => $this->parseDate($item['MatureDate'] ?? null),
                'rating' => $item['Rating'] ?? null,
                'nominal' => isset($item['Nominal']) ? (float) $item['Nominal'] : null,
                'margin' => $item['Margin'] ?? null,
                'wali_amanat' => $item['WaliAmanat'] ?? null,
            ]);
            $this->stats['bonds']++;
        }
    }

    /**
     * Import bond details
     */
    private function importBondDetails(string $kodeEmiten, array $items): void
    {
        foreach ($items as $item) {
            CompanyBondDetail::create([
                'source_id' => $item['id'] ?? null,
                'kode_emiten' => $kodeEmiten,
                'nama_seri' => $item['NamaSeri'] ?? null,
                'amortisasi_value' => $item['AmortisasiValue'] ?? null,
                'sinking_fund' => $item['SinkingFund'] ?? null,
                'coupon_detail' => $item['CouponDetail'] ?? null,
                'coupon_payment_detail' => $this->parseDate($item['CouponPaymentDetail'] ?? null),
                'mature_date' => $this->parseDate($item['MatureDate'] ?? null),
            ]);
            $this->stats['bond_details']++;
        }
    }

    /**
     * Parse date safely
     */
    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            $date = Carbon::parse($value);
            
            // Skip invalid dates (like 1900-01-01 or 1911-01-01)
            if ($date->year < 1950) {
                return null;
            }

            return $date->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Show import summary
     */
    private function showSummary(): void
    {
        $this->info('Import completed!');
        
        $this->table(
            ['Data Type', 'Count'],
            [
                ['Companies Processed', $this->stats['companies_processed']],
                ['Companies Skipped', $this->stats['companies_skipped']],
                ['Secretaries', $this->stats['secretaries']],
                ['Directors', $this->stats['directors']],
                ['Commissioners', $this->stats['commissioners']],
                ['Audit Committees', $this->stats['audit_committees']],
                ['Shareholders', $this->stats['shareholders']],
                ['Subsidiaries', $this->stats['subsidiaries']],
                ['Auditors (KAP)', $this->stats['auditors']],
                ['Dividends', $this->stats['dividends']],
                ['Bonds', $this->stats['bonds']],
                ['Bond Details', $this->stats['bond_details']],
                ['Errors', count($this->stats['errors'])],
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

