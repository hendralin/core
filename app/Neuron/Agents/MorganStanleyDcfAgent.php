<?php

namespace App\Neuron\Agents;

use App\Neuron\BaseStockAgent;
use App\Services\Valuation\DcfValuationService;
use App\Services\Valuation\ExternalValuationEnrichmentService;
use NeuronAI\Agent\SystemPrompt;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class MorganStanleyDcfAgent extends BaseStockAgent
{
    protected function instructions(): string
    {
        return (string) new SystemPrompt(
            background: [
                'Kamu adalah VP investment banking yang menyusun memo valuasi DCF untuk satu saham IDX (BEI).',
                'Bahasa keluaran WAJIB Bahasa Indonesia, nada formal seperti memo M&A/IB.',
                'Semua angka kuantitatif dalam memo WAJIB bersumber dari hasil tool. Dilarang mengarang harga, laba, atau rasio.',
                'Model internal memakai FCF proxy (bukan FCFF dari laporan arus kas penuh): jelaskan secara eksplisit definisi dan keterbatasannya.',
                'WACC, beta (jika dari Finnhub), dan asumsi terminal harus dirujuk dari output tool atau dinyatakan sebagai asumsi eksplisit.',
            ],
            steps: [
                'Ambil data historis dan konteks emiten dengan get_dcf_history.',
                'Bangun model dengan build_dcf_model (kode emiten; nama perusahaan diisi otomatis dari data fundamental; parameter opsional jika user memberi asumsi).',
                'Jalankan run_dcf_sensitivity untuk tabel sensitivitas fair value.',
                'Opsional: enrich_dcf_market jika perlu data pasar/beta dari Finnhub (hanya jika relevan).',
                'Susun memo: ringkasan eksekutif, asumsi utama, tabel proyeksi 5 tahun, rincian WACC, terminal value (dua metode), perbandingan vs harga pasar, verdict (undervalued/fairly valued/overvalued), risiko asumsi.',
            ],
            output: [
                'Format: memo valuasi dengan heading jelas, tabel Markdown untuk proyeksi dan sensitivitas.',
                'Cantumkan penafian: bukan rekomendasi investasi; model sensitif terhadap asumsi.',
            ],
            toolsUsage: [
                'Selalu panggil get_dcf_history lalu build_dcf_model untuk satu ticker per permintaan.',
            ],
        );
    }

    protected function tools(): array
    {
        $dcf = app(DcfValuationService::class);
        $ext = app(ExternalValuationEnrichmentService::class);

        return [
            Tool::make(
                'get_dcf_history',
                'Historis pendapatan, rasio terkait, snapshot harga internal, dan bundel fundamental untuk satu kode emiten IDX.'
            )
                ->addProperty(new ToolProperty(
                    name: 'code',
                    type: PropertyType::STRING,
                    description: 'Kode emiten, misalnya BBCA.',
                    required: true
                ))
                ->setCallable(function (string $code) use ($dcf): array {
                    return $dcf->getHistoricalForDcf($code);
                }),

            Tool::make(
                'build_dcf_model',
                'Menghitung model DCF proxy lengkap: proyeksi 5 tahun, PV FCF, terminal perpetuity & exit multiple, nilai ekuitas per saham, banding harga pasar.'
            )
                ->addProperty(new ToolProperty(
                    name: 'code',
                    type: PropertyType::STRING,
                    description: 'Kode emiten IDX.',
                    required: true
                ))
                ->addProperty(new ToolProperty(
                    name: 'revenue_growth_annual',
                    type: PropertyType::NUMBER,
                    description: 'Opsional: pertumbuhan pendapatan tahunan dalam persen (override).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'npm_pct',
                    type: PropertyType::NUMBER,
                    description: 'Opsional: NPM rata (persen).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'terminal_growth_pct',
                    type: PropertyType::NUMBER,
                    description: 'Opsional: pertumbuhan terminal (persen).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'beta',
                    type: PropertyType::NUMBER,
                    description: 'Opsional: beta ekuitas.',
                    required: false
                ))
                ->setCallable(function (
                    string $code,
                    ?float $revenue_growth_annual = null,
                    ?float $npm_pct = null,
                    ?float $terminal_growth_pct = null,
                    ?float $beta = null,
                ) use ($dcf): array {
                    $overrides = [];
                    if ($revenue_growth_annual !== null) {
                        $overrides['revenue_growth_annual'] = $revenue_growth_annual;
                    }
                    if ($npm_pct !== null) {
                        $overrides['npm_pct'] = $npm_pct;
                    }
                    if ($terminal_growth_pct !== null) {
                        $overrides['terminal_growth_pct'] = $terminal_growth_pct;
                    }
                    if ($beta !== null) {
                        $overrides['beta'] = $beta;
                    }

                    return $dcf->buildFullDcfModel($code, $overrides);
                }),

            Tool::make(
                'run_dcf_sensitivity',
                'Matriks sensitivitas fair value (midpoint) terhadap pergeseran WACC dan terminal growth.'
            )
                ->addProperty(new ToolProperty(
                    name: 'code',
                    type: PropertyType::STRING,
                    description: 'Kode emiten.',
                    required: true
                ))
                ->addProperty(new ToolProperty(
                    name: 'wacc_shift',
                    type: PropertyType::NUMBER,
                    description: 'Opsional: geser WACC absolut (default 0.01 = 1% poin).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'terminal_shift',
                    type: PropertyType::NUMBER,
                    description: 'Opsional: geser terminal growth absolut (default 0.005).',
                    required: false
                ))
                ->setCallable(function (
                    string $code,
                    ?float $wacc_shift = null,
                    ?float $terminal_shift = null,
                ) use ($dcf): array {
                    return [
                        'sensitivity_rows' => $dcf->runSensitivity($code, [], $wacc_shift, $terminal_shift),
                    ];
                }),

            Tool::make(
                'enrich_dcf_market',
                'Data pasar/beta dari Finnhub (jika API key terset). Simbol IDX otomatis .JK.'
            )
                ->addProperty(new ToolProperty(
                    name: 'code',
                    type: PropertyType::STRING,
                    description: 'Kode emiten.',
                    required: true
                ))
                ->setCallable(function (string $code) use ($ext): array {
                    return $ext->enrichDcf($code);
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
