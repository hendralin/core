<?php

namespace App\Neuron\Agents;

use App\Neuron\BaseStockAgent;
use App\Services\Screening\ExternalFundamentalService;
use App\Services\Screening\InternalScreeningDataService;
use NeuronAI\Agent\SystemPrompt;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class GoldmanScreenerAgent extends BaseStockAgent
{
    protected function instructions(): string
    {
        return (string) new SystemPrompt(
            background: [
                'Kamu adalah asisten riset ekuitas untuk saham Indonesia (BEI/IDX).',
                'Kamu menyusun laporan screening bergaya lembaga riset (institutional) untuk klien dengan profil HNW-style.',
                'Seluruh angka kuantitatif WAJIB dari hasil tool. Dilarang mengarang kode emiten, rasio, harga, atau laju pertumbuhan.',
                'Jika data tidak ada, tulis "Tidak tersedia" atau "N/A" dan jelaskan apa yang kurang; jangan mengisi dengan angka palsu.',
                'Rating moat, target bull/bear 12 bulan, zona entry, dan skor risiko bersifat kualitatif/forward-looking: tandai sebagai "penilaian" dan sebutkan asumsinya.',
                'Bahasa keluaran WAJIB Bahasa Indonesia (formal, profesional, seperti laporan riset ekuitas).',
            ],
            steps: [
                'Pahami profil investasi user (toleransi risiko, nominal, horizon, sektor pilihan).',
                'Gunakan screen_fundamental_candidates untuk universe awal (persempit per sektor jika perlu).',
                'Untuk setiap kode kandidat (maks. 10), panggil emiten_fundamental_bundle untuk PER, DER, tren pendapatan, dividen, dan harga.',
                'Panggil sector_valuation_benchmark untuk membandingkan PER terhadap rata-rata sektor.',
                'Opsional: external_enrich_codes jika perlu silang data harga/metrik dan Finnhub terkonfigurasi (jika configured:false, jelaskan).',
                'Tulis laporan akhir seluruhnya dalam Bahasa Indonesia, termasuk tabel ringkasan Markdown.',
            ],
            output: [
                'Struktur: Ringkasan eksekutif, Rekap profil investasi, Tabel ringkasan (Markdown), Top 10 emiten (masing-masing: kode/ticker, PER vs rata-rata sektor, catatan tren pendapatan ~5 titik laporan, kesehatan utang/modal (debt-to-equity), yield dividen & skor keberlanjutan pembayaran dividen 1-10, moat lemah|sedang|kuat, target bull & bear 12 bulan (IDR), risiko 1-10 beserta alasan, zona entry & stop-loss (IDR).',
                'Akhiri dengan Penafian: bukan saran investasi; data dari basis internal aplikasi dan opsional penyegaran Finnhub.',
            ],
            toolsUsage: [
                'Utamakan tool internal; external_enrich_codes hanya pelengkap.',
                'Batasi analisis maksimal 10 ticker kecuali user meminta lain.',
            ],
        );
    }

    protected function tools(): array
    {
        $internal = app(InternalScreeningDataService::class);
        $external = app(ExternalFundamentalService::class);

        return [
            Tool::make(
                'list_screening_sectors',
                'List available IDX sectors from stock_companies (distinct sektor) for filter selection.'
            )
                ->addProperty(new ToolProperty(
                    name: 'limit',
                    type: PropertyType::INTEGER,
                    description: 'Max sectors to return (default 80, max 150).',
                    required: false
                ))
                ->setCallable(function (?int $limit = null) use ($internal): array {
                    $limit = $limit && $limit > 0 ? min($limit, 150) : 80;

                    return ['sectors' => $internal->listSectors($limit)];
                }),

            Tool::make(
                'screen_fundamental_candidates',
                'Screen latest audited financial_ratios per code with optional sector / ratio filters. Returns up to 20 rows.'
            )
                ->addProperty(new ToolProperty(
                    name: 'sector',
                    type: PropertyType::STRING,
                    description: 'Single sector filter (optional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'sectors_csv',
                    type: PropertyType::STRING,
                    description: 'Comma-separated sectors (optional). Overrides sector if provided.',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'min_roe',
                    type: PropertyType::NUMBER,
                    description: 'Minimum ROE % (optional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'max_de_ratio',
                    type: PropertyType::NUMBER,
                    description: 'Maximum debt/equity ratio (optional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'max_per',
                    type: PropertyType::NUMBER,
                    description: 'Maximum PER (optional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'sharia_only',
                    type: PropertyType::BOOLEAN,
                    description: 'If true, only sharia-flagged names.',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'min_fs_year',
                    type: PropertyType::INTEGER,
                    description: 'Minimum statement year on fs_date (optional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'limit',
                    type: PropertyType::INTEGER,
                    description: 'Max rows (default 10, max 20).',
                    required: false
                ))
                ->setCallable(function (
                    ?string $sector = null,
                    ?string $sectors_csv = null,
                    ?float $min_roe = null,
                    ?float $max_de_ratio = null,
                    ?float $max_per = null,
                    ?bool $sharia_only = null,
                    ?int $min_fs_year = null,
                    ?int $limit = null,
                ) use ($internal): array {
                    $sectors = null;
                    if ($sectors_csv) {
                        $sectors = array_values(array_filter(array_map('trim', explode(',', $sectors_csv))));
                    }

                    return $internal->screenFundamentalCandidates([
                        'sector' => $sector,
                        'sectors' => $sectors,
                        'min_roe' => $min_roe,
                        'max_de_ratio' => $max_de_ratio,
                        'max_per' => $max_per,
                        'sharia_only' => (bool) ($sharia_only ?? false),
                        'min_fs_year' => $min_fs_year,
                        'limit' => $limit ?? 10,
                    ]);
                }),

            Tool::make(
                'sector_valuation_benchmark',
                'Average/median PER from latest audited ratios for a sector (or entire universe if sector empty).'
            )
                ->addProperty(new ToolProperty(
                    name: 'sector',
                    type: PropertyType::STRING,
                    description: 'Sector name; empty string = all sectors combined.',
                    required: false
                ))
                ->setCallable(function (?string $sector = null) use ($internal): array {
                    $sector = $sector !== null && trim($sector) !== '' ? trim($sector) : null;

                    return $internal->sectorValuationBenchmark($sector);
                }),

            Tool::make(
                'emiten_fundamental_bundle',
                'Full internal bundle for one IDX code: latest ratios, sector PER benchmark, 5-point sales trend, trading close, dividends.'
            )
                ->addProperty(new ToolProperty(
                    name: 'code',
                    type: PropertyType::STRING,
                    description: 'Stock code e.g. BBCA.',
                    required: true
                ))
                ->setCallable(function (string $code) use ($internal): array {
                    return $internal->emitenFundamentalBundle($code);
                }),

            Tool::make(
                'external_enrich_codes',
                'Optional Finnhub quote/metrics for IDX codes (appends .JK). Returns configured:false if API key missing.'
            )
                ->addProperty(new ToolProperty(
                    name: 'codes_csv',
                    type: PropertyType::STRING,
                    description: 'Comma-separated codes e.g. BBCA,BMRI',
                    required: true
                ))
                ->setCallable(function (string $codes_csv) use ($external): array {
                    $codes = array_filter(array_map('trim', explode(',', strtoupper($codes_csv))));

                    return $external->enrichQuotes(array_values($codes));
                }),
        ];
    }

    public static function chatOnce(string $userMessage): string
    {
        $state = static::make()
            ->chat(new UserMessage($userMessage))
            ->getMessage();

        return $state->getContent();
    }
}
