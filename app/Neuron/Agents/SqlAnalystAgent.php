<?php

namespace App\Neuron\Agents;

use App\Models\FinancialRatio;
use App\Models\StockCompany;
use App\Neuron\BaseStockAgent;
use NeuronAI\Agent\SystemPrompt;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;

class SqlAnalystAgent extends BaseStockAgent
{
    protected function instructions(): string
    {
        return (string) new SystemPrompt(
            background: [
                'Kamu adalah analis fundamental untuk aplikasi Bandar Saham.',
                'Kamu bekerja di atas data tabel financial_ratios dan stock_companies di database.',
            ],
            steps: [
                'Pahami pertanyaan user tentang perbandingan rasio (PER, PBV, ROE, DER, NPM, dsb.).',
                'Terjemahkan kebutuhan user menjadi filter terstruktur (sektor, industri, batas rasio, status syariah, periode laporan).',
                'Gunakan tool eksekusi query untuk mengambil sampel data, lalu jelaskan insight-nya dengan bahasa yang mudah dimengerti.',
            ],
            output: [
                'Jawab dalam bahasa Indonesia.',
                'Tampilkan daftar emiten yang relevan dalam bentuk poin singkat berisi kode, nama, dan beberapa rasio kunci.',
                'Akhiri dengan rangkuman pola umum (misal sektor apa yang dominan, profil risiko, valuasi relatif murah/mahal).',
            ],
            toolsUsage: [
                'Selalu gunakan tool schema bila butuh tahu tabel/kolom yang tersedia.',
                'Jika user menyebut sektor/industri namun tidak yakin penamaan persisnya, panggil tool daftar sektor/industri (distinct) terlebih dulu lalu pilih yang paling cocok.',
                'Saat menggunakan tool eksekusi query, isikan properti filter dengan eksplisit dan batasi limit hasil (maksimal 50).',
            ],
        );
    }

