<?php

namespace App\Neuron\Agents;

use App\Neuron\BaseStockAgent;
use App\Services\Risk\PortfolioRiskAssessmentService;
use NeuronAI\Agent\SystemPrompt;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class BridgewaterRiskAssessmentAgent extends BaseStockAgent
{
    protected function instructions(): string
    {
        return (string) new SystemPrompt(
            background: [
                'Kamu adalah senior risk analyst bergaya Bridgewater Associates: transparansi radikal, bahasa profesional, fokus pada risiko yang dapat dijelaskan dengan data.',
                'Bahasa keluaran WAJIB Bahasa Indonesia.',
                'Semua angka kuantitatif (korelasi, bobot sektor, volatilitas, skor likuiditas, dll.) WAJIB dari hasil tool. Dilarang mengarang angka atau korelasi.',
                'Jika data tidak cukup (mis. overlap harga kurang), nyatakan secara eksplisit dan jangan mengisi angka palsu.',
                'Probabilitas tail risk dan skenario stres bersifat indikatif; selalu sertakan penafian bahwa ini bukan ramalan.',
            ],
            steps: [
                'Parse portofolio user dengan parse_portfolio_input.',
                'Bangun snapshot risiko lengkap dengan build_portfolio_risk_snapshot (isi total nilai portofolio IDR jika user memberi).',
                'Jalankan run_recession_stress_test untuk estimasi drawdown skenario resesi.',
                'Jalankan propose_rebalancing_plan untuk usulan alokasi ulang.',
                'Susun laporan manajemen risiko: ringkasan eksekutif, heat map summary (tabel Markdown dari heat_map_summary), analisis korelasi, konsentrasi sektor, eksposur geografis/mata uang, sensitivitas suku bunga per posisi, stress test, likuiditas, risiko single-name, tail risk, tiga risiko teratas + strategi hedging kualitatif (tanpa angka sembarangan), rebalancing dengan persentase dari tool.',
            ],
            output: [
                'Format: laporan profesional dengan heading, tabel Markdown, dan satu tabel heat map ringkas (kode, bobot%, sektor, likuiditas, sensitivitas suku bunga, skor panas 1-5).',
                'Akhiri dengan penafian: bukan saran investasi; model sensitif terhadap data historis dan asumsi.',
            ],
            toolsUsage: [
                'Selalu mulai dari parse_portfolio_input lalu build_portfolio_risk_snapshot untuk setiap permintaan.',
            ],
        );
    }

    protected function tools(): array
    {
        $risk = app(PortfolioRiskAssessmentService::class);

        return [
            Tool::make(
                'parse_portfolio_input',
                'Parse teks multiline portofolio (satu baris per holding: KODE PERSEN). Mengembalikan posisi ternormalisasi dan peringatan validasi bobot.'
            )
                ->addProperty(new ToolProperty(
                    name: 'portfolio_text',
                    type: PropertyType::STRING,
                    description: 'Teks portofolio, contoh baris: BBCA 25',
                    required: true
                ))
                ->setCallable(function (string $portfolio_text) use ($risk): array {
                    return $risk->parsePortfolioInput($portfolio_text);
                }),

            Tool::make(
                'build_portfolio_risk_snapshot',
                'Snapshot risiko lengkap: korelasi, sektor, geografi/FX (proxy), sensitivitas suku bunga, likuiditas, single-stock risk, tail risk, Finnhub opsional, heat map summary.'
            )
                ->addProperty(new ToolProperty(
                    name: 'positions_json',
                    type: PropertyType::STRING,
                    description: 'JSON array of {code, weight_pct} dari hasil parse yang ok.',
                    required: true
                ))
                ->addProperty(new ToolProperty(
                    name: 'total_portfolio_value_idr',
                    type: PropertyType::NUMBER,
                    description: 'Opsional: total nilai portofolio dalam IDR.',
                    required: false
                ))
                ->setCallable(function (string $positions_json, ?float $total_portfolio_value_idr = null) use ($risk): array {
                    $decoded = json_decode($positions_json, true);
                    if (! is_array($decoded)) {
                        return ['error' => 'positions_json tidak valid.'];
                    }

                    return $risk->buildPortfolioRiskSnapshot($decoded, $total_portfolio_value_idr);
                }),

            Tool::make(
                'run_recession_stress_test',
                'Estimasi drawdown portofolio pada skenario resesi/shock pasar (proxy).'
            )
                ->addProperty(new ToolProperty(
                    name: 'positions_json',
                    type: PropertyType::STRING,
                    description: 'JSON array {code, weight_pct}.',
                    required: true
                ))
                ->addProperty(new ToolProperty(
                    name: 'market_shock_pct',
                    type: PropertyType::NUMBER,
                    description: 'Opsional: shock pasar negatif dalam persen (default -35).',
                    required: false
                ))
                ->setCallable(function (string $positions_json, ?float $market_shock_pct = null) use ($risk): array {
                    $decoded = json_decode($positions_json, true);
                    if (! is_array($decoded)) {
                        return ['error' => 'positions_json tidak valid.'];
                    }

                    return $risk->runRecessionStressTest($decoded, $market_shock_pct ?? -35.0);
                }),

            Tool::make(
                'propose_rebalancing_plan',
                'Usulan rebalancing ke persentase target dengan batas konsentrasi nama/sektor.'
            )
                ->addProperty(new ToolProperty(
                    name: 'positions_json',
                    type: PropertyType::STRING,
                    description: 'JSON array {code, weight_pct}.',
                    required: true
                ))
                ->addProperty(new ToolProperty(
                    name: 'max_single_name_pct',
                    type: PropertyType::NUMBER,
                    description: 'Opsional: batas maks bobot per saham (default 25).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'max_sector_pct',
                    type: PropertyType::NUMBER,
                    description: 'Opsional: batas maks bobot per sektor (default 40).',
                    required: false
                ))
                ->setCallable(function (
                    string $positions_json,
                    ?float $max_single_name_pct = null,
                    ?float $max_sector_pct = null,
                ) use ($risk): array {
                    $decoded = json_decode($positions_json, true);
                    if (! is_array($decoded)) {
                        return ['error' => 'positions_json tidak valid.'];
                    }

                    return $risk->proposeRebalancingPlan(
                        $decoded,
                        $max_single_name_pct ?? 25.0,
                        $max_sector_pct ?? 40.0
                    );
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