    protected function tools(): array
    {
        return [
            Tool::make(
                'get_schema_description',
                'Mengembalikan deskripsi singkat tabel dan kolom yang boleh diakses oleh agent.'
            )->setCallable(function (): array {
                return config('stock_schema.tables', []);
            }),

            Tool::make(
                'list_available_sectors',
                'Mengembalikan daftar sektor yang tersedia (distinct) dari tabel stock_companies.'
            )
                ->addProperty(new ToolProperty(
                    name: 'limit',
                    type: PropertyType::INTEGER,
                    description: 'Jumlah maksimal sektor yang dikembalikan (maksimal 200, default 50).',
                    required: false
                ))
                ->setCallable(function (?int $limit = null): array {
                    $limit = $limit && $limit > 0 ? min($limit, 200) : 50;

                    return StockCompany::query()
                        ->select('sektor')
                        ->whereNotNull('sektor')
                        ->where('sektor', '!=', '')
                        ->distinct()
                        ->orderBy('sektor')
                        ->limit($limit)
                        ->pluck('sektor')
                        ->values()
                        ->all();
                }),

            Tool::make(
                'list_available_industries',
                'Mengembalikan daftar industri yang tersedia (distinct) dari tabel stock_companies.'
            )
                ->addProperty(new ToolProperty(
                    name: 'limit',
                    type: PropertyType::INTEGER,
                    description: 'Jumlah maksimal industri yang dikembalikan (maksimal 200, default 50).',
                    required: false
                ))
                ->setCallable(function (?int $limit = null): array {
                    $limit = $limit && $limit > 0 ? min($limit, 200) : 50;

                    return StockCompany::query()
                        ->select('industri')
                        ->whereNotNull('industri')
                        ->where('industri', '!=', '')
                        ->distinct()
                        ->orderBy('industri')
                        ->limit($limit)
                        ->pluck('industri')
                        ->values()
                        ->all();
                }),

            Tool::make(
                'execute_query_spec',
                'Eksekusi pencarian fundamental terstruktur di tabel financial_ratios dan stock_companies.'
            )
                ->addProperty(new ToolProperty(
                    name: 'sector',
                    type: PropertyType::STRING,
                    description: 'Filter sektor IDX (opsional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'industry',
                    type: PropertyType::STRING,
                    description: 'Filter industri IDX (opsional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'sharia_only',
                    type: PropertyType::BOOLEAN,
                    description: 'Jika true, hanya ambil saham syariah.',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'min_roe',
                    type: PropertyType::NUMBER,
                    description: 'Minimal ROE (dalam persen, opsional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'min_roa',
                    type: PropertyType::NUMBER,
                    description: 'Minimal ROA (dalam persen, opsional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'max_de_ratio',
                    type: PropertyType::NUMBER,
                    description: 'Maksimal Debt to Equity ratio (opsional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'max_per',
                    type: PropertyType::NUMBER,
                    description: 'Maksimal PER (opsional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'max_price_bv',
                    type: PropertyType::NUMBER,
                    description: 'Maksimal PBV (opsional).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'min_fs_year',
                    type: PropertyType::INTEGER,
                    description: 'Tahun laporan minimal (opsional, misalnya 2022).',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'order_by',
                    type: PropertyType::STRING,
                    description: 'Kolom urutan utama: "roe", "per", "price_bv", "de_ratio", atau "npm".',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'order_direction',
                    type: PropertyType::STRING,
                    description: 'Arah urutan: "asc" atau "desc". Default desc.',
                    required: false
                ))
                ->addProperty(new ToolProperty(
                    name: 'limit',
                    type: PropertyType::INTEGER,
                    description: 'Jumlah maksimal baris yang dikembalikan (maksimal 50, default 20).',
                    required: false
                ))
                ->setCallable(function (
                    ?string $sector = null,
                    ?string $industry = null,
                    ?bool $sharia_only = false,
                    ?float $min_roe = null,
                    ?float $min_roa = null,
                    ?float $max_de_ratio = null,
                    ?float $max_per = null,
                    ?float $max_price_bv = null,
                    ?int $min_fs_year = null,
                    ?string $order_by = null,
                    ?string $order_direction = null,
                    ?int $limit = null,
                ): array {
                    $limit = $limit && $limit > 0 ? min($limit, 50) : 20;

                    $query = FinancialRatio::query()
                        ->with('stockCompany')
                        ->audited();

                    if ($sector) {
                        $query->whereHas('stockCompany', function ($q) use ($sector) {
                            $q->where('sektor', $sector);
                        });
                    }

                    if ($industry) {
                        $query->whereHas('stockCompany', function ($q) use ($industry) {
                            $q->where('industri', $industry);
                        });
                    }

                    if ($sharia_only) {
                        $query->sharia();
                    }

                    if ($min_roe !== null) {
                        $query->where('roe', '>=', $min_roe);
                    }

                    if ($min_roa !== null) {
                        $query->where('roa', '>=', $min_roa);
                    }

                    if ($max_de_ratio !== null) {
                        $query->where('de_ratio', '<=', $max_de_ratio);
                    }

                    if ($max_per !== null) {
                        $query->where('per', '<=', $max_per);
                    }

                    if ($max_price_bv !== null) {
                        $query->where('price_bv', '<=', $max_price_bv);
                    }

                    if ($min_fs_year !== null) {
                        $query->whereYear('fs_date', '>=', $min_fs_year);
                    }

                    $allowedOrder = [
                        'roe',
                        'roa',
                        'per',
                        'price_bv',
                        'de_ratio',
                        'npm',
                    ];

                    $order_by = $order_by && in_array($order_by, $allowedOrder, true) ? $order_by : 'roe';
                    $order_direction = strtolower((string) $order_direction) === 'asc' ? 'asc' : 'desc';

                    $query->orderBy($order_by, $order_direction);

                    logger()->info('SqlAnalystAgent execute_query_spec', [
                        'sector' => $sector,
                        'industry' => $industry,
                        'sharia_only' => $sharia_only,
                        'min_roe' => $min_roe,
                        'min_roa' => $min_roa,
                        'max_de_ratio' => $max_de_ratio,
                        'max_per' => $max_per,
                        'max_price_bv' => $max_price_bv,
                        'min_fs_year' => $min_fs_year,
                        'order_by' => $order_by,
                        'order_direction' => $order_direction,
                        'limit' => $limit,
                    ]);

                    $rows = $query->limit($limit)->get();

                    return $rows->map(function (FinancialRatio $row) {
                        /** @var StockCompany|null $company */
                        $company = $row->stockCompany;

                        return [
                            'code' => $row->code,
                            'company_name' => $company?->nama_emiten,
                            'sector' => $company?->sektor ?? $row->sector,
                            'industry' => $company?->industri ?? $row->industry,
                            'fs_date' => optional($row->fs_date)->format('Y-m-d'),
                            'per' => $row->per !== null ? (float) $row->per : null,
                            'price_bv' => $row->price_bv !== null ? (float) $row->price_bv : null,
                            'de_ratio' => $row->de_ratio !== null ? (float) $row->de_ratio : null,
                            'roe' => $row->roe !== null ? (float) $row->roe : null,
                            'roa' => $row->roa !== null ? (float) $row->roa : null,
                            'npm' => $row->npm !== null ? (float) $row->npm : null,
                            'is_sharia' => $row->isSharia(),
                        ];
                    })->all();
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

